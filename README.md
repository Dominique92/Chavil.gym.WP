# Chavil.gym.WP
EN COURS DE DEVELOPPEMENT
Theme WordPress sur base twentytwentythree pour afficher les cours de la Gym Volontaire de Chaville

INSTALL
=======
Sous domaine - SSL Let's Encrypt
WP : install
Copier ces fichiers dans /wp-content/themes/gym
Supprimer toutes extension et thème autre que 23
Installer "Anti-Spam by CleanTalk"
  (Spam protection, AntiSpam, FireWall by CleanTalk)
  Clé d'accès
Installer "Block Editor Colors", ajouter le jaune #fff00
Installer "Contact Form 7" (pour le formulaire de contact)
  Extensions -> Contact Form 7 -> Réglages
Installer "Favicon by RealFaviconGenerator"
  Apparence -> Favicon (images/favicon.jpg)
Installer "Leaflet Map"
Installer "LiteSpeed Cache"
Installer "Site Kit by Google"
Installer "WooCommerce"
  Créer produits
    Produit simple / Virtuel
    150
    Inventaire : Vendre individuellement
Installer "Woocommerce checkout manager (WooCommerce Commander directeur par QuadLayers)
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

Toutes extensions : activer, activer les mises à jour

TODO / BUGS
===========
Ne vois plus le chargement du certificat médical
Refermer le sous-menu si on tape ailleurs
Editeur : Style blocs liste en mode tablette ou mobile
Lien direct commande : réaffiche l'accueil immédiatement si erreur

TAGS DANS LES PAGES
===================
<meta http-equiv="refresh" content="0;https">
[leaflet-map lng="2.19712" lat="48.81788"][leaflet-marker]

MISE EN SERVICE
===============
Changer url
SQL : wpgym_ -> wp_   2023.gym -> chaville.gym