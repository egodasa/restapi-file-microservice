# RestAPI File Management as Microservice
1. PHP as programming language
1. Sqlite3 as database
1. Made with `Lumen` Microframework

# Requirement
1. PHP >= 5.6
1. `sqlite` and `pdo` extension

# Quick Start
1. Clone this repository
1. Run `composer install`
1. Run migration file `php database_migration.php`
1. Read config file at `config.json` and learn it
1. Make a `POST` request to `/add` with `form-data` included files to be uploaded with `name` listed on `config.json` at `FORM_INPUT` configuration. <br> Response return detail file with `image ID` or `id`
1. Make a `GET` request to `/info/{id}` with file `id`
1. Make a `GET` request to `/permanent/{id}` with file `id`
1. Make a `GET` request to `/delete/{id}` with file `id`
1. Make a `GET` request to `/download/{id}/{name}` with file `id` and custom `name` file
1. Run `cronjob.php` to delete file except for `permanent` file. Run this file with cronjob with specific period.
