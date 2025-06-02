<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use App\Models\AccessToken;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Http;
use App\Notifications\TaskReminderNotification;

class SendTaskFollowUpLinks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:task-follow-up-links';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envoie les liens de suivi des tâches aux employés chaque matin';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $today = now();

        // Sauter le dimanche
        if ($today->isSunday()) {
            $this->info('Dimanche — pas de token généré.');
            return;
        }

        $employees = Employee::all();

        //delete old tokens
        AccessToken::where('expires_at', '<', now())->delete();

        foreach ($employees as $employee) {



            $tasks = $employee->tasks()
                ->where('status', '!=', 'done')
                ->where('due_week', now()->format('o-\WW')) // Format: 2025-W22
                ->get();



            if ($tasks->isEmpty()) {
                continue; // Pas de tâches à notifier
            }


            $token = Str::random(32);

            AccessToken::create([
                'employee_id' => $employee->id,
                'token' => $token,
                'expires_at' => now()->addHours(24), // valide 24h
            ]);

            $link = 'suivi/' . $token;



            $taskList = $this->buildEmployeeMessage($employee, $tasks, $link);

            $this->sendWhatsAppMessage($employee->phone, $taskList, $link, count($tasks), $employee->name);

            //$employee->notify(new TaskReminderNotification($employee, $tasks, $link));

            // TODO : envoyer le lien via WhatsApp ici
            $this->info("Lien envoyé à {$employee->name} : $link");
        }
    }

    private function buildEmployeeMessage($employee, $tasks, $link)
    {
        /* $taskList = '';
        foreach ($tasks as $task) {
            $taskList .= $task->title . "\n"; // Ajoute chaque tâche avec un saut de ligne
        }

        $message = "Bonjour {$employee->name},\n\n";
        $message .= "Le système a détecté que vous avez " . count($tasks) . " tâche(s) non réalisée(s) cette semaine :\n\n";
        $message .= $taskList;
        $message .= "Merci de mettre à jour le statut de ces tâches en cliquant sur le bouton ci-dessous.\n";
        $message .= $link;

        return $message; */

        $taskList = '';
        foreach ($tasks as $index => $task) {
            $taskList .= $task->name. ' (' . $task->progress . '%)'; 
            if ($index < count($tasks) - 1) {
                $taskList .= ' | '; // Séparateur entre les tâches
            }
        }

        return $taskList;
    }

    protected function sendWhatsAppMessage($to, $taskList, $link, $taskCount, $employeeName )
    {

        $to = $this->formatPhoneNumber($to);

   

        /* $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $to,
            'type' => 'text',
            'text' => [
                'preview_url' => true, // Activez pour prévisualiser les liens
                'body' => $message
            ]
        ]; */

        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $to,
            'type' => 'template',
            'template' => [
                'name' => 'task_reminder', // Nom du modèle approuvé
                'language' => ['code' => 'fr'],
                'components' => [
                    [
                        'type' => 'body',
                        'parameters' => [
                            ['type' => 'text', 'text' => $employeeName], // {{1}} : Nom
                            ['type' => 'text', 'text' => $taskCount],   // {{2}} : Nombre de tâches
                            ['type' => 'text', 'text' => $taskList],    // {{3}} : Liste des tâches
                        ]
                    ],
                    [
                        'type' => 'button',
                        'sub_type' => 'url',
                        'index' => 0,
                        'parameters' => [
                            ['type' => 'text', 'text' => $link] // {{4}} : Lien du bouton
                        ]
                    ]
                ]
            ]
        ];

        $response = Http::withToken(env('WHATSAPP_TOKEN'))
            ->post("https://graph.facebook.com/v23.0/" . env('WHATSAPP_PHONE_NUMBER_ID') . "/messages", $payload);

        if ($response->successful()) {
            $this->info("Message envoyé à $to");
        } else {
            // Loguer les détails de l'erreur
            \Log::error("Échec de l'envoi à $to : " . $response->body());
            $this->error("Échec de l'envoi à $to : " . $response->status() . " - " . $response->body());
        }
    }

    private function formatPhoneNumber($phone)
    {
        // Supprimer les espaces, tirets, ou autres caractères
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        // Ajouter le "+" si absent
        if (!str_starts_with($phone, '+')) {
            $phone = '+' . $phone;
        }
        return $phone;
    }
}
