## Triangle Skeleton (Demo Application) 

The "Triangle Skeleton" is a reference application created to show how
to develop applications following the Symfony Best Practices.

Requirements
------------

* PHP 8.0.2 or higher
* Node.js
* PDO-SQLite PHP extension enabled
* Symfony [local web server](https://symfony.com/doc/current/setup/symfony_server.html) 
for the local development 



Installation
------------

```bash
$ git clone https://github.com/trngl8/triangle-skeleton-demo.git my_project
$ cd my_project/
$ composer install 
$ npm install
$ yarn install
```

## Set up database
```bash
$ php bin/console doctrine:database:create 
$ php bin/console doctrine:schema:create
```

## Run application
```bash
$ yarn dev
$ symfony server:start
```

You may use application on http://127.0.0.1:8000

Users manipulation
--------
List:
```bash
$ php bin/console app:user:list 
```


Creation:
```bash
$ php bin/console app:user:add 
```
