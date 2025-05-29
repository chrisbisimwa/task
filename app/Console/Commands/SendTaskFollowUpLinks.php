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

            $link = url('/suivi/' . $token);

            //$message = $this->buildEmployeeMessage($employee, $tasks, $link);

            //$this->sendWhatsAppMessage($employee->phone, $message);

            $employee->notify(new TaskReminderNotification($employee, $tasks, $link));

            // TODO : envoyer le lien via WhatsApp ici
            $this->info("Lien envoyé à {$employee->name} : $link");
        }
    }

    private function buildEmployeeMessage($employee, $tasks, $link)
    {
        $taskList = '';
        foreach ($tasks as $task) {
            $taskList .= "- {$task->title}\n";
        }

        $message = "Bonjour {$employee->name},\n\n";
        $message .= "Le système reporte que vous avez " . count($tasks) . " tâche(s) non réalisée(s) cette semaine :\n\n";
        $message .= $taskList . "\n";
        $message .= "Merci de bien vouloir mettre à jour le statut de ces tâches via ce lien :\n";
        $message .= $link;

        return $message;
    }

    protected function sendWhatsAppMessage($to, $message)
    {
        $response = Http::withToken(env('WHATSAPP_TOKEN'))->post("https://graph.facebook.com/v19.0/" . env('WHATSAPP_PHONE_NUMBER_ID') . "/messages", [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'text',
            'text' => [
                'preview_url' => false,
                'body' => $message
            ]
        ]);

        if ($response->successful()) {
            $this->info("Message envoyé à $to");
        } else {
            $this->error("Échec de l'envoi à $to : " . $response->body());
        }
    }
}
