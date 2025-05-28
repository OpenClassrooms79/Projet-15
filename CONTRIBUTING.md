# 🤝 Contribuer au site officiel de Ina Zaoui

Merci pour votre intérêt ! Voici comment contribuer efficacement à ce projet.

## 🐞 Signaler un bug

1. Vérifiez que le bug n'a pas déjà été signalé.
2. Créez un [ticket GitHub](https://github.com/OpenClassrooms79/Projet-15/site/issues/new) avec le label `bug` en
   incluant :
    - Une description claire du bug
    - Les étapes pour le reproduire
    - Le comportement attendu
    - Le contexte (navigateur, versions de PHP/Symfony, etc.)

## 💡 Proposer une amélioration

1. Créez un [ticket GitHub](https://github.com/OpenClassrooms79/Projet-15/site/issues/new) avec le label `enhancement`.
2. Décrivez :
    - Le besoin ou problème identifié
    - Le comportement souhaité
    - Toute idée technique ou de design associée

## 💻 Contribuer au code

### 🚀 Installation rapide

```bash
git clone https://github.com/OpenClassrooms79/Projet-15.git
cd Projet-15
composer install
```

#### Configurer la base de données

Dans le fichier `.env.local` :

```bash
DATABASE_URL="postgresql://login:motdepasse@host:5432/inazaoui"
```

Puis exécuter :

```bash
symfony console doctrine:database:create
symfony console doctrine:migrations:migrate -n
symfony console doctrine:fixtures:load -n --purge-with-truncate --env=test
```

### 🧭 Directives

- Respecter les [conventions PSR-12](https://www.php-fig.org/psr/psr-12/)
- Code sans erreur avec PhpStan (niveau 6 minimum)
- Ajouter des tests pour chaque nouvelle fonctionnalité
- Rédiger des commits clairs et concis

### 🌱 Branches Git

Branche de base : `main`

Créez une branche nommée de manière explicite. Exemple :

```bash
git checkout -b fix/photo-upload-validation
git checkout -b feature/photo-upload-validation
```

Lorsque vos modifications sont prêtes à être proposées :

- Poussez votre branche sur le dépôt distant
- Créez une Pull Request (PR) claire et complète :
    - Donnez un titre explicite résumant l’objectif de la PR
    - Rédigez une description détaillée :
        - Expliquez ce que fait la PR
        - Précisez pourquoi ces changements sont nécessaires
        - Ajoutez, si besoin, des captures d’écran, des liens vers des tickets ou des étapes de test
- Un relecteur examinera et validera la PR avant qu’elle soit fusionnée

🧪 Tests

- Tests unitaires et fonctionnels avec PHPUnit
- Fixtures disponibles dans `src/DataFixtures/`
- Utiliser la base de données de test (fichier `.env.test`)
- Les tests unitaires et fonctionnels doivent tous réussir dans l’environnement CI (GitHub Actions).

📚 Documentation

- Mettre à jour le `README.md` si une fonctionnalité est modifiée ou ajoutée
- Documenter les endpoints, rôles, ou processus techniques si nécessaire