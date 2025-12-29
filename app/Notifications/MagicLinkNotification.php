<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MagicLinkNotification extends Notification
{
    /**
     * Create a new notification instance.
     */
    public function __construct(public string $loginUrl) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ["mail"];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return new MailMessage()
            ->subject("Your Magic Login Link")
            ->greeting("Hello!")
            ->line("Click the button below to log in to your account.")
            ->line("This link will expire in 15 minutes.")
            ->action("Log In", $this->loginUrl)
            ->line(
                "If you did not request this login link, you can safely ignore this email.",
            );
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
