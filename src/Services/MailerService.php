<?php

namespace App\Services;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

Class MailerService 
{   
    private $replyTo;
    public function __construct(private MailerInterface $mailer, $replyTo) {
        $this->replyTo = $replyTo;
    }
    
    
    public function sendEmail(
        $to = 'christophe.gpa@gmail.com',
        $content = '<p>See Twig integration for better HTML integration!</p>',
        $subject = 'Time for Symfony Mailer!'
        ): Void
    {
        $email = (new Email())
            ->from('christophe.test.dev@gmail.com')
            ->to($to)
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            ->replyTo($this->replyTo)
            //->priority(Email::PRIORITY_HIGH)
            ->subject($subject)
            ->text('Sending emails is fun again!')
            ->html($content);

        $this->mailer->send($email);
        

        // ...
    }
}