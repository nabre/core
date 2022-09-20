
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
Per aggiungere gli elementi mini nel database per poter inizare ad utilizzare il pacchetto richiamare il seguente comando:
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
# 3 Pannello di controllo
Il pacchetto prevede tre ambienti di base con predefinte alcune funzionalità di gestione.

| Uri principale    | Descrizione                                                                           |
| ---               | ---                                                                                   |
| *user*            | percorso dove si ritrova la gestione del proprio profilo dopo l'esecuzione del login. |
| *manage*          | Definite le pagine per la gestione delle funzionalità operative dell'applicazione.    |
| *admin*           | Pannello amministrativo dell'applicazione.                                            |
| *admin/builder*   | Pagine dedicate alla costruzione di alcune parti generali dell'applicazione.          |

# 4 Funzionalità
## 4.1 Account
Quando si utilizza la procedura descritta al §2.2.2 viene genrato un account predefinito con la possibilità di accesso ad ogni parte dell'applicazione con le seguenti credenziali:
| **Nome utente:**  | admin@admin.test |
| ---               | ---              |
| **Password:**     | password         |
## 4.2 Route
## 4.3 Menu
## 4.4 Breadcrumbs
## 4.5 Form
## 4.6 Table
## 4.7 Template

# 5 Artisan
Il presente pacchetto prevede alcuni comandi artisan aggiuntivi per facilitare alcune oprazioni di gestione dell'applicazione.

| Comando           | Descrizione                                                |
| -------------     | -------------                                              |
| mongodb:dump      | Crea un fil di backup del database MongoDB impostato.      |
| mongodb:restore   | Ripristina l'ultimo file di backup presente nel DB MongoDB |
| nabrecore:install | Installa le impostazioni del presente pacchetto.           |
| page:install      | Vengono aggiunte le pagine presenti nell'applicazione.     |
