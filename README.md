
# ***WORK IN PROGRESS***
Il pacchetto è ancora in fase di elaborazione!
# Indice dei contenuti
| § | Argomento |
| :--- | :--- |
|1.  |[Introduzione](#1-introduzione) |
|2.  |[Installazione](#2-installazione)|


# 1 Introduzione
Il presente pacchetto viene impiegato per impostare alcune funzionalità di per lo sviluppo di applicazioni basate sul framework Laravel.<br>
Si prevede l'impiego di un database MongoDB.
# 2 Installazione
## 2.1 Framework Laravel
Installazione del framework secondo la [guida](https://laravel.com/docs).
```
composer create-project laravel/laravel example-app
```
## 2.2 Modifica file
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
## 2.3 Aggiungere file Model
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
## 2.2 Database
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

## 2.4 Installa il presente pacchetto:
```bash
composer require nabre/core
```

## 2.5 Seeders
Popolare il databse con *collections* e *documents* necessari richiamare il seguente comando:
```bash
php artisan db:seed --class=Nabre\Database\Seeders\DatabaseSeeder 
```

## 2.6 NPM
Si utilizzano i seguenti pacchetti [NPM(Node Package Manager)](https://docs.npmjs.com/) da installare:
```bash
npm install fortawesome/fontawesome-free
npm install flag-icons
npm install jquery
npm install livewire-sortable
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
Quando si utilizza la procedura descritta al §2.2.2 viene genrato un account predefinito con la possibilità di accesso ad ogni parte dell'applicazione con le seguenti credenziali:
| **Nome utente:**  | admin@admin.test |
| ---               | ---              |
| **Password:**     | password         |

## 3.3 Ruoli & permessi
L'applicazione si basa sul pacchetto [***mostafamaklad/laravel-permission-mongodb***](https://guthub.com/mostafamaklad/laravel-permission-mongodb) per gestire i ruoli e permessi.
Consultare la guida per comprendere come integrarlo nella propia applicazione.
Nella presente applicazione è stato integrato un un sistema di ruoli gerarchico in funzione di una priorità definita, dove chi ha un valore minore può accedere a ruoli con priorità di valore maggiore.
In modo predefinito l'applicazione i seguenti ruoli con le rispettive priorità:
| Priorità | Ruolo     | Descrizione |
| ---:     | :---       | :--- |
| 1]       | *builder* | Vincolante, l'applicazione cerca il presente ruolo per poter definire l'accessibilità a tutti gli ambienti possibili e è definito nelle `Route` |
| 2]       | *admin*   | Utilizzato nelle `Route` |
| 3]       | *manage*  | Utilizzato nelle `Route` |

Per aggiornare i ruoli e permessi adottati nei middleware delle route utilizzare il seguente comando:
```bash
php artisan roles:update
```
## 3.4 Route
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
| nabrecore:install | Installa le impostazioni del presente pacchetto.                  |
| page:install      | Vengono aggiunte le pagine presenti nell'applicazione.            |
| roles:update      | Aggiorna ruoli e permessi utilizzati nel middleware delle route   |
