J'installe mon projet,

J'ai commencé par installer composer et scope afin de pouvoir lancer le projet Symfony

J'utilise la commande ```symfony new --webapp NOMDUDOSSIER``` afin de créer le projet

J'installe d'autres composants avec composer qui me serviront à rendre une structure et une architecture correct à chaques commit:

- PHPstan : 
    - Détecter des erreurs potentielles sans exécuter le code
    - ```vendor/bin/phpstan analyse```
- PHP-CS-Fixer :
    - Formater automatiquement ton code PHP selon la convention (pas de correction d'erreur, uniquement l'indentation)
    - ```php vendor/bin/php-cs-fixer fix src```


Ainsi que twig/inky-extra et twig/markdown-extra pour faire fonctionner les commandes :

- ```symfony console lint:twig```(vérifier la syntaxe des fichiers twig, je n'utilise pas turbo et stimulus dans symfony et pour éviter les erreurs liés à ça j'ai commenté les lignes qui font appels à eux dans config/bundles.php)

- ```symfony console lint:container```(Vérifier les containers, que les classes n'ont pas d'erreurs d'appelle dans le namespace ou de chemins)

- ```symfony console lint:yaml config``` (Vérifier les fichiers yalm)

 
- J'ai aussi installé Node.js pour pour pouvoir utiliser vue.JS (plus simple pour l'affichage de l'agenda)

- J'ai installé le package "symfonycasts/reset-password-bundle" avec composer ```composer require symfonycasts/reset-password-bundle```
C'est un package qui gère toute la logique derrière un changement de mot de pass (Génération de token, expiration, validation)

- ```composer require --dev doctrine/doctrine-fixtures-bundle``` pour installer des fixtures. Des comptes temporaires de testes qu'on fait avec ```symfony console make:fixtures```, cela crée le fichier de test que je remplis. Une fois remplie je lance ```symfony console doctrine:fixtures:load```

-```composer require symfonycasts/verify-email-bundle``` c'est le bundle qui gère la vérification d'email en envoyant un mail de vérification.
D'ailleurs j'ai beaucoup galérer pour un problème d'envoie d'email asynchrone via le formulaire et le bundle.
Le fichier ```messenger.yaml``` une ligne du routing envoyait des emails de manière asynchrone (Apparemment ajouté à une liste qui peut être rendu par la suite).
J'ai commenté la ligne pour que les envois soient automatique.

```composer require symfony/rate-limiter```
