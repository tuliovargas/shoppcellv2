<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Sobre Projeto

LojaPDV é um sistema feito para loja de informática, para venda de produtos e controle de estoque.

> Configure o BD com o SQL MariaBD versão 10;
> Renomeie o arquivo **.env.exemple** e atualize o arquivo para **.env** e insira os dados do BD.

Após isso, dê os seguintes comandos
```javascript
composer install
npm install
php artisan key:generate
php artisan migrate --seed
npm run dev
```
daí o projeto já deve rodar, senão então rode no 
```javascript
php artisan serve
```
Telas do projeto: https://www.figma.com/file/WvpobjeIF7ctgF4bIhzX5S/

## Docker-compose
```shell
docker-compose up -d

docker-compose -f docker-compose-prod.yaml up -d
```

## Backup Google
Deve criar um arquivo com os dados do projeto do google o nome do arquivo deve ser nomeado ```client_secret.json```

E popular o arquivo ```.env``` com as váriáveis do Drive
``REFRESH_TOKEN`` e  ``BACKUP_FOLDER``
