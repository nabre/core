
# ***WORK IN PROGRESS***
Il pacchetto è ancora in fase di elaborazione!
# Indice dei contenuti
| § | Argomento |
| :--- | :--- |
|1.  |[Introduzione](#1-introduzione) |
|2.  |[Installazione](#2-installazione)|


# 1 Introduzione
Il presente pacchetto viene impiegato per impostare alcune funzionalità di background per lo sviluppo di applicazioni basate sul framework Laravel.
# 2 Installazione
## 2.1 Framework Laravel
Installazione del framework secondo la [guida](https://laravel.com/docs).
## 2.2 Modifica file
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
Il presente pacchetto prevede l'impiego di una database ***MongoDB*** facendo riferimento al pacchetto [***jenssegers/laravel-mongodb***](https://github.com/jenssegers/laravel-mongodb).

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
modifica il file ***.env***:
```
DB_CONNECTION=mongodb
DB_HOST= 
DB_DATABASE= 
DB_USERNAME= 
DB_PASSWORD=
```

## 2.4 Installa il presente pacchetto:
```bash
composer require nabre/core
```

## 2.5 Seeders
Per aggiungere gli elementi mini nel database per poter inizare ad utilizzare il pacchetto richiamare il seguente comando:
```bash
php artisan db:seed --class=Nabre\Database\Seeders\DatabaseSeeder 
```

### 2.2.1 Installazione

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
## 3.1 Ambienti predefiniti
Il pacchetto prevede i seguenti ambienti di base con predefinte alcune funzionalità di gestione.

| Uri principale    | Descrizione                                                                           |
| ---               | ---                                                                                   |
| *user*            | percorso dove si ritrova la gestione del proprio profilo dopo l'esecuzione del login. |
| *manage*          | Definite le pagine per la gestione delle funzionalità operative dell'applicazione.    |
| *admin*           | Pannello amministrativo dell'applicazione.                                            |
| *admin/builder*   | Pagine dedicate alla costruzione di alcune parti generali dell'applicazione.          |
## 3.2 Pagine: Route e "folder"
Il pacchetto prevede una gerarchia automatizzata dei files secondo il percorso uri definito nella funzione Route.<br>
Questo semplifica la gestione delle Breadcrumbs (cfr. [§4.4](#44-breadcrumbs)) e vengono preimpostate alcune pagine per la gestione dell'applicazione.
# 4 Funzionalità
## 4.1 Account
Quando si utilizza la procedura descritta al §2.2.2 viene genrato un account predefinito con la possibilità di accesso ad ogni parte dell'applicazione con le seguenti credenziali:
| **Nome utente:**  | admin@admin.test |
| ---               | ---              |
| **Password:**     | password         |
## 4.2 Ruoli & permessi
L'applicazione si basa sul pacchetto [***mostafamaklad/laravel-permission-mongodb***](https://guthub.com/mostafamaklad/laravel-permission-mongodb) per gestire i ruoli e permessi.
Consultare la guida per comprendere come integrarlo nella propia applicazione.
Nella presente applicazione è stato integrato un un sistema di ruoli gerarchico in funzione di una priorità definita, dove chi ha un valore minore può accedere a ruoli con priorità di valore maggiore.
In modo predefinito l'applicazione i seguenti ruoli con le rispettive priorità:
| Priorità | Ruolo     | Descrizione |
| ---:     | ---       | --- |
| 1]       | *builder* | Vincolante, l'applicazione cerca il presente ruolo per poter definire l'accessibilità a tutti gli ambienti possibili |
| 2]       | *admin*   | Utilizzato nelle `Route` del pacchetto |
| 3]       | *manage*  | Utilizzato nelle `Route` del pacchetto |

Per aggiornare i ruoli e permessi adottati nei middleware delle route utilizzare il seguente comando:
```bash
php artisan roles:update
```
## 4.3 Route
## 4.4 Breadcrumbs
L'applicazione genera i *breadcrumbs* basandosi sul percorso di chiamata impostato.
Vengono riconosciute le pagine con suffisso **.index** come pagine generate dalla funzione `Route::resource()` e quindi nidifica conseguentemente i suffissi complementare della funzione resource; **.edit**, **.view**, **.create**.
## 4.5 Menu
### 4.5.1 Formato
#### 4.5.1.1 Automatico
L'appicazione di basa sul percorso di chiamata iniziale per caricare tutti i percorsi "figli", secondo le regole di *breadcrumbs*.
#### 4.5.1.2 Manuale
È possibile aggiungere manualmente le pagine per creare il proprio menu personalizzato ad un livello.
### 4.5.3 Impostazioni
| Parametro | Descrizione |
| --- | --- |
| icona | visualizza (sì/no) l'icona della pagina
| Testo | visualizza (sì/no) il titolo della pagina
## 4.6 Form
### 4.6.1 Impostazioni base
### 4.6.2 Tipi di input
### 4.6.3 Request
### 4.6.4 Relazioni; EmbedsOne & EmbedsMany
## 4.7 Table
### 4.7.1 Impostazioni base
### 4.7.2 Policy
### 4.7.3 Colonne personalizzate
## 4.8 Template

# 5 Artisan
Il presente pacchetto prevede alcuni comandi artisan aggiuntivi per facilitare alcune oprazioni di gestione dell'applicazione.

| Comando           | Descrizione                                                       |
| -------------     | -------------                                                     |
| mongodb:dump      | Crea un fil di backup del database MongoDB impostato.             |
| mongodb:restore   | Ripristina l'ultimo file di backup presente nel DB MongoDB        |
| nabrecore:install | Installa le impostazioni del presente pacchetto.                  |
| page:install      | Vengono aggiunte le pagine presenti nell'applicazione.            |
| roles:update      | Aggiorna ruoli e permessi utilizzati nel middleware delle route   |
