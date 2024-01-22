# Chavil.gym.WP
Theme WordPress sur base twentytwentythree pour afficher les cours de https://chaville.gym.c92.fr/


BUGS
====
```
On ne voit pas le panier même s'il est plein
Boutique : Lien direct commande : réaffiche l'accueil immédiatement si erreur
```

TODO
====
```
Sortir les shortcodes de footer / mettre categorie en argument
```

BEST
====
```
Rediriger en dehors de la boutique
Taille des photos sur une grande page
Trier l'ordre des articles dans la page boutique
Ne pas afficher le panier dans les horaries si le cours est déjà dans le panier
Style print (titres)
Editeur : Style blocs entouré en mode tablette ou mobile
??? debug ? Validation ! Undefined offset: -1 in /home3/cado1118/public_html/gymnew/wp-includes/post-template.php on line 330
```

INSTALL CPanel O2switch new.gym.c92.fr
=======
```
Créer sous domaine
Installer Let's Encrypt™ SSL
CPanel -> WordPress
  https://
  xxx.c92.fr
Supprimer toutes extension
Extensions -> add -> WooCommerce
  pays : france
Apparence -> Thème -> Twenty-Three
Reparenter les pages sous mon_compte ou brouillon
ATTENTION : Voir toutes les pages de la boutique pour les créer avec le bon style
/.htaccess -> Header set Cache-Control "max-age=0, private, no-cache, no-store, must-revalidate"
/config.php -> debug = true
Copier /fichiers dans /fichiers
Copier les fichiers .../gym/... dans /wp-content/themes/gym
Activer theme gym
Importer pages gym
Réglages -> Lecture -> La page d’accueil affiche -> Une page statique -> Accueil
Importer produits
Ou
  Produit simple / Virtuel
  155
  Inventaire : Vendre individuellement
  Cocher catégorie / créer
Installer "WooCommerce Extended Coupon Features FREE" Par Soft79
  Marketing -> Codes promo
    Retirer le menu de code promo hérité
    Forfait 2 cours ...
    Remise panier fixe
    Valeur : 215 ....
    Publier
Installer "Favicon by RealFaviconGenerator"
  Apparence -> Favicon
  Sélectionner dans la bibliothèque de médias
  Sélectionner des fichiers (sur le PC)
  images/icon.jpg
  Sélectionner
  Generate your favicon
  Generate your favicon and html code
Installer "Block Editor Colors"
  Extensions -> Block Editor Colors -> Settings
  Ajouter Yellow #fff00
Installer "Leaflet Map"
  Leaflet Map -> Réglages -> 48,798 2,187 17 300 400
Installer "Contact Form by WPForms – Drag & Drop Form Builder for WordPress" Par WPForms (pour le formulaire de contact)
  Page "Nous écrire" : insérer bloc form
  Générer formulaire simple

Installer Checkout Field Manager (Checkout Manager) for WooCommerce par QuadLayers
  Extensions -> WooCommerce Commander directeur -> Paramètres (Réglages)
  Général -> Commandes -> Facturation : ajouter des champs
  Définir date naissance et certificat médical (obligatoires)
  Désactiver Entreprise & Région
  ????? VERIFIER SI A FAIRE Extensions -> Contact Form 7 -> Réglages

??? Installer "Multiples Roles"
    Installer "WP Dark Mode"
      Settings -> Cocher tout
    Installer "Site Kit by Google"
    Installer "Anti-Spam by CleanTalk"
      (Spam protection, AntiSpam, FireWall by CleanTalk)
      Clé d'accès
    Installer "LiteSpeed Cache"

Créer comptes
Toutes extensions : activer, activer les mises à jour
```

INSTALL CPanel O2switch
=======
```
Créer sous domaine
Installer Let's Encrypt™ SSL
CPanel -> WordPress
Supprimer toutes extension
Extensions -> add -> WooCommerce
Voir toutes les pages
Reparenter les pages sous mon_compte ou brouillon
Copier les fichiers .../gym/... dans /wp-content/themes/gym
Activer theme gym
Importer pages gym
Importer produits
Réglages -> Lecture -> La page d’accueil affiche -> Une page statique

***

Copier /fichiers dans /fichiers
Installer "Favicon by RealFaviconGenerator"
  Apparence -> Favicon
  Sélectionner dans la bibliothèque de médias
  images/favicon.jpg
  Generate your favicon
Installer "Block Editor Colors"
  Extensions -> Block Editor Colors -> Settings
  Ajouter Yellow #fff00
Installer "Leaflet Map"
  Leaflet Map -> Réglages -> reprendre les valeurs
Installer "Contact Form by WPForms – Drag & Drop Form Builder for WordPress" Par WPForms (pour le formulaire de contact)
  ????? VERIFIER SI A FAIRE Extensions -> Contact Form 7 -> Réglages

Entrer les pages

Installer "WooCommerce"
  Créer catégories
  Créer produits
    Produit simple / Virtuel
    155
    Inventaire : Vendre individuellement
    Cocher catégorie
NO: Installer "Woocommerce checkout manager (WooCommerce Commander directeur par QuadLayers)
Installer Checkout Field Manager (Checkout Manager) for WooCommerce par QuadLayers
  Extensions -> WooCommerce Commander directeur -> Paramètres (Réglages)
  Général -> Commandes -> Facturation : ajouter des champs
  Définir date naissance et certificat médical (obligatoires)
  Désactiver Entreprise & Région
Installer "WooCommerce Extended Coupon Features FREE" Par Soft79
  Marketing -> Codes promo
    Retirer le menu de code promo hérité
    Restrictions d'usage -> min / max / Utilisation individuelle
    ?? Divers -> Coupon automatique / Appliquer silencieusement

??? Installer "Multiples Roles"
    Installer "WP Dark Mode"
      Settings -> Cocher tout
    Installer "Site Kit by Google"
    Installer "Anti-Spam by CleanTalk"
      (Spam protection, AntiSpam, FireWall by CleanTalk)
      Clé d'accès
    Installer "LiteSpeed Cache"

Toutes extensions : activer, activer les mises à jour
```

INSTALL
=======
```
O2switch
Créer base MySQL
Ajouter un utilisateur à la base de données : tous privilèges
Créer sous domaine
Installer Let's Encrypt™ SSL
Download WP -> upload
Install
Copier les fichiers dans /wp-content/themes/gym
Copier /fichiers dans /fichiers
Activer theme gym
Supprimer toutes extension
Installer "Favicon by RealFaviconGenerator"
  Apparence -> Favicon
  Sélectionner dans la bibliothèque de médias
  images/favicon.jpg
  Generate your favicon
Installer "Block Editor Colors"
  Extensions -> Block Editor Colors -> Settings
  Ajouter Yellow #fff00
Installer "Leaflet Map"
  Leaflet Map -> Réglages -> reprendre les valeurs
Installer "Contact Form by WPForms – Drag & Drop Form Builder for WordPress" Par WPForms (pour le formulaire de contact)
  ????? VERIFIER SI A FAIRE Extensions -> Contact Form 7 -> Réglages

Entrer les pages
réglages -> Lecture -> La page d’accueil affiche -> Une page statique

Installer "WooCommerce"
  Créer catégories
  Créer produits
    Produit simple / Virtuel
    155
    Inventaire : Vendre individuellement
    Cocher catégorie
NO: Installer "Woocommerce checkout manager (WooCommerce Commander directeur par QuadLayers)
Installer Checkout Field Manager (Checkout Manager) for WooCommerce par QuadLayers
  Extensions -> WooCommerce Commander directeur -> Paramètres (Réglages)
  Général -> Commandes -> Facturation : ajouter des champs
  Définir date naissance et certificat médical (obligatoires)
  Désactiver Entreprise & Région
Installer "WooCommerce Extended Coupon Features FREE" Par Soft79
  Marketing -> Codes promo
    Retirer le menu de code promo hérité
    Restrictions d'usage -> min / max / Utilisation individuelle
    Divers -> Coupon automatique / Appliquer silencieusement

??? Installer "Multiples Roles"
    Installer "WP Dark Mode"
      Settings -> Cocher tout
    Installer "Site Kit by Google"
    Installer "Anti-Spam by CleanTalk"
      (Spam protection, AntiSpam, FireWall by CleanTalk)
      Clé d'accès
    Installer "LiteSpeed Cache"

Toutes extensions : activer, activer les mises à jour
```

TAGS DANS LES PAGES
===================
```
<meta http-equiv="refresh" content="0;https://chaville.gym.c92.fr/wp-content/uploads/2023/05/Fiche-inscription_2023_2024.pdf">
[leaflet-map lng="2.19712" lat="48.81788"][leaflet-marker]
```

MISE EN SERVICE
===============
```
Changer url
SQL : chaville.gym.c92.fr -> test.gym.c92.fr
SQL : chaville.gym@c92.fr -> chavil.gym@c92.fr
robots.txt
User-agent: *
Disallow: /
```
