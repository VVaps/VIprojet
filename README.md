# VIprojet

Laravel-based marketplace application

# État du projet 
En cours

# ArtisLoca

# Description
Site marchand dédié aux artisans locaux. L'artisan peut créer son catalogue de produits. Le client peut visualiser tous les artisans, tous les produits. Il peut filtrer les produits par artisan. Il peut également commander le produit en le mettant dans son panier. 

## Technologies utilisées

- Laravel

## Structure du projet

```
Home
|
|- register
|         |-login
|- login
|       |-si client 
|               |- Visualiser les artisans
|                           |- visualiser le catalogue de l'artisan
|                                           |- Commander
|                                           |- donner un avis
|               |- visualiser les produits
|                           |- Commander
|                           |- donner un avis
|               |- valider la commande
|       |-si utilisateur propriétaire de comptes artisant 
|               |- Visualiser ses artisans
|                           | Créer un nouveau compte artisan
|                           | Modifier un compte artisan
|                           | Supprimer un compte artisan
|                           |- visualiser le catalogue d'un artisan
|                                               |- Créer un nouveau produit
|                                               |- Modifier un produit
|                                               |- Supprimer un produite
|- logout
|- Visualisation des produits
|                    |-Commander (panier)
|- Visualisation des artisans
                     |-Visualisation du catalogue de l'artisan
                                                    |-Commander (panier)
```
![Shéma mermaid du projet](mermaid-VIProjet.png)

# Installation
Installer Laravel sur votre poste (https://laravel.com/docs/12.x/installation)
Ouvrir VS Code et installer les extensions:
        Laravel (Offical VS Code extension)
        Laravel Blade Snippers
        Laravel Snippers
        Laravel Artisan

Récupérer l'URL de ce projet en cliquant sur le bouton CODE et en copiant l'URL affichée.
Sur votre pc, aller dans le répertoire dédié à votre projet.
Ouvrez git bash par un click droit sur le répertoire.
Dans la fenêtre du terminal git, exécuter la commande git clone *URL (*faire click droit/paste pour copier l'URL)

Ouvrir le projet VIProjet/LocalMarketPlace sous VSCode.
Ouvrir un terminal dans vscode avec Ctrl%, choisir l'onglet Terminal.
Exécuter les lignes de commande:
        copy .env.example .env
        composer install
        npm install

Modifier l'environnement en:
        Créant le fichier bootstrap.js dans le répertoire resources/js du projet
        Remplaçant le contenu de app.js par "import '../css/app.css'" 

Créer le container:
Installer Docker sur votre poste (https://docs.docker.com/engine/install/).        
Ouvrir Docker.
Exécuter dans le terminal la ligne de commande:
        docker compose up -d

Paramétrer les derniers éléments:
Exécuter les lignes de commande:
        php artisan key:generate
        php artisan make:migration
        php artisan migrate


# Création de la base de données
Vérifier l'existance de la base et des tables.

Lancer Docker.
Aller dans le container VIProjet.
Celui-ci contient MySQL et PHPMyAdmin.
Cliquer sur 8081:80 pour ouvrir PHPMyAdmin.

Utilisateur root, mot de passe root.

Ouvrir la base marketplace.
Vérifier si les tables artisans, s et order_items, comments, cart_items, products et users existent.
Aller sur l'onglet SQL.

Si vous n'avez pas de table dans la base exécutez le script marketplace_all.sql

Si vous avez les tables users et products, exéucter le script marketplace_partial.sql

Les scripts se trouvent dans VIProjet/database. Les ouvrir avec VS Code et copier le contenu dans l'onglet SQL. 

# lancer le serveur virtuel
Démarrer docker.

Ouvrir le dossier du projet avec VS Code.

Ouvrir un premier terminal powershell et exécuter les lignes de commande:
        npm install
        npm run dev
        
Ouvrir un second terminal powershell et exécuter la ligne de commande:
        php artisan serve
        
Le serveur virtuel est lancé.

Cliquez sur le lien virtuel fourni par le serveur dans le terminal ou tapez l'url suivante:
localhost:8000 pour lancer le projet

**Comment arrêter le projet:**
Fermer le serveur en faisant Ctrl+C dans le terminal où le serveur a été lancé.

Fermer VS Code.

Fermer le docker.

# Licence
Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International Public License.


[CAHIER DES CHARGES.md](https://github.com/user-attachments/files/23502912/CAHIER.DES.CHARGES.md)
### Cahier des charges projet


# Cahier des charges – ArtisLoca

---

## 1. Présentation du projet
- Site marchand pour les artisans locaux
- Les artisans doivent se faire connaitre mais n'ont pas de temps pour gérer cela. Les clients aimeraient favoriser le commerce local mais ne savent pas forcément où se diriger, ni comment être sur que les produits soient vraiment locaux. 

## 2. Objectifs
- **Objectifs principaux** : Inscription et connexion/déconnexion de l'utilisateur
                             Création des artisans 
                             Création des produits de l'artisan
                             Chargement du panier, validation de la commande
                             Notifications
- **Objectifs secondaires** : Ajout d'images pour les produits et pour les 
                                Ajout de filtres dans le catalogue produits pour faciliter la recherche
- **Indicateurs de réussite** : Les objectifs principaux sont fonctionnels, la navigation fonctionne.

## 3. Cibles utilisateurs
- **Types d’utilisateurs** : particuliers, artisans ...
- **Attentes et usages** : Les particuliers navigueront dans les catalogues et pourront les commander en remplissant leur caddie et en validant leur commande.
                            Les artisans saisiront leur catalogue, et pourront le maintenir facilement

## 4. Périmètre fonctionnel
- **Fonctionnalités principales** S'incrire, se connecter et se déconnecter.
                                    visualiser tous les artisans
                                    visualiser tous les produits tous catalogues confondus
                                    remplir le panier et valider la commande
                                    Créer, modifier, supprimer un artisan
                                    Créer, modifier, supprimer un produit
                                    Emmetre un avis
- **Fonctionnalités secondaires** : Ajout de la photo de l'artisan (logo entreprise ou autre)
                                    Ajout de la photo du produit
- **Non-fonctionnalités** : La prise en charge du paiement, 
                            L'envoi d'un mail de récapitulation de la commande à l'utilisateur et à l'artisan
                            Le commentaire au niveau de l'artisan

## 5. Parcours utilisateur
- **Scénarios d’utilisation** : Le particulier peut visualiser les produits et artisans dès la page d'accueil sans avoir besoin de se connecter. Il peut filtrer les produits par artisan en allant sur "Nos artisans" sélection d'un artisan et en click sur le bouton "voir les produits". Il ajouter des produits dans son panier en cliquant sur "Ajouter au panier". Il devra s'inscrire et se connecter pour valider sa commande.
L'artisan devra peut aussi voir tous les artisans et produits sans connexion.
Il devra s'enregistrer pour gérer ses catalogues. En s'enregistrant, il devra cocher la case "Je suis artisan" et remplir tous les champs demandés avant de valider son inscription.
Il devra ensuite cliquer sur "Nos artisans" pour visualiser l'artisan qu'il a créé. Il peut en créer d'autres. Pour chaque artisan il pourra créer un catalogue en cliquant sur le bouton "Voir les produits" dans la carte artisan. Il pourra alors créer, modifier, supprimer les produits.
Dès qu'il est connecté les listes artisan et produits sont limitées aux siennes.

- **Wireframes/maquettes** 

## 6. Spécifications techniques
- **Technologies front-end** : React , javascript (natifs dans Lavarel)
- **Technologies back-end** : Lavarel
- **Base de données** : MySQL
- **API externes** : 
- **Contraintes de sécurité** : Mot de passe hâché, email utilisateur unique, contrôles dans le code du type d'utilisateur pour ne pas donner accès à la gestion des catalogues aux utilisateur "non artisants".

## 7. Graphisme et ergonomie
- **Charte graphique** : reste à faire
- **Design responsive** : reste à faire

## 8. Livrables attendus
- **Code source complet**
- **README détaillé**
- **Rapport de réalisation** : Difficultés rencontrées, solutions apportées
- **Schéma d’architecture**
- **Présentation sous forme de slides**
- **Documentation API** (si réalisée)

## 9. Planning prévisionnel
- **Grandes étapes** : Découpage jour par jour
- **Jalons** : Prototype, maquette, API, front, test, démo finale

## 10. Critères de validation
- **Test de chaque fonctionnalité**
- **Revue de code**
- **Déploiement local**

## 12. Annexes
- Liens ressources, documentation choisie
- Maquettes initiales

<img width="1271" height="487" alt="image" src="https://github.com/user-attachments/assets/7215e834-17b7-4592-9385-8d6470c983b0" />



[ArtisLoca (1).pptx](https://github.com/user-attachments/files/23503194/ArtisLoca.1.pptx)

