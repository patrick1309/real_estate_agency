<?php

namespace App\Notification;

use App\Entity\Contact;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class ContactNotification
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function notify(Contact $contact)
    {
        $email = (new TemplatedEmail())
            ->from('hello@example.com')
            ->to('you@example.com')
            ->subject('Nouveau message pour le bien ' . $contact->getProperty()->getName())
            ->htmlTemplate('emails/contact.html.twig')
            // pass variables (name => value) to the template
            ->context([
                'contact' => $contact
            ]);

        $this->mailer->send($email);
    }
}
