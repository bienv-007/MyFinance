# MyFinance

Application web de gestion de finances personnelles : suivi des revenus et dépenses, enveloppes budgétaires et planification financière grâce aux prévisions de revenus et de dépenses. Interface entièrement en français.

## Aperçu

MyFinance permet de centraliser la gestion de ses finances au quotidien :

- enregistrer et suivre ses **revenus** et **dépenses** (classées par catégories) ;
- définir des **budgets** sur des périodes données et suivre leur état automatiquement ;
- **planifier** ses entrées et sorties d'argent via des prévisions, puis les concrétiser en un clic ;
- consulter des **statistiques** synthétiques pour anticiper ses besoins.

L'application expose deux expériences complémentaires : un **tableau de bord interactif** (SPA Vue 3) et des **vues détaillées** rendues côté serveur (Blade).

## Fonctionnalités

### Authentification
- Inscription et connexion par email / mot de passe (mots de passe hachés).
- Sessions sécurisées avec régénération à la connexion.
- Déconnexion et contrôle d'accès par utilisateur : chaque utilisateur ne voit et ne manipule que ses propres données.

### Tableau de bord
- Vue d'ensemble : nombre de catégories, revenus, dépenses, budgets et budgets actifs.
- Accès rapide aux modules et recherche temps réel.

### Catégories
- Création, modification et suppression de catégories.
- Recherche.
- Suppression bloquée si la catégorie est utilisée par des dépenses.

### Revenus
- Enregistrement d'un revenu (source, montant, date, description).
- Modification, suppression, recherche et tri.

### Dépenses
- Enregistrement d'une dépense rattachée à une catégorie (montant, date, description).
- Modification, suppression, recherche par catégorie/description et tri.

### Budgets
- Création d'enveloppes budgétaires (période, montant total, dates de début et de fin).
- Statut calculé automatiquement : **À venir**, **En cours** ou **Terminé**.
- Recherche, tri, statistiques (total, budgets actifs, montant budgété).

### Prévisions de revenus
- Planification des entrées futures (source, montant, date, description).
- Action **« Marquer comme perçu »** : la prévision devient un revenu réel, sans être supprimée (statut « Réalisée »).
- Statuts automatiques : **Réalisée**, **À venir**, **Expirée**, **Aujourd'hui**.
- Filtres avancés : recherche, source, date, mois, montant min/max, tri.
- Statistiques : montant total, montant du mois et de l'année, revenus attendus, expirés, prochain revenu, source principale, prévision la plus élevée.

### Prévisions de dépenses
- Planification des sorties futures (catégorie, montant, date, description).
- Action **« Valider »** : la prévision est convertie en dépense réelle (transaction atomique) puis supprimée.
- Statuts automatiques : **À venir**, **Dépassée**, **Aujourd'hui**.
- Filtres avancés : recherche, catégorie, date, mois, montant min/max, tri.
- Statistiques : montant total, prévisions en attente et dépassées, prochaine dépense, catégorie la plus fréquente.

### API REST
- Endpoints JSON complets pour tous les modules, protégés par session (`auth:web`).
- Authentification : `/api/auth/register`, `/api/auth/login`, `/api/auth/me`, `/api/auth/logout`.
- Ressources : catégories, revenus, dépenses, budgets, prévisions de revenus et de dépenses.
- Pagination (10 éléments), filtres, recherche, tri et statistiques intégrées.
- Détection des erreurs et validation des formulaires (codes 422, 403, 404).

## Architecture

- **Tableau de bord SPA** : application monopage construite en Vue 3 (chargée via CDN), communicant avec l'API via Axios (sessions + CSRF), avec Toastr pour les notifications et SweetAlert2 pour les confirmations.
- **Vues détaillées** : pages serveur Blade (index, création, édition, consultation) avec composants Blade réutilisables (cartes, formulaires, badges de statut).
- **Couche métier** : contrôleurs web + contrôleurs API partageant les mêmes `Form Requests` de validation et `API Resources` de sérialisation.
- **Services & Actions** : `RevenuPrevisionService` pour les filtres et statistiques ; `MarkRevenuPrevisionAsReceived` et `ConvertDepensePrevisionToDepense` pour les conversions transactionnelles (via `DB::transaction`).
- **Sécurité des données** : vérification de propriété sur chaque ressource (403/404), relation Eloquent scopées à l'utilisateur authentifié.

## Technologies utilisées

### Backend
| Technologie | Usage |
| --- | --- |
| PHP 8.3 | Langage serveur |
| Laravel 13 | Framework (routing, Eloquent, migrations, sessions, validation) |
| Eloquent ORM | Modélisation et relations entre entités |
| Laravel Sanctum | Gestion des sessions / jetons d'API |
| MySQL | Base de données relationnelle |
| Laravel Tinker | Console REPL de développement |
| Laravel Pint | Formatage du code |
| Laravel Pail | Visualisation des logs en temps réel |

### Frontend
| Technologie | Usage |
| --- | --- |
| Vue 3 | Tableau de bord (SPA, chargé via CDN) |
| Axios | Appels API |
| Tailwind CSS 4 | Styles de l'interface |
| Blade | Vues détaillées rendues côté serveur |
| Font Awesome | Icônes |
| Toastr / SweetAlert2 | Notifications et confirmations |

### Outils de développement
| Technologie | Usage |
| --- | --- |
| Vite | Bundler des assets |
| PHPUnit / Collision | Tests fonctionnels et unitaires |
| FakerPHP | Données factices pour les tests et factories |

## Installation

### Prérequis
- PHP ≥ 8.3
- Composer
- Node.js & npm
- MySQL (ou toute base supportée par Laravel)

### Étapes

```bash
# 1. Installer les dépendances PHP
composer install

# 2. Créer le fichier d'environnement et la clé applicative
cp .env.example .env
php artisan key:generate

# 3. Configurer la base de données dans .env (DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD)

# 4. Créer les tables et (optionnel) les données de test
php artisan migrate --seed

# 5. Installer les dépendances frontend et compiler les assets
npm install
npm run build

# 6. Lancer l'application en développement (serveur + queue + logs + Vite)
composer dev
```

L'application est ensuite accessible à l'adresse indiquée par le serveur (par défaut `http://localhost:8000`).

## Tests

La suite de tests couvre l'authentification, les opérations CRUD, les prévisions et les erreurs de l'API.

```bash
composer test
```

## Licence

Projet sous licence [MIT](https://opensource.org/licenses/MIT).
