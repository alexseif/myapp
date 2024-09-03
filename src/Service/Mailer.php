<?php

namespace App\Service;

use FOS\UserBundle\Mailer\MailerInterface;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Mailer\MailerInterface as SymfonyMailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class Mailer implements MailerInterface
{

    private $mailer;

    private $router;

    private $twig;

    private $fromEmail;

    public function __construct(
      SymfonyMailerInterface $mailer,
      UrlGeneratorInterface $router,
      Environment $twig,
      string $fromEmail
    ) {
        $this->mailer = $mailer;
        $this->router = $router;
        $this->twig = $twig;
        $this->fromEmail = $fromEmail;
    }

    public function sendConfirmationEmailMessage(UserInterface $user): void
    {
        $url = $this->router->generate(
          'fos_user_registration_confirm',
          ['token' => $user->getConfirmationToken()],
          UrlGeneratorInterface::ABSOLUTE_URL
        );
        $context = ['user' => $user, 'confirmationUrl' => $url];
        $this->sendMessage(
          'emails/registration.html.twig',
          $context,
          $this->fromEmail,
          $user->getEmail()
        );
    }

    public function sendResettingEmailMessage(UserInterface $user): void
    {
        $url = $this->router->generate(
          'fos_user_resetting_reset',
          ['token' => $user->getConfirmationToken()],
          UrlGeneratorInterface::ABSOLUTE_URL
        );
        $context = ['user' => $user, 'confirmationUrl' => $url];
        $this->sendMessage(
          'emails/resetting.html.twig',
          $context,
          $this->fromEmail,
          $user->getEmail()
        );
    }

    protected function sendMessage(
      $templateName,
      $context,
      $fromEmail,
      $toEmail
    ): void {
        $body = $this->twig->render($templateName, $context);
        $email = (new Email())
          ->from($fromEmail)
          ->to($toEmail)
          ->subject('Subject')
          ->html($body);

        $this->mailer->send($email);
    }

}