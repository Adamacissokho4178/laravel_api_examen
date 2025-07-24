# Portail Scolaire - Backend Laravel

## Installation

```bash
composer install
cp .env.example .env
php artisan key:generate
```

## Configuration
- Configure la base de données dans `.env`
- Lance les migrations :

```bash
php artisan migrate
```

## Lancement du serveur

```bash
php artisan serve
```

L'API sera accessible sur http://localhost:8000

## Fonctionnalités principales
- Authentification (Sanctum)
- Gestion des rôles (admin, enseignant, élève/parent)
- API REST pour l'inscription et la connexion
