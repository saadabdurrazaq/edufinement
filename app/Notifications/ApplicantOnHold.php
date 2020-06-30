<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\HtmlString;

class ApplicantOnHold extends Notification
{
    use Queueable;

    /**
        * undocumented class variable
        *
        * @var string
        **/
        public $user;

    /**
        * Create a new notification instance.
        *
        * @return void
        */
        public function __construct($user)
        {
            $this->user = $user;
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
                ->subject('Technical Problem.')
                ->line(
                    new HtmlString('You may have previously received an email from us, but we should inform you that it was a technical problem from our system.
                    <br><br>
                    Because of this, we havent announced you anything until you received a next email from us. Stay tuned, stay updates!
                    <br><br>
                    we are sorry for your inconvenience.'));
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
