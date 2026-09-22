# 🛍️ AFROSTYLE BOUTIQUE — E-commerce de tenues africaines

Plateforme e-commerce complète dédiée à la vente de tenues africaines authentiques, avec authentification sécurisée, panier et favoris persistants, et un espace administrateur complet.

Projet réalisé dans le cadre du cours de **Développement Web** (Master Ingénierie Mathématique et Numérique — FST/UCAD & Université Bretagne Sud), en équipe de 4 personnes.

## 📌 Contexte

L'objectif était de concevoir et développer une plateforme e-commerce complète, en définissant une architecture multi-profils (visiteur, client, administrateur) et en implémentant l'ensemble des parcours utilisateurs, de la découverte du catalogue jusqu'à la commande, ainsi qu'un back-office complet de gestion.

## 🎯 Fonctionnalités

**Visiteurs**
- Parcours du catalogue et fiches produits détaillées
- Recherche de produits
- Création de compte

**Clients** (après connexion)
- Panier et liste de favoris, persistés en base et restaurés automatiquement à la connexion
- Passage et suivi de commande
- Avis et évaluations produits
- Gestion du compte, récupération de mot de passe

**Administrateurs**
- Authentification dédiée, séparée de celle des clients
- Gestion complète des produits (ajout, modification, suppression)
- Gestion des commandes (liste et détail)
- Gestion des clients
- Gestion des fournisseurs
- Gestion des promotions
- Dashboard analytique

## 🔐 Sécurité

- Mots de passe hashés (`password_hash` / `password_verify`) — jamais stockés en clair
- Requêtes SQL **préparées** via PDO — protection contre les injections SQL
- Sessions PHP pour la gestion de l'état de connexion et des rôles
- Authentification admin isolée de l'authentification client (`login_admin.php` / `auth_check.php`)
- Redirection post-connexion contextuelle (retour à la page demandée avant l'authentification)

## 🏗️ Structure du projet

```
afrostyle-boutique/
├── administrateur/
│   ├── login_admin.php / deconnexion.php / auth_check.php   # Authentification admin dédiée
│   ├── header_admin.php / footer_admin.php
│   ├── index.php                                             # Dashboard analytique
│   ├── produits.php / produit_ajouter.php / produit_modifier.php / produit_supprimer.php
│   ├── commandes.php / commande_detail.php
│   ├── clients.php
│   ├── fournisseurs.php
│   ├── promotions.php
│   ├── parametre.php / profil.php
│   └── database.sql                                          # Schéma de base de données
├── commun/                # Includes partagés (header, footer, connexion DB)
├── public/                # Assets (CSS, images, JS)
├── index.php              # Page d'accueil
├── catalogue.php          # Liste des produits
├── produit.php / produit_detail.php
├── panier.php             # Panier (persisté en base)
├── favoris.php            # Liste de favoris
├── connexion.php / inscription.php / deconnexion.php / mdp_oublie.php
├── compte.php             # Espace client
├── commandes.php / confirmation.php / succes.php
├── avis.php                # Avis et évaluations
└── contact.php
```

## 🛠️ Stack technique

`PHP` · `MySQL` · `PDO` · `Bootstrap` · `Sessions PHP` · `Authentification sécurisée`

## 🚀 Lancer le projet

1. Importer le schéma de base de données :
   ```bash
   mysql -u root -p nom_de_la_base < administrateur/database.sql
   ```
2. Configurer les identifiants de connexion dans `commun/connexiondb.php`
3. Lancer un serveur PHP :
   ```bash
   php -S localhost:8000
   ```
4. Accéder à `http://localhost:8000` (côté client) ou `http://localhost:8000/administrateur/login_admin.php` (côté admin)

## 💡 Ce que ce projet démontre

Une plateforme e-commerce entièrement fonctionnelle, pas seulement conçue : authentification sécurisée (mots de passe hashés, requêtes préparées PDO), panier et favoris persistés en base et restaurés à la connexion, et un back-office complet (produits, commandes, clients, fournisseurs, promotions) avec une authentification admin isolée de celle des clients. Une attention portée à la sécurité et à la séparation des responsabilités dès les fondations.

## 👨‍💻 Équipe projet

Mouhamadoul Mourtadha Gueye · Mouhamadou Malal Ba · Ndeye Touty Sarr · Nisrine Attoumane

Cours : Développement Web — M. Martial Kouleye Ngouffo

## 👤 Auteur

Mouhamadoul Mourtadha Gueye — [LinkedIn](https://www.linkedin.com/in/mouhamadoul-mourtadha-gueye/) · [GitHub](https://github.com/mourtadag4-code)
