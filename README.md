# Loisirs Location – Plateforme de Location de Vans Aménagés

## Présentation du projet
Ce projet est une plateforme web dédiée à la location de vans aménagés pour une entreprise spécialisée. L’objectif est de faciliter la gestion des réservations, du catalogue de vans, des utilisateurs et des paiements, tout en offrant une expérience fluide aux clients et à l’équipe de gestion.

## Objectifs principaux
- Digitaliser la gestion des locations et des réservations de vans aménagés
- Permettre aux clients de consulter le catalogue de vans et de réserver en ligne
- Offrir un espace d’administration pour gérer les vans, les réservations et les utilisateurs
- Améliorer la visibilité de l’entreprise

## Fonctionnalités principales
- Consultation du catalogue de vans aménagés
- Recherche et filtrage par modèle, capacité, équipements, disponibilité, etc.
- Réservation en ligne avec calendrier de disponibilité
- Gestion des comptes clients (inscription, connexion, historique de réservations)
- Espace administrateur :
   - Gestion du catalogue de vans (ajout, modification, suppression)
   - Gestion des réservations
   - Gestion des utilisateurs
- Notifications par email (confirmation, rappel, etc.)

## Utilisateurs
- **Clients** : consultation du catalogue de vans, réservation, gestion de compte
- **Administrateurs** : gestion du catalogue de vans, des réservations, des utilisateurs

## Technologies utilisées
- **Symfony (PHP 8.3)** : framework principal
- **PostgreSQL** : base de données relationnelle
- **PgAdmin** : interface d’administration de la base de données
- **Docker Compose** : orchestration des services (web, bdd, admin)
- **Twig** : moteur de templates pour le front-end
- **HTML/CSS/JS** : interface utilisateur

## Structure du projet
- `src/` : code source Symfony (contrôleurs, entités, repositories)
- `templates/` : vues Twig
- `public/` : point d’entrée web
- `config/` : configuration Symfony
- `assets/` : fichiers front-end (JS, CSS)
- `docker/` : fichiers spécifiques Docker (si besoin)

## Pages principales
- Accueil
- Catalogue (liste et détail des vans aménagés)
- Réservation (formulaire, calendrier de disponibilité)
- Espace client (profil, historique de réservations)
- Espace administrateur (gestion du catalogue de vans, réservations, utilisateurs)
- Connexion/Inscription
- Contact

## Contraintes et recommandations
- Sécurité : ne jamais exposer les identifiants sensibles (utiliser `.env`)
- Respect du RGPD pour la gestion des données utilisateurs
- Interface responsive (adaptée mobile/tablette)
- Documentation claire pour l’installation et l’utilisation
- Préciser les conditions de location (âge, permis, durée minimale, etc.)

## Livrables attendus
- Code source complet (Symfony, Docker, etc.)
- Fichiers de configuration (`.env.example`, `compose.yaml`)
- Documentation d’installation et d’utilisation (ce README)
- Procédure de connexion à PgAdmin (`PGADMIN-CONNEXION.md`)

## Installation et lancement
1. **Cloner le dépôt**
2. **Configurer les variables d’environnement**
   - Copier `.env.example` en `.env` et adapter les valeurs
3. **Lancer les services Docker**
   ```sh
   docker compose up -d
   ```
4. **Accéder à l’application**
   - Application web : http://localhost:8000
   - PgAdmin : http://localhost:5050 ([voir la procédure de connexion à PgAdmin](docs/PGADMIN-CONNEXION.md))

## Accès administrateur
- Les accès administrateur sont à configurer dans la base de données ou via la console Symfony.

## Contact
Pour toute question, contactez l’équipe projet ou consultez la documentation fournie.
