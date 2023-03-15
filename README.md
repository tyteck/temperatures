## Temperatures

Ce projet à pour ambition de répondre à la question "mais est-ce qu'il fait vraiment de plus en plus chaud ?".
Rappelle toi à l'anniversaire de XXX on était déja en short t-shirt !!!

### Comment y répondre ?

Dans mon esprit afficher une courbe des températures recueillies depuis 2018 dans tous les départements français me parait une bonne solution.
Je compte récupérer les données de température et en faire un graphique on ne peut plus simple à comprendre.

D'emblée, dès la page d'accueil on pourra voir 3 courbes des tempéatures.

-   La température minimal en France
-   La température maximale en France
-   La température moyenne en France

En utilisant une liste déroulante on pourra voir la meme chose mais pour un département francais spécifique.

## Installation

Ce service ne requiert que quelques éléments "standards".

-   un serveur web
-   un moteur de base de données
-   php 8.2

Par soucis de compatibilité multi plateformes j'utilise docker.
A titre d'exemple voici ma configuration locale

### Configuration locale

#### Configuration commune à tous mes projets

-   un container mysql - base de données
-   un container mailhog - mail catcher
-   un container phpmyadmin - qui tape sur le serveur de DB local
-   un container [nginx-proxy](https://github.com/tyteck/nginx-proxy) - Il bind les ports 80 et 443 et redirige les urls locales vers le bon container.

#### Configuration spécifique à temperatures

-   un container php8.2-apache

tous les containers (communs et spécifiques) sont sur le même réseau, ce qui leur permet de communiquer avec les outils communs notamment.

Pour démarrer le service, il faut

-   démarrer le reverse proxy lancé
-   modifier le fichier hosts
    -   sous linux(/macOs ?) /etc/hosts
    -   sous windows %WINDIR%\system32\driver\etc\hosts
-   modifier le `.env` pour refléter sa config locale
    -   le .env.example devrait être à jour.
-   et enfin démarrer le container **temperatures**
    -   `docker compose build && docker compose up -d`

### Production

La configuration de prod est radicalement différrente et seule le container du projet est lancé.
En prod :

-   Pas de phpmyadmin/adminer
-   Pas de mailcatcher
-   La BD est sur un cluster autre (renseigné dans la conf)

## License

Le projet Temperatures est un logiciel open-source sous license [MIT](https://opensource.org/licenses/MIT).
