Birdwatch Backend
=================

Birdwatch is a backend API for tracking bird sightings, categorizing birds, and awarding points based on observed species.
Built with Laravel Framework 12.47.0, PostgreSQL@16, Docker, and a test-first approach.

TECH STACK
----------
- PHP 8.4
- Laravel 12
- PostgreSQL
- Docker / Docker Compose
- Laravel Pint
- PHPUnit
- GitHub Actions

GETTING STARTED
---------------
git clone git@github.com:iankivaiuliia/birdwatch.git
cd birdwatch/app

ENVIRONMENT
-----------
cp .env.example .env
cp .env.testing.example .env.testing

docker compose up -d
docker exec -it birdwatch_app composer install
docker exec -it birdwatch_app php artisan migrate

TESTING
-------
docker exec -it birdwatch_app php artisan test

CODE STYLE
----------
docker exec -it birdwatch_app ./vendor/bin/pint --test

AUTHOR
------
Iuliia Iankiva
