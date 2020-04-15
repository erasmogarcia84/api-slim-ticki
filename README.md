# api-slim-ticki
API Rest - PHP Slim framework

This simple RESTful API, allows CRUD operations to manage resources like: Users, Tasks and Reports.

Main technologies used: PHP, Slim 3 & MySQL.

## Pre Requisites:

    Git.
    Composer.
    PHP.
    MySQL/MariaDB.

For a quick test, please run this command in the project root folder:
```bash
$ php -S localhost:8000
```

**Queries already implemented:**

```bash
# GET All Clients.
$ http://localhost:8000/public/api/tms/clientes

# GET Client Details.
$ http://localhost:8000/public/api/tms/clientes/{id}

# GET Expired Quotes.
$ http://localhost:8000/public/api/presupuestos/expirados
```
