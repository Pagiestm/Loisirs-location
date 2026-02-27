<?php

namespace App\Service;

use NotFloran\MjmlBundle\Renderer\RendererInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\BodyRendererInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class MailService
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly BodyRendererInterface $bodyRenderer,
        private readonly RendererInterface $mjml,
        private readonly Environment $twig,
        private readonly string $defaultFromEmail,
        private readonly string $defaultFromName
    ) {}

    /**
     * Envoie un email avec un template Twig
     *
     * @param string|Address $to Destinataire
     * @param string $subject Sujet de l'email
     * @param string $template Chemin du template Twig (ex: 'email/welcome.html.twig')
     * @param array<string, mixed> $context Variables à passer au template
     * @param string|Address|null $from Expéditeur (optionnel, utilise la valeur par défaut si non fourni)
     * @param string|null $replyTo Email de réponse (optionnel)
     * @param array<string|Address> $cc Destinataires en copie (optionnel)
     * @param array<string|Address> $bcc Destinataires en copie cachée (optionnel)
     *
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function sendTemplatedEmail(
        string|Address $to,
        string $subject,
        string $template,
        array $context = [],
        string|Address|null $from = null,
        ?string $replyTo = null,
        array $cc = [],
        array $bcc = []
    ): void {

        $email = (new TemplatedEmail())
            ->from($from ?? $this->defaultFromEmail)
            ->to($to)
            ->subject($subject)
            ->htmlTemplate($template)
            ->context($context);

        if ($replyTo) {
            $email->replyTo($replyTo);
        }

        foreach ($cc as $ccAddress) {
            $email->addCc($ccAddress);
        }

        foreach ($bcc as $bccAddress) {
            $email->addBcc($bccAddress);
        }

        $this->bodyRenderer->render($email);
        $this->mailer->send($email);
    }

    /**
     * Envoie un email simple (sans template)
     *
     * @param string|Address $to Destinataire
     * @param string $subject Sujet de l'email
     * @param string $htmlContent Contenu HTML de l'email
     * @param string|null $textContent Contenu texte brut (optionnel)
     * @param string|Address|null $from Expéditeur (optionnel)
     * @param string|null $replyTo Email de réponse (optionnel)
     *
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function sendEmail(
        string|Address $to,
        string $subject,
        string $htmlContent,
        ?string $textContent = null,
        string|Address|null $from = null,
        ?string $replyTo = null
    ): void {
        $email = (new Email())
            ->from($from ?? new Address($this->defaultFromEmail, $this->defaultFromName))
            ->to($to)
            ->subject($subject)
            ->html($htmlContent);

        if ($textContent) {
            $email->text($textContent);
        }

        if ($replyTo) {
            $email->replyTo($replyTo);
        }

        $this->mailer->send($email);
    }

    /**
     * Envoie un email avec un template MJML
     *
     * @param string|Address $to Destinataire
     * @param string $subject Sujet de l'email
     * @param string $mjmlTemplate Chemin du template MJML (ex: 'email/welcome.mjml.twig')
     * @param array<string, mixed> $context Variables à passer au template
     * @param string|Address|null $from Expéditeur (optionnel, utilise la valeur par défaut si non fourni)
     * @param string|null $replyTo Email de réponse (optionnel)
     * @param array<string|Address> $cc Destinataires en copie (optionnel)
     * @param array<string|Address> $bcc Destinataires en copie cachée (optionnel)
     *
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     * @throws \Twig\Error\Error
     */
    public function sendMjmlEmail(
        string|Address $to,
        string $subject,
        string $mjmlTemplate,
        array $context = [],
        string|Address|null $from = null,
        ?string $replyTo = null,
        array $cc = [],
        array $bcc = []
    ): void {
        // Rendre le template MJML avec Twig
        $mjmlBody = $this->twig->render($mjmlTemplate, $context);

        // Convertir le MJML en HTML
        $htmlBody = $this->mjml->render($mjmlBody);

        // Créer et configurer l'email
        $email = (new Email())
            ->from($from ?? new Address($this->defaultFromEmail, $this->defaultFromName))
            ->to($to)
            ->subject($subject)
            ->html($htmlBody);

        if ($replyTo) {
            $email->replyTo($replyTo);
        }

        foreach ($cc as $ccAddress) {
            $email->addCc($ccAddress);
        }

        foreach ($bcc as $bccAddress) {
            $email->addBcc($bccAddress);
        }

        $this->mailer->send($email);
    }
}
