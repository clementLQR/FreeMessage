# FreeMessage

FreeMessage est une application web sociale permettant de publier des messages (texte + image optionnelle) au sein de catégories thématiques (jeux vidéo, musique, films, livres, sport, peinture et dessin, photographie, séries), de réagir aux publications (like / dislike) et de les commenter.

Projet développé en PHP natif (architecture MVC maison) avec le moteur de templates [Twig](https://twig.symfony.com/) et une base de données MySQL/MariaDB.

## Sommaire

- [Fonctionnalités](#fonctionnalités)
- [Stack technique](#stack-technique)
- [Prérequis](#prérequis)
- [Installation](#installation)
- [Lancer le projet](#lancer-le-projet)
- [Structure du projet](#structure-du-projet)
- [Se connecter en tant qu'administrateur](#se-connecter-en-tant-quadministrateur)

## Fonctionnalités

- Inscription / connexion avec mots de passe hachés (`password_hash`)
- Publication de messages, avec ou sans image, dans une catégorie
- Likes / dislikes asynchrones (sans rechargement de page), mutuellement exclusifs
- Tri des publications par catégorie (plus récent / plus liké)
- Commentaires sur les publications
- Profil utilisateur : biographie modifiable, changement d'identifiant, liste de ses publications
- Suppression de son propre contenu
- Panneau d'administration (desktop) : gestion des publications (avec leurs commentaires) et des comptes, recherche et tri des tableaux
- Protection CSRF sur toutes les requêtes POST
- Interface responsive : navigation en barre basse sur mobile/tablette, en sidebar sur desktop

## Stack technique

- **Backend** : PHP 8+ (testé avec PHP 8.2), routeur maison (`routeur.php`) + contrôleurs (`controler.php`) + modèles (`modele/modele.php`)
- **Templates** : [Twig](https://twig.symfony.com/) 3.x
- **Base de données** : MySQL / MariaDB (via `mysqli`, requêtes préparées)
- **Frontend** : HTML/CSS/JS vanilla (pas de framework front, pas de build step)
- **Dépendances PHP** : gérées par [Composer](https://getcomposer.org/)

## Prérequis

- [XAMPP](https://www.apachefriends.org/) (Apache + MySQL/MariaDB + PHP 8+), ou toute stack équivalente
- [Composer](https://getcomposer.org/)
- PHP avec l'extension `mysqli` activée

## Installation

1. **Cloner le projet dans le dossier `htdocs` de XAMPP**

   ```bash
   cd C:/xampp/htdocs
   git clone https://github.com/clementLQR/FreeMessage.git
   cd FreeMessage
   ```

2. **Installer les dépendances PHP (Twig)**

   Le dossier `vendor/` n'est pas versionné, il doit être généré :

   ```bash
   composer install
   ```

3. **Créer la base de données et l'importer**

   Démarrer Apache et MySQL depuis le panneau de contrôle XAMPP, puis dans phpMyAdmin (ou en ligne de commande) :

   ```bash
   # créer la base
   mysql -u root -e "CREATE DATABASE freemessagev2 CHARACTER SET utf8mb4;"

   # importer le schéma + les données de démonstration
   mysql -u root freemessagev2 < freemessage.sql
   ```

   `freemessage.sql` contient la structure complète des tables (`utilisateur`, `message`, `commentaire`, `reaction`, `categorie`, `type`) ainsi qu'un jeu de données de démonstration (catégories, utilisateurs, publications avec et sans image, commentaires).

4. **Vérifier les identifiants de connexion à la base**

   Par défaut XAMPP utilise l'utilisateur `root` sans mot de passe. Si votre configuration diffère, adaptez [connection.php](connection.php) :

   ```php
   $mysqli = mysqli_connect('localhost', 'root', '', 'freemessagev2');
   ```

## Lancer le projet

Une fois Apache et MySQL démarrés, l'application est accessible à l'adresse :

```
http://localhost/FreeMessage/
```

Le fichier `.htaccess` redirige toutes les requêtes (hors fichiers statiques : CSS, JS, images) vers `routeur.php`, qui fait office de point d'entrée unique.

## Structure du projet

```
FreeMessage/
├── routeur.php          # point d'entrée, dispatch des routes
├── controler.php        # logique métier / contrôleurs
├── bootstrap.php         # initialisation de Twig
├── connection.php        # connexion à la base de données
├── modele/
│   └── modele.php        # accès aux données (requêtes SQL)
├── template/              # vues Twig
├── images/                # icônes SVG et assets statiques
├── images-upload/         # images envoyées par les utilisateurs
├── freemessage.sql        # export de la base (structure + démo)
└── composer.json
```

## Se connecter en tant qu'administrateur

Un compte `admin` est fourni dans le jeu de données de démonstration (`freemessage.sql`) et donne accès à un panneau de gestion des publications et des comptes.

1. Se rendre sur la page de connexion : `http://localhost/FreeMessage/connexion-inscription`
2. Se connecter avec les identifiants suivants :
   - **Identifiant** : `admin`
   - **Mot de passe** : `adminfreemessage1234`
3. Accéder ensuite manuellement à l'URL `http://localhost/FreeMessage/admin` (aucun lien n'y mène depuis l'interface, il faut la saisir directement).

⚠️ Le panneau d'administration n'est stylé que pour les écrans desktop (≥ 1024px de large) ; sur mobile/tablette le contenu reste accessible mais sans mise en forme dédiée.
