<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use App\Models\AccessToken;
use App\Models\NotificationLog;
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

        // Eager load uniquement les tâches concernées pour chaque employé
        $employees = Employee::with(['tasks' => function ($query) {
            $query->where('status', '!=', 'done')
                ->where('due_week', now()->format('o-\WW'));
        }])->get();

        // Nettoyage des anciens tokens expirés
        AccessToken::where('expires_at', '<', now())->delete();

        foreach ($employees as $employee) {
            $tasks = $employee->tasks;

            if ($tasks->isEmpty()) {
                continue; // Pas de tâches à notifier
            }

            // Invalider tous les tokens actifs de l’employé avant de créer le nouveau
            AccessToken::where('employee_id', $employee->id)
                ->where('expires_at', '>', now())
                ->delete();

            $token = Str::random(32);

            AccessToken::create([
                'employee_id' => $employee->id,
                'token' => $token,
                'expires_at' => now()->addHours(24), // valide 24h
            ]);

            $link = 'suivi/' . $token;

            $taskList = $this->buildEmployeeTasks($employee, $tasks, $link);

            // Gestion multicanal (WhatsApp, email)
            $channels = $employee->notification_channels ?? ['whatsapp'];
            foreach ($channels as $channel) {
                $logData = [
                    'employee_id' => $employee->id,
                    'channel' => $channel,
                    'sent_at' => now(),
                    'payload' => null,
                    'status' => 'success',
                    'error_message' => null,
                ];

                try {
                    if ($channel === 'whatsapp') {
                        $payload = $this->getWhatsAppPayload($employee, $taskList, $link, count($tasks));
                        $logData['payload'] = $payload;
                        $response = $this->sendWhatsAppMessage($employee->phone, $payload);
                        if (!$response->successful()) {
                            $logData['status'] = 'failed';
                            $logData['error_message'] = $response->body();
                        }
                    } elseif ($channel === 'email') {
                        try {
                            $employee->notify(new TaskReminderNotification($employee, $tasks, url($link)));
                            $logData['payload'] = [
                                'subject' => "Suivi quotidien de vos tâches - " . now()->format('d/m/Y'),
                                'message' => $this->buildEmployeeMessage($employee, $tasks, url($link)),
                            ];
                            // Vérifier si la notification a échoué via le système Laravel
                            if (method_exists($employee->routeNotificationFor('mail'), 'failures')) {
                                $failures = $employee->routeNotificationFor('mail')->failures();
                                if (!empty($failures)) {
                                    $logData['status'] = 'failed';
                                    $logData['error_message'] = implode(', ', $failures);
                                }
                            }
                        } catch (\Exception $e) {
                            $logData['status'] = 'failed';
                            $logData['error_message'] = $e->getMessage();
                        }
                        
                    }
                } catch (\Exception $e) {
                    $logData['status'] = 'failed';
                    $logData['error_message'] = $e->getMessage();
                }
                NotificationLog::create($logData);
            }

            $this->info("Lien envoyé à {$employee->name} : $link");
        }
    }

    private function buildEmployeeMessage($employee, $tasks, $link)
    {
        $taskList = '';
        foreach ($tasks as $task) {
            $taskList .= $task->title . "\n"; // Ajoute chaque tâche avec un saut de ligne
        }

        $message = "Bonjour {$employee->name},\n\n";
        $message .= "Le système a détecté que vous avez " . count($tasks) . " tâche(s) non réalisée(s) cette semaine :\n\n";
        $message .= $taskList;
        $message .= "Merci de mettre à jour le statut de ces tâches en cliquant sur le bouton ci-dessous.\n";
        $message .= $link;

        return $message;

       
    }

    protected function getWhatsAppPayload($employee, $taskList, $link, $taskCount)
    {
        return [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $this->formatPhoneNumber($employee->phone),
            'type' => 'template',
            'template' => [
                'name' => 'task_reminder', // Nom du template WhatsApp approuvé
                'language' => ['code' => 'fr'],
                'components' => [
                    [
                        'type' => 'body',
                        'parameters' => [
                            ['type' => 'text', 'text' => $employee->name],
                            ['type' => 'text', 'text' => $taskCount],
                            ['type' => 'text', 'text' => $taskList],
                        ]
                    ],
                    [
                        'type' => 'button',
                        'sub_type' => 'url',
                        'index' => 0,
                        'parameters' => [
                            ['type' => 'text', 'text' => $link]
                        ]
                    ]
                ]
            ]
        ];
    }

     protected function sendWhatsAppMessage($to, $payload)
    {
        return Http::withToken(env('WHATSAPP_TOKEN'))
            ->post("https://graph.facebook.com/v23.0/" . env('WHATSAPP_PHONE_NUMBER_ID') . "/messages", $payload);
    }

    private function buildEmployeeTasks($employee, $tasks, $link)
    {
       

        $taskList = '';
        foreach ($tasks as $index => $task) {
            $taskList .= $task->name . ' (' . $task->progress . '%)';
            if ($index < count($tasks) - 1) {
                $taskList .= ' | '; // Séparateur entre les tâches
            }
        }

        return $taskList;
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
