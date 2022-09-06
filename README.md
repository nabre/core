# nabre/core
## Introduzione
Il presente pacchetto viene impiegato per impostare alcune funzionalità di background per lo sviluppo di applicazioni basate sul framework Laravel.
## Installazione
Installazione del framework Laravel secondo la [guida](https://laravel.com/docs).

Installa il presente pacchetto:
```bash
composer require nabre/core
```

Procedere con la modifica dei file di Laravel elencati nel capitolo successivo.
### File Laravel

**bootstap/app.php**
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

### MongoDB

## Funzionalità
### Route
### Breadcrumbs
### Form
### Table
### Template

## Artisan
Comandi aggiuntivi: