<?php

namespace App\Mailer;

use App\Entity\User;

class BucketMailer
{
    private $mailer;
    private $twig;

    public function __construct(\Swift_Mailer $mailer, \Twig\Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function sendRegistrationMail(User $user)
    {
        //envoi de l'email
        $message = new \Swift_Message('Bienvenue sur Bucket-List !');
        $message->setFrom('info@bucketlist.com');
        $message->addTo( $user->getEmail() );
        $message->addReplyTo('info@bucketlist.com');

        //contenu du message, vient de mes fichiers twig
        $html = $this->twig->render('emails/welcome.html.twig');
        $message->setBody($html, 'text/html');

        $this->mailer->send($message);
    }
}