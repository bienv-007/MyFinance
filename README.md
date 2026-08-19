<p align="center">
  <h1 align="center">MyFinance</h1>
  <p align="center">Application web de gestion de finances personnelles</p>
  <p align="center">
    <img src="https://img.shields.io/badge/PHP-8.3-777BB4?style=flat-square&logo=php&logoColor=white" alt="PHP">
    <img src="https://img.shields.io/badge/Laravel-13-FF2D20?style=flat-square&logo=laravel&logoColor=white" alt="Laravel">
    <img src="https://img.shields.io/badge/Vue-3-4FC08D?style=flat-square&logo=vuedotjs&logoColor=white" alt="Vue.js">
    <img src="https://img.shields.io/badge/Tailwind_CSS-4-06B6D4?style=flat-square&logo=tailwindcss&logoColor=white" alt="Tailwind CSS">
    <img src="https://img.shields.io/badge/License-MIT-06B6D4?style=flat-square" alt="License">
  </p>
</p>

---

## Apercu

**MyFinance** est une application web de gestion de finances personnelles permettant de centraliser le suivi des revenus, des depenses, des enveloppes budgetaires et de la planification financiere grace aux previsions. Interface entierement en **francais**.

L'application expose deux experiences complementaires :

- **Tableau de bord interactif** -- SPA monopage (Vue 3) offrant une vue d'ensemble et un acces rapide a tous les modules.
- **Vues detaillees** -- Pages serveur (Blade) pour la gestion fine de chaque module avec formulaires, cartes et badges de statut.

## Fonctionnalites

### Authentification
- Inscription et connexion par email / mot de passe (hachage bcrypt, cout 12).
- Sessions securisees avec regeneration a la connexion.
- Deconnexion et controle d'acces strict : chaque utilisateur ne voit et ne manipule que ses propres donnees.

### Tableau de bord
- Vue d'ensemble : nombre de categories, revenus, depenses et budgets.
- Acces rapide aux modules via navigation laterale.
- Recherche temps reel sur l'ensemble des donnees.

### Categories
- CRUD complet (creation, modification, suppression).
- Recherche par nom.
- Suppression bloquee si la categorie est utilisee par des depenses ou des previsions.

### Revenus
- Enregistrement d'un revenu (source, montant, date, description).
- Modification, suppression, recherche et tri par date.
- Assignation automatique au budget actif par date.

### Depenses
- Enregistrement d'une depense rattachee a une categorie (montant, date, description).
- **Debit automatique** du solde du budget actif a la creation.
- **Credit automatique** du solde a la modification ou suppression.
- **Blocage** si le montant depasse le solde disponible.
- Modification, suppression, recherche par categorie/description et tri.

### Budgets
- Creation d'enveloppes budgetaires (periode, montant total, dates de debut et de fin).
- **Un seul budget actif** par utilisateur a la fois.
- Statut calcule automatiquement : **A venir** / **En cours** / **Termine** / **Archive**.
- **Archivage automatique** lorsque le solde est epuise ou la date d'expiration atteinte.
- Historique des budgets archives avec montant depense, solde final et depenses/revenus lies.
- Statistiques : total, budgets actifs, montant budgete, montant depense, solde restant.

### Previsions de revenus
- Planification des entrees futures (source, montant, date, description).
- Action **<< Marquer comme percu >>** : conversion en revenu reel (idempotent, la prevision n'est pas supprimee).
- Statuts automatiques : **Realisee** / **A venir** / **Expiree** / **Aujourd'hui**.
- Filtres avances : recherche, source, date, mois (AAAA-MM), montant min/max, tri.
- Statistiques : montant total, montant du mois/annee, revenus attendus, expires, prochain revenu, source principale, prevision la plus elevee.

### Previsions de depenses
- Planification des sorties futures (categorie, montant, date, description).
- Action **<< Valider >>** : conversion atomique en depense reel (`DB::transaction`), debit du budget, archivage si necessaire, suppression de la prevision.
- Statuts automatiques : **A venir** / **Depassee** / **Aujourd'hui**.
- Filtres avances : recherche, categorie, date, mois (AAAA-MM), montant min/max, tri.
- Statistiques : montant total, en attente, depassees, prochaine depense, categorie la plus frequente.

### Notifications
- Notifications automatiques : seuils de budget (80 %, 90 %, 100 %), previsions dues/expirrees, validation de previsions.
- Polling temps reel (15 secondes) avec son, vibration et notifications navigateur.
- Marquer lu / marquer toutes lues / supprimer.
- Preferences configurables par utilisateur (son, vibration, notifications navigateur).

## Technologies utilisees

### Backend

| Technologie | Version | Usage |
| --- | --- | --- |
| PHP | ^8.3 | Langage serveur |
| Laravel | ^13.8 | Framework (routing, Eloquent, migrations, sessions, validation) |
| Eloquent ORM | -- | Modelisation et relations entre entites |
| Laravel Sanctum | ^4.0 | Gestion des sessions / jetons d'API |
| MySQL | -- | Base de donnees relationnelle (SQLite pour les tests) |
| Laravel Pint | ^1.27 | Formatage du code |
| Laravel Pail | ^1.2.5 | Visualisation des logs en temps reel |

### Frontend

| Technologie | Usage |
| --- | --- |
| Vue 3 | Tableau de bord SPA (charge via CDN) |
| Axios | Appels API (sessions + CSRF) |
| Tailwind CSS 4 | Styles de l'interface |
| Blade | Vues detaillees rendues cote serveur |
| Alpine.js | Interactivite dans les vues Blade |
| Font Awesome 6 | Icones |
| Toastr / SweetAlert2 | Notifications toast et dialogues de confirmation |
| Flowbite | Composants UI |

### Outils de developpement

| Technologie | Usage |
| --- | --- |
| Vite | Bundler des assets avec HMR |
| PHPUnit / Collision | Tests fonctionnels et unitaires |
| FakerPHP | Donnees factices pour les tests et factories |
| Composer setup | Installation automatisee |

## Installation

### Pre requis

- PHP >= 8.3
- Composer
- Node.js >= 18 & npm
- MySQL (ou toute base supportee par Laravel)

### Installation rapide

```bash
git clone https://github.com/votre-utilisateur/MyFinance.git
cd MyFinance
composer setup
```

La commande `composer setup` execute automatiquement :
1. `composer install`
2. Copie de `.env.example` vers `.env` (si absent)
3. Generation de la cle applicative
4. Execution des migrations
5. Installation des dependances frontend
6. Compilation des assets

### Installation manuelle

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install
npm run build
```

### Lancer le serveur de developpement

```bash
composer dev
```

Cela lance en parallele :
- Le serveur Laravel (`php artisan serve`)
- L'ecoute de la queue (`php artisan queue:listen`)
- Le visualiseur de logs (`php artisan pail`)
- Le bundler Vite avec HMR (`npm run dev`)

L'application est accessible sur `http://localhost:8000`.

> **Compte de test** : `test@example.com` / `password`

## Docker

### Build de l'image

```bash
docker build -t myfinance .
```

### Lancer le conteneur

```bash
docker run -d \
  --name myfinance \
  -p 10000:10000 \
  -e APP_KEY=base64:... \
  -e DB_CONNECTION=mysql \
  -e DB_HOST=host.docker.internal \
  -e DB_PORT=3306 \
  -e DB_DATABASE=myfinance \
  -e DB_USERNAME=root \
  -e DB_PASSWORD=secret \
  myfinance
```

L'application sera accessible sur `http://localhost:10000`.

> Le Dockerfile utilise un build multi-etapes : compilation frontend avec Node 22, puis image PHP 8.3 avec les extensions necessaires.

## Structure du projet

```
MyFinance/
├── app/
│   ├── Actions/                  # Actions transactionnelles metier
│   │   ├── ConvertDepensePrevisionToDepense.php
│   │   └── MarkRevenuPrevisionAsReceived.php
│   ├── Console/Commands/         # Commandes Artisan (taches planifiees)
│   │   ├── ArchiveCompletedBudgets.php
│   │   └── NotifyPrevisions.php
│   ├── Http/
│   │   ├── Controllers/         # Controleurs web (Blade)
│   │   ├── Controllers/Api/     # Controleurs API (JSON)
│   │   ├── Requests/            # Validation des formulaires
│   │   └── Resources/           # Serialisation JSON (API Resources)
│   ├── Models/                  # 10 modeles Eloquent
│   └── Services/                # Logique metier reutilisable
│       ├── BudgetCycleService.php
│       ├── NotificationService.php
│       └── RevenuPrevisionService.php
├── database/
│   ├── factories/               # 7 factories pour les tests
│   ├── migrations/              # 18 migrations
│   └── seeders/
├── public/
│   ├── js/                      # Scripts JS pour les vues Blade
│   └── sounds/                  # Fichiers audio (notifications)
├── resources/
│   ├── views/
│   │   ├── welcome.blade.php    # SPA Vue 3 (tableau de bord)
│   │   ├── layouts/             # Mise en page Blade
│   │   ├── components/          # 14 composants Blade reutilisables
│   │   ├── budgets/             # Vues budgets
│   │   ├── depense-previsions/  # Vues previsions de depenses
│   │   ├── revenu-previsions/   # Vues previsions de revenus
│   │   └── notifications/       # Vues notifications
│   └── css/                     # Styles Tailwind
├── routes/
│   ├── api.php                  # Routes API
│   └── web.php                  # Routes web
├── tests/                       # ~77 tests
│   ├── Feature/Api/             # Tests API
│   └── Feature/                 # Tests metier
├── dockerfile
├── composer.json
└── package.json
```

## API REST

L'application expose une API JSON complete. Toutes les routes protegees necessitent une session authentifiee (`auth:web`).

### Authentification

| Methode | Endpoint | Description |
| --- | --- | --- |
| `POST` | `/api/auth/register` | Inscription |
| `POST` | `/api/auth/login` | Connexion |
| `GET` | `/api/auth/me` | Utilisateur courant |
| `POST` | `/api/auth/logout` | Deconnexion |

### Categories

| Methode | Endpoint | Description |
| --- | --- | --- |
| `GET` | `/api/categories` | Liste (paginee, recherche) |
| `POST` | `/api/categories` | Creation |
| `GET` | `/api/categories/{id}` | Detail |
| `PUT` | `/api/categories/{id}` | Modification |
| `DELETE` | `/api/categories/{id}` | Suppression (bloquee si utilisee) |

### Revenus

| Methode | Endpoint | Description |
| --- | --- | --- |
| `GET` | `/api/revenus` | Liste (paginee, tri, recherche) |
| `POST` | `/api/revenus` | Creation |
| `GET` | `/api/revenus/{id}` | Detail |
| `PUT` | `/api/revenus/{id}` | Modification |
| `DELETE` | `/api/revenus/{id}` | Suppression |

### Depenses

| Methode | Endpoint | Description |
| --- | --- | --- |
| `GET` | `/api/depenses` | Liste (paginee, tri, recherche, filtre budget) |
| `POST` | `/api/depenses` | Creation (debit budget automatique) |
| `GET` | `/api/depenses/{id}` | Detail |
| `PUT` | `/api/depenses/{id}` | Modification (recalcul budget) |
| `DELETE` | `/api/depenses/{id}` | Suppression (credit budget) |

### Budgets

| Methode | Endpoint | Description |
| --- | --- | --- |
| `GET` | `/api/budgets` | Liste (paginee, stats, historiques) |
| `POST` | `/api/budgets` | Creation (un seul actif par utilisateur) |
| `GET` | `/api/budgets/{id}` | Detail |
| `PUT` | `/api/budgets/{id}` | Modification (redemarrage de cycle) |
| `DELETE` | `/api/budgets/{id}` | Suppression |
| `GET` | `/api/budgets/historiques` | Historique des budgets archives |

### Previsions de revenus

| Methode | Endpoint | Description |
| --- | --- | --- |
| `GET` | `/api/revenu-previsions` | Liste (paginee, filtres, stats) |
| `POST` | `/api/revenu-previsions` | Creation |
| `GET` | `/api/revenu-previsions/{id}` | Detail |
| `PUT` | `/api/revenu-previsions/{id}` | Modification |
| `DELETE` | `/api/revenu-previsions/{id}` | Suppression |
| `POST` | `/api/revenu-previsions/{id}/receive` | Marquer comme percu |

### Previsions de depenses

| Methode | Endpoint | Description |
| --- | --- | --- |
| `GET` | `/api/depense-previsions` | Liste (paginee, filtres, stats) |
| `POST` | `/api/depense-previsions` | Creation |
| `GET` | `/api/depense-previsions/{id}` | Detail |
| `PUT` | `/api/depense-previsions/{id}` | Modification |
| `DELETE` | `/api/depense-previsions/{id}` | Suppression |
| `POST` | `/api/depense-previsions/{id}/validate` | Valider (convertir en depense) |

### Notifications

| Methode | Endpoint | Description |
| --- | --- | --- |
| `GET` | `/api/notifications` | Liste (paginee, compteur non-lues) |
| `GET` | `/api/notifications/{id}` | Detail (marque comme lu) |
| `PATCH` | `/api/notifications/{id}/read` | Marquer comme lu |
| `PATCH` | `/api/notifications/read-all` | Marquer toutes comme lues |
| `DELETE` | `/api/notifications/{id}` | Supprimer une notification |
| `DELETE` | `/api/notifications` | Supprimer toutes les notifications |

### Preferences de notification

| Methode | Endpoint | Description |
| --- | --- | --- |
| `GET` | `/api/notification-preferences` | Recuperer les preferences |
| `PUT` | `/api/notification-preferences` | Mettre a jour les preferences |

## Tests

La suite de tests couvre l'authentification, les operations CRUD, les previsions, les notifications et la gestion des erreurs API (~77 tests).

```bash
composer test
```

Les tests utilisent une base SQLite en memoire pour des executions rapides et isolees.

## Licence

Ce projet est sous licence [MIT](https://opensource.org/licenses/MIT).
