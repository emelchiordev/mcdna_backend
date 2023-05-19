# mercadona_backend

Partie BACKEND de l'application MERCADONA

[Documentation Technique Mercadona.pdf](https://github.com/emelchiordev/mercadona_front/files/11517382/Documentation.Technique.Mercadona.pdf)


[Manuel utilisation Mercadona.pdf](https://github.com/emelchiordev/mercadona_front/files/11517384/Manuel.utilisation.Mercadona.pdf)


## Prérequis

Assurez-vous d'avoir les éléments suivants installés avant de commencer :

- PHP 8.1
- COMPOSER 2.5.5
- POSTGRESQL 15
- SYMFONY CLI 4.28.1

## ENVIRONNEMENT

- APP_ENV=(prod ou dev)
- APP_SECRET="secret"
- DATABASE_URL="postgresql://username:password@databasename:5432/app?serverVersion=15&charset=utf8"
- JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
- JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
- JWT_PASSPHRASE="PASSPHRASE"
- CORS_ALLOW_ORIGIN='adresse_autorisé'
- XDEBUG_MODE=coverage

## Installation

1. Clonez le dépôt :

git clone https://github.com/emelchiordev/mercadona_backend.git

2. Accédez au répertoire du projet :

cd mercadona_backend

3. Installez les dépendances :

composer install

## Installation de la base de donnée

- php bin/console doctrine:database:create
- php bin/console doctrine:migrations:migrate


## Utilisation (mode dev)

symfony serve
