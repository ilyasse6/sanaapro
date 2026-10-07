# Collaboration — Projet Company (Prowise)

Ce document explique comment travailler à deux sur ce projet avec GitHub.

---

## Pour toi (Ilyasse) — Premier push

Exécute ces commandes **une seule fois** pour envoyer le projet sur GitHub :

```bash
cd /Applications/XAMPP/xamppfiles/htdocs/company

# 1. Initialiser Git (si pas déjà fait)
git init

# 2. Ajouter le dépôt distant
git remote add origin https://github.com/ilyasse6/company.git

# 3. Tout ajouter, committer, pousser
git add .
git commit -m "Initial commit - Projet Prowise"
git branch -M main
git push -u origin main
```

Si le dépôt GitHub a déjà du contenu (ex. README créé sur GitHub), utilise plutôt :

```bash
git pull origin main --allow-unrelated-histories
git push -u origin main
```

---

## Pour ton ami — Récupérer le projet et travailler

### 1. Prérequis sur sa machine

- **PHP** 8.1+ (XAMPP, MAMP, Laragon ou PHP seul)
- **Composer** : https://getcomposer.org
- **Node.js** (optionnel, pour les assets)
- **MySQL** (ou MariaDB)
- **Git** : https://git-scm.com

### 2. Cloner le dépôt

```bash
git clone https://github.com/ilyasse6/company.git
cd company
```

### 3. Installer les dépendances PHP

```bash
composer install
```

### 4. Fichier d’environnement

```bash
cp .env.example .env
php artisan key:generate
```

Puis ouvrir `.env` et configurer la base de données :

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=company
DB_USERNAME=root
DB_PASSWORD=
```

Créer la base `company` dans MySQL (ou le nom choisi dans `DB_DATABASE`).

### 5. Lancer les migrations et les seeders

```bash
php artisan migrate --seed
```

Ou pour repartir de zéro avec des données de test :

```bash
php artisan migrate:fresh --seed
```

### 6. Lien storage (optionnel)

```bash
php artisan storage:link
```

### 7. Démarrer le serveur

```bash
php artisan serve
```

Puis ouvrir : **http://localhost:8000**

---

## Workflow quotidien (pour les deux)

### Avant de commencer à travailler

```bash
git pull origin main
```

### Après avoir fini une fonctionnalité / correction

```bash
git add .
git commit -m "Description courte de ce que tu as fait"
git push origin main
```

L’autre fait un `git pull origin main` pour récupérer les changements.

---

## Récapitulatif pour ton ami

| Étape | Commande / action |
|--------|--------------------|
| 1. Cloner | `git clone https://github.com/ilyasse6/company.git` puis `cd company` |
| 2. Dépendances | `composer install` |
| 3. Env | `cp .env.example .env` puis `php artisan key:generate` |
| 4. Base de données | Créer la BDD dans MySQL, remplir `DB_*` dans `.env` |
| 5. Migrations | `php artisan migrate --seed` |
| 6. Lancer | `php artisan serve` → http://localhost:8000 |

**Important :** ne jamais committer le fichier `.env` (il est dans `.gitignore`). Chacun garde son propre `.env` avec ses paramètres locaux (DB, etc.).

---

## En cas de conflit Git

Si `git pull` affiche des conflits :

1. Ouvrir les fichiers marqués "both modified".
2. Résoudre les conflits (garder le bon code ou fusionner).
3. Puis :

```bash
git add .
git commit -m "Résolution des conflits"
git push origin main
```

Si tu veux, tu peux envoyer à ton ami le lien vers ce fichier :  
`https://github.com/ilyasse6/company/blob/main/COLLABORATION.md`
