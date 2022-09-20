
Il pacchetto è ancora in fase di elaborazione!
# ***WORK IN PROGRESS***
# 1 Introduzione
Il presente pacchetto viene impiegato per impostare alcune funzionalità di background per lo sviluppo di applicazioni basate sul framework Laravel.
# 2 Installazione
Installazione del framework Laravel secondo la [guida](https://laravel.com/docs).
Installa il presente pacchetto:
```bash
composer require nabre/core
```
Successivamente procedere con la modifica dei file e l'esecuzione di alcuni comandi per completare l'installazione come previsto nei segunti sottocapitoli.
## 2.1 Modifica file Laravel
Editare i seguenti file di seguito elencati:
***bootstap/app.php***
```php
<?php

$app = new Illuminate\Foundation\Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    //App\Http\Kernel::class
    Nabre\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    //App\Console\Kernel::class
    Nabre\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    //App\Exceptions\Handler::class
    Nabre\Exceptions\Handler::class
);

return $app;
```
## 2.2 Database
Il presente pacchetto prevede l'impiego di una database ***MongoDB***.

### 2.2.0 Installazione
Il presente pacchetto si supporta del pacchetto ***jenssegers/laravel-mongodb***.
Seguire la guida per l'installazione e l'utilizzo delle sue funzionalità.

### 2.2.1 Aggiungere file Model
È necessario aggiungere i seguenti file Model nel percorso ***App\Models***:

***Permission.php***
```php
<?php

namespace App\Models;

use Nabre\Models\Permission as Original;

class Permission extends Original
{
}

```

***Role.php***
```php
<?php

namespace App\Models;

use Nabre\Models\Role as Original;

class Role extends Original
{
}

```

***User.php***
```php
<?php

namespace App\Models;

use Nabre\Models\User as Original;

class User extends Original
{
}

```

***UserContact.php***
```php
<?php

namespace App\Models;

use Nabre\Models\UserContact as Original;

class UserContact extends Original
{
}

```
### 2.2.2 Seeders
```bash
php artisan db:seed --class=Nabre\Database\Seeders\DatabaseSeeder 
```
## 2.3 NPM
```bash
npm install fortawesome/fontawesome-free
npm install flag-icons
npm install jquery
npm install livewire-sortable
```
***/webpack.mix.js***
```js
const mix = require('laravel-mix');

mix.js('vendor/nabre/core/resources/js/app.js', 'public/js')
   .sass('vendor/nabre/core/resources/sass/app.scss', 'public/css')
   .sourceMaps()
   .version();
```
```bash
npm run dev
```
# 3 Funzionalità
## 3.1 Route
## 3.2 Breadcrumbs
## 3.3 Form
## 3.4 Table
## 3.5 Template

# 4 Artisan
Il presente pacchetto prevede alcuni comandi artisan aggiuntivi per facilitare alcune oprazioni di gestione dell'applicazione.

| Comando           | Descrizione                                                |
| -------------     | -------------                                              |
| mongodb:dump      | Crea un fil di backup del database MongoDB impostato.      |
| mongodb:restore   | Ripristina l'ultimo file di backup presente nel DB MongoDB |
| nabrecore:install | Installa le impostazioni del presente pacchetto.           |
| page:install      | Vengono aggiunte le pagine presenti nell'applicazione.     |
