# INSTRUCTIONS

Cet exercice a pour but d’évaluer vos compétences en **PHP** et **Symfony**.

Vous devrez réaliser une application gestion de bookmarks.

Vous implémenterez l'ajout de deux types de liens :

* vidéo (provenant de Vimeo)
* photo (provenant de Flickr)

Les propriétés communes d’un lien référencé sont :

* URL
* titre
* auteur
* date d'ajout

Les liens de type video auront les propriétés spécifiques suivantes :

* largeur
* hauteur
* durée

Idem pour les liens de type image :

* largeur
* hauteur

Il est possible d’associer des mots-clés pour chaque lien référencé.

La récupération des propriétés d’un lien référencé se fait en utilisant le protocole ouvert [oEmbed](http://oembed.com/). Exemple de librairie qui implémente oembed: https://github.com/oscarotero/Embed

Il faut faire une API REST au format JSON pour gérer les bookmarks: lister les liens, ajouter un lien, modifier les mots clés d’un lien, gérer les mots clés d’un lien, supprimer un lien...

Les contraintes sont d'utiliser **Symfony 4.x** et **PHP 7.x**. Il ne faut pas utiliser de générateur d'API tel que **API Platform**. Il n'y a pas besoin de faire la partie front qui consomme l'API.

Le livrable attendu est une archive de l’application incluant si besoin les instructions d’installation.

Pour vous aider à démarrer nous vous proposons un squelette d’application Symfony avec un docker-compose.

Les pré-requis sont:

* Être sous Linux
* Installer docker https://docs.docker.com/install/
* Installer docker-compose https://docs.docker.com/compose/install/