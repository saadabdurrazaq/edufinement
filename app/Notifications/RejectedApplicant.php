<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\HtmlString;

class RejectedApplicant extends Notification
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
                ->subject('Account Rejected.')
                ->line(
                    new HtmlString('We appreciate that you took the time to apply for the position of student with our school. We received applications from many people. After reviewing your submitted application materials, we have decided that we will not offer you an interview.
                    <br><br>
                    We appricate that you are intersted in our school. Please do apply again in the future. Again, thank you for applying. We wish you all the best.'));
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
