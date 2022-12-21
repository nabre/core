
# ***WORK IN PROGRESS***
Il pacchetto è ancora in fase di elaborazione!
# Indice dei contenuti
| § | Argomento |
| :--- | :--- |
|1.  |[Introduzione](#1-introduzione) |
|2.  |[Installazione](#2-installazione)|


# 1 Introduzione
Il presente pacchetto viene impiegato per impostare alcune funzionalità di per lo sviluppo di applicazioni basate sul framework Laravel.

Si prevede l'impiego di un database MongoDB.
# 2 Installazione
## 2.1 Framework Laravel
Installazione del framework secondo la [guida](https://laravel.com/docs).
```
composer create-project laravel/laravel example-app
```
## 2.2 Preparazione dei file 
## Modifica file
Editare il file
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
## Aggiungere file Model
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
## Modifica file /routes/web.php
```php
<?php

use Illuminate\Support\Facades\Route;

```
Verifficare che la chiamata "/" non sia definita.

## Modifica il file ***config/permission.php***
```php
<?php

return [

    'models' => [
        'permission' => App\Models\Permission::class,
        'role' => App\Models\Role::class,
    ],

    'collection_names' => [
        'roles' => 'roles',
        'permissions' => 'permissions',
    ],

    'cache_expiration_time' => 60 * 24,

    'log_registration_exception' => true,

    'display_permission_in_exception' => false,
];
```

## Modifica il file ***config/app.php***
Aggiungi le lingue dell'applicazione selezionabili.
```php
    'available_locales' => [/*'English' => 'en', 'Deutsch' => 'de', 'Français' => 'fr',*/ 'Italiano' => 'it'],
```

## Database
Si utilizza un database ***MongoDB*** in riferimento al pacchetto [***jenssegers/laravel-mongodb***](https://github.com/jenssegers/laravel-mongodb).

Modifica il file ***config/database.php***:
aggiungi nelle *connections* il seguente codice.

```php
'mongodb' => [
    'driver' => 'mongodb',
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => env('DB_PORT', 27017),
    'database' => env('DB_DATABASE', 'homestead'),
    'username' => env('DB_USERNAME', 'homestead'),
    'password' => env('DB_PASSWORD', 'secret'),
    'options' => [
        'database' => env('DB_AUTHENTICATION_DATABASE', 'admin'), // required with Mongo 3+
    ],
],
```
modifica il file ***.env*** aggiungendo e compilando i seguenti parametri:
```
DB_CONNECTION=mongodb
DB_HOST= 
DB_DATABASE= 
DB_USERNAME= 
DB_PASSWORD=
DB_AUTHENTICATION_DATABASE=admin
```

## 2.3 Installa il presente pacchetto:
```bash
composer require nabre/core
```

## 2.4 Seeders
Popolare il databse con *collections* e *documents* di base richiamare il seguente comando:
```bash
php artisan db:seed --class=Nabre\Database\Seeders\DatabaseSeeder 
```

## 2.5 NPM
Si utilizzano i seguenti pacchetti [NPM(Node Package Manager)](https://docs.npmjs.com/) da installare:
```bash
npm install @fortawesome/fontawesome-free
npm install bootstrap
npm install flag-icons
npm install jquery
npm install jquery-ui
npm install livewire-sortable

```
Compilatore npm con mix<br>
Controllare il metodo definito e installare nel caso.
```bash
npm install laravel-mix --save-dev
```
modicare ***/package.json***
```json
"scripts": {
    "dev": "npm run development",
    "development": "mix",
    "watch": "mix watch",
    "watch-poll": "mix watch -- --watch-options-poll=1000",
    "hot": "mix watch --hot",
    "prod": "npm run production",
    "production": "mix --production"
}
```
Creare/modificare il seguente file:
***/webpack.mix.js***

```js
const mix = require('laravel-mix');

mix.js('vendor/nabre/core/resources/js/app.js', 'public/js')
   .sass('vendor/nabre/core/resources/sass/app.scss', 'public/css')
   .sourceMaps()
   .version();
```
Aggiornare i files ***public/js/app.js*** e ***public/css/app.css*** eseguendo il comando:
```bash
npm run dev
```
# 3 Funzionalità
## 3.1 Ambienti predefiniti
Il sistema di gestione prevede i seguenti ambienti di base con predefinte alcune funzionalità di gestione.

| Uri principale    | Descrizione                                                                           |
| ---               | ---                                                                                   |
| *user/*            | percorso dove si ritrova la gestione del proprio profilo dopo l'esecuzione del login. |
| *manage/*          | Definite le pagine per la gestione delle funzionalità operative dell'applicazione.    |
| *admin/*           | Pannello amministrativo dell'applicazione.                                            |
| *admin/builder/*   | Pagine dedicate alla costruzione di alcune parti generali dell'applicazione.          |

## 3.2 Account
L'account predefinito, dopo aver popolato per la prima volta il database (funzione artisan db:seed specifico), prevedere le seguenti credenziali:

| **Nome utente:**  | admin@account.test    |
| ---               | ---                   |
| **Password:**     | password              |

## 3.3 Ruoli & permessi
L'applicazione si basa sul pacchetto [***mostafamaklad/laravel-permission-mongodb***](https://guthub.com/mostafamaklad/laravel-permission-mongodb) per gestire i ruoli e permessi.<br>
Consultare la guida per comprendere come integrarlo nella propia applicazione.

Nella presente applicazione è stato integrato un un sistema di ruoli gerarchico in funzione di una priorità definita, dove chi ha un valore minore può accedere a ruoli con priorità di valore maggiore.

In modo predefinito l'applicazione i seguenti ruoli con le rispettive priorità:
| Priorità | Ruolo     | Descrizione |
| ---:     | :---       | :--- |
| 1]       | *builder* | Vincolante, l'applicazione cerca il presente ruolo per poter definire l'accessibilità a tutti gli ambienti possibili e è definito nelle `Route` |
| 2]       | *admin*   | Utilizzato nelle `Route` |
| 3]       | *manage*  | Utilizzato nelle `Route` |

Qualora nelle `Route::middleware()` vengono aggiunti ruoli o permessi, questi possono essere aggiornati automaticamente nel database utilizzando il seguente comando:
```bash
php artisan roles:update
```
## 3.4 Route
Il comando `Route::resource()` è stato modificato nel seguente aggiungendo pagine da generare automaticamente.

È stato implementato un sistema delle funzioni `only()` & `exept()`.
## 3.5 Breadcrumbs
L'applicazione genera i *breadcrumbs* basandosi sul percorso di chiamata impostato.
Vengono riconosciute le pagine con suffisso **.index** come pagine generate dalla funzione `Route::resource()` e quindi nidifica conseguentemente i suffissi complementari della funzione resource; **.edit**, **.view**, **.create**.
## 3.6 Menu
### 3.6.1 Formato
#### 3.6.1.1 Automatico
L'appicazione di basa sul percorso di chiamata iniziale per caricare tutti i percorsi "figli", secondo le regole di *breadcrumbs*.
#### 3.6.1.2 Manuale
È possibile aggiungere manualmente le pagine per creare il proprio menu personalizzato ad un livello.
### 3.6.2 Impostazioni
| Parametro | Descrizione |
| --- | --- |
| icona | visualizza (sì/no) l'icona della pagina
| Testo | visualizza (sì/no) il titolo della pagina
## 3.7 Form
### 3.7.1 Impostazioni base
### 3.7.2 Tipi di input
### 3.7.3 Request
### 3.7.4 Relazioni; EmbedsOne & EmbedsMany
## 3.8 Table
### 3.8.1 Impostazioni base
### 3.8.2 Policy
### 3.8.3 Colonne personalizzate
## 3.9 Template

# 4 Artisan
Il presente pacchetto prevede alcuni comandi artisan aggiuntivi per facilitare alcune oprazioni di gestione dell'applicazione.

| Comando           | Descrizione                                                       |
| -------------     | -------------                                                     |
| mongodb:dump      | Crea un fil di backup del database MongoDB impostato.             |
| mongodb:restore   | Ripristina l'ultimo file di backup presente nel DB MongoDB        |
| page:install      | Vengono aggiunte le pagine presenti nell'applicazione.            |
| roles:update      | Aggiorna ruoli e permessi utilizzati nel middleware delle route   |
