<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskReminderNotification extends Notification
{
    use Queueable;
    public $employee;
    public $tasks;
    public $link;


    /**
     * Create a new notification instance.
     */
    public function __construct($employee, $tasks, $link)
    {
        $this->employee = $employee;
        $this->tasks = $tasks;
        $this->link = $link;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $date = now()->format('d/m/Y');

        $message = (new MailMessage)
            ->subject("Suivi quotidien de vos tâches  - {$date}")
            ->greeting("Bonjour {$this->employee->name},")
            ->line("Le système a détecté que vous avez " . count($this->tasks) . " tâche(s) non réalisée(s) cette semaine :");

        foreach ($this->tasks as $task) {
            $message->line("- {$task->name} ({$task->progress}%)");
        }

        return $message
            ->line('Merci de mettre à jour le statut de ces tâches en cliquant sur le bouton ci-dessous.')
            ->action('Mettre à jour mes tâches', $this->link)
            ->line('Bonne journée de travail !');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
