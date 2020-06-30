<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class MailForApplicant extends Notification
{
    use Queueable;

    /**
        * undocumented class variable
        *
        * @var string
        **/
        public $new_user;

    /**
        * Create a new notification instance.
        *
        * @return void
        */
        public function __construct($new_user)
        {
            $this->user = $new_user;
        }

    /**
        * Get the notification's delivery channels.
        *
        * @param  mixed  $notifiable
        * @return array
        */
        public function via($notifiable)
        {
            return ['mail'];
        }

    /**
        * Get the mail representation of the notification.
        * 
        * @param  mixed  $notifiable
        * @return \Illuminate\Notifications\Messages\MailMessage
        */
        public function toMail($notifiable)
        {   
            return (new MailMessage)
                ->from('seadclark@gmail.com', 'Admin')
                ->subject('Registration Successful.')
                ->line('Thank you for registration! You will be notified if you are approved. Stay tuned for updates!');
                //->markdown('emails.notification', ['user' => $this->new_user]); //for customizing email template
        }

    /**
        * Get the array representation of the notification.
        *
        * @param  mixed  $notifiable
        * @return array
        */
        public function toArray($notifiable)
        {
            return [
                //
            ];
        }

}
