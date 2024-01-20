# Chavil.gym.WP
Theme WordPress sur base twentytwentythree pour afficher les cours de https://chaville.gym.c92.fr/


BUGS
====
```
gym 2024 formulaire contact
gym 2024 js bouton reste ouvert / ne marche pas sur mobile
zIndex menu / commandes...
taille des photos sur une grande page
On ne voit pas le panier même s'il est plein
Boutique : Lien direct commande : réaffiche l'accueil immédiatement si erreur
```

TODO
====
```
```

BEST
====
```
Réduction proportionnelle au nombre d'articles
Saisir les horaires à partir des produits de la boutique
Trier l'ordre des articles dans la page boutique
Ne pas afficher le panier dans les horaries si le cours est déjà dans le panierStyle print (titres)
Refermer le sous-menu si on tape ailleurs
Editeur : Surligner le bloc sélectionné
Editeur : Style blocs liste en mode tablette ou mobile
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
