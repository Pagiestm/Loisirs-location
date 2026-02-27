# Utilisation du MailService

Le `MailService` est un service générique pour envoyer des emails dans l'application.

## Configuration

Les paramètres par défaut sont définis dans le fichier `.env` :

```dotenv
MAILER_DSN=smtp://localhost:1025
MAIL_FROM_ADDRESS=noreply@loisirs-location.fr
MAIL_FROM_NAME="Loisirs Location"
```

## Envoi d'un email avec template

### Exemple simple

```php
use App\Service\MailService;
use Symfony\Component\Mime\Address;

class MonController
{
    public function __construct(
        private readonly MailService $mailService
    ) {
    }

    public function envoyerEmail(): void
    {
        $this->mailService->sendTemplatedEmail(
            to: 'destinataire@example.com',
            subject: 'Bienvenue !',
            template: 'emails/example.html.twig',
            context: [
                'title' => 'Bienvenue sur notre plateforme',
                'message' => 'Merci de vous être inscrit.',
                'actionUrl' => 'https://example.com/confirmer',
                'actionText' => 'Confirmer mon email'
            ]
        );
    }
}
```

### Exemple avec options avancées

```php
use Symfony\Component\Mime\Address;

$this->mailService->sendTemplatedEmail(
    to: new Address('destinataire@example.com', 'Nom du destinataire'),
    subject: 'Confirmation de réservation',
    template: 'emails/reservation_confirmed.html.twig',
    context: [
        'reservation' => $reservation,
        'customer' => $customer
    ],
    from: new Address('contact@loisirs-location.fr', 'Service Client'),
    replyTo: 'contact@loisirs-location.fr',
    cc: ['manager@loisirs-location.fr'],
    bcc: ['archive@loisirs-location.fr']
);
```

## Envoi d'un email simple (sans template)

```php
$this->mailService->sendEmail(
    to: 'destinataire@example.com',
    subject: 'Notification simple',
    htmlContent: '<p>Contenu HTML de l\'email</p>',
    textContent: 'Contenu texte brut',
    from: 'noreply@loisirs-location.fr',
    replyTo: 'contact@loisirs-location.fr'
);
```

## Envoi asynchrone

Grâce à la configuration de Messenger, les emails sont envoyés de manière asynchrone automatiquement.
Les messages sont stockés dans la base de données (transport Doctrine) et traités par les workers.

### Lancer un worker pour traiter les emails

```bash
php bin/console messenger:consume async -vv
```

### Vérifier les emails avec Mailpit

Mailpit est configuré sur le port 8025. Accédez à l'interface web :

```
http://localhost:8025
```

## Créer un template d'email personnalisé

1. Créez un fichier dans `templates/emails/` (ex: `welcome.html.twig`)
2. Étendez le template de base ou créez votre propre design :

```twig
{% extends '@email/default/notification/body.html.twig' %}

{% block content %}
    <h1>{{ greeting }}</h1>
    <p>{{ message }}</p>

    {% if button_url %}
        <a href="{{ button_url }}" class="button">
            {{ button_text }}
        </a>
    {% endif %}
{% endblock %}
```

3. Utilisez-le dans votre code :

```php
$this->mailService->sendTemplatedEmail(
    to: $user->getEmail(),
    subject: 'Bienvenue !',
    template: 'emails/welcome.html.twig',
    context: [
        'greeting' => 'Bonjour ' . $user->getName(),
        'message' => 'Merci de vous être inscrit...',
        'button_url' => $this->generateUrl('verify_email', ['token' => $token]),
        'button_text' => 'Vérifier mon email'
    ]
);
```

## Gestion des erreurs

Le service peut lever une `TransportExceptionInterface` en cas d'erreur d'envoi.
Il est recommandé de gérer cette exception :

```php
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

try {
    $this->mailService->sendTemplatedEmail(
        to: 'destinataire@example.com',
        subject: 'Test',
        template: 'emails/test.html.twig',
        context: []
    );
} catch (TransportExceptionInterface $e) {
    // Gérer l'erreur
    $this->logger->error('Erreur d\'envoi d\'email', [
        'error' => $e->getMessage()
    ]);
}
```
