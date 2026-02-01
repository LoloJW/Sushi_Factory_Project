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