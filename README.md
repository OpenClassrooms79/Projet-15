![Intégration continue](https://github.com/OpenClassrooms79/Projet-15/actions/workflows/ci.yml/badge.svg)
![Phpstan](https://img.shields.io/badge/phpstan-level%207-green)
![Codecov](https://codecov.io/gh/OpenClassrooms79/Projet-15/branch/main/graph/badge.svg)
![Symfony](https://img.shields.io/badge/Symfony-black?logo=symfony)
![GitHub Issues](https://img.shields.io/github/issues/OpenClassrooms79/Projet-15)
[![Contribuer](https://img.shields.io/badge/CONTRIBUTING-md-blue)](CONTRIBUTING.md)

# 📸 Ina Zaoui – Site officiel

Ce site présente les œuvres de la photographe **Ina Zaoui**, spécialiste des paysages du monde entier, explorés via des
moyens de déplacement écologiques (à pied, à vélo, à la voile, etc.).  
Il met également en lumière de jeunes photographes invités à publier leurs propres clichés via un espace
administrateur dédié.

## ✨ Fonctionnalités

- Galerie de photos classées par albums.
- Espace admin pour :
    - Gestion des albums
    - Gestion des photographes invités
    - Ajout/suppression de photos (admin et utilisateurs)

## 📦 Pré-requis

- PHP ≥ 8.0
- Composer
- PostgreSQL ≥ 17
- Extension PHP Xdebug
- Symfony CLI

## ⚙️ Installation

### 1️⃣ Cloner le projet

```bash
git clone https://github.com/OpenClassrooms79/Projet-15
cd Projet-15
```

### 2️⃣ Installer les dépendances

```bash
composer install
```

### 3️⃣ Installation de la base de données

#### Configurer la connexion dans le fichier `.env.local` :

```bash
DATABASE_URL="postgresql://login:motdepasse@host:5432/inazaoui"
```

#### Supprimer la base de données

```bash
symfony console doctrine:database:drop --force --if-exists
```

#### Créer la base de données

```bash
symfony console doctrine:database:create
```

#### Exécuter les migrations

```bash
symfony console doctrine:migrations:migrate -n
```

#### Charger les fixtures dans l'environnement de test

```bash
symfony console doctrine:fixtures:load -n --purge-with-truncate --env=test
```

### 4️⃣ Copier les images

Télécharger [l'archive de sauvegarde du site](https://s3.eu-west-1.amazonaws.com/course.oc-static.com/projects/876_DA_PHP_Sf_V2/P15/backup.zip)
et copier les images vers le répertoire `public/uploads` du projet.

## 🚀 Usage

### Démarrage du serveur web

```bash
symfony serve
```

### Analyse statique du code avec phpstan

```bash
vendor/bin/phpstan analyse src tests
```

### 🧪 Exécution des tests unitaires et fonctionnels et calcul de la couverture du code

```bash
php bin/phpunit --coverage-html var/coverage
```

Le détail de la couverture du code sera disponible dans le répertoire `var/coverage/index.html` du projet.  
L'objectif minimum de couverture est de 70%.