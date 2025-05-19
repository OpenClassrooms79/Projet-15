![Intégration continue](https://github.com/OpenClassrooms79/Projet-15/actions/workflows/ci.yml/badge.svg)
![Phpstan](https://img.shields.io/badge/phpstan-level%207-green)
![Codecov](https://codecov.io/gh/OpenClassrooms79/Projet-15/branch/main/graph/badge.svg)

# Ina Zaoui

Pour se connecter avec le compte de Ina, il faut utiliser les identifiants suivants:

- identifiant : `ina@zaoui.com`
- mot de passe : `password`

Vous trouverez dans le fichier `backup.zip` un dump SQL anonymisé de la base de données et toutes les images qui se
trouvaient dans le dossier `public/uploads`.
Faudrait peut être trouver une meilleure solution car le fichier est très gros, il fait plus de 1Go.

-----

# 📸 Ina Zaoui – Site officiel

Ce site présente les œuvres de la photographe **Ina Zaoui**, spécialiste des paysages du monde entier, explorés via des
moyens de déplacement écologiques (à pied, à vélo, en montgolfière, etc.).  
Il met également en lumière de **jeunes photographes** invités à publier leurs propres clichés via un espace
administrateur dédié.

## 🌍 Fonctionnalités

- Galerie de photos classées par albums.
- Espace admin pour :
    - Gestion des **albums**
    - Gestion des **photographes invités**
    - Ajout/suppression de **photos** (admin et utilisateurs)

## 📦 Pré-requis

- PHP ≥ 8.0
- Composer ≥ 2.8
- Symfony CLI (facultatif mais conseillé)
- PostgreSQL ≥ 17

## ⚙️ Installation

```bash
git clone https://github.com/OpenClassrooms79/Projet-15
cd Projet-15

composer install
