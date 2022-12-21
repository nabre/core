<?php

namespace Nabre\Providers;

use Nabre\Http\Middleware\LocalizationMiddleware;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\ServiceProvider as Sp;
use Nabre\Http\Middleware\DisabledPagesMiddleware;
use Nabre\Http\Middleware\ImpersonateMiddleware;
use Nabre\Http\Middleware\SettingAutoSaveMiddleware;
use Nabre\Http\Middleware\SettingOverrideMiddleware;
use Nabre\Setting\Facade;
use Nabre\Setting\Manager;
use Blade;
use Nabre\Console\Commands\MongoDB\DumpCommand as MongoDBDumpCommand;
use Nabre\Console\Commands\MongoDB\RestoreCommand as MOngoDBRestoreCommand;
use Nabre\Console\Commands\Page\InstallCommand as PageInstallCommand;
use Nabre\Console\Commands\Roles\UpdateCommand as RolesUpdateCommand;
use Nabre\Http\Middleware\DefaultAccountMiddleware;

class AppServiceProvider extends Sp
{
    public $bindings = [
        \Illuminate\Routing\ResourceRegistrar::class => \Nabre\Routing\ResourceRegistrar::class,
    ];

    public function register()
    {
        $this->app->register(GlobalFunctionsServiceProvider::class);
        $this->app->register(MacroServiceProvider::class);
        $this->app->register(AuthServiceProvider::class);
        $this->app->register(ObserverServiceProvider::class);
        $this->app->register(LivewireServiceProvider::class);
        $this->app->register(\Collective\Html\HtmlServiceProvider::class);
        $this->app->register(EventServiceProvider::class);

        //Setting
        $this->app->singleton('setting.manager', function ($app) {
            return new Manager($app);
        });

        $this->app->singleton('setting', function ($app) {
            return $app['setting.manager']->driver();
        });

        //Config
        $this->mergeConfigFrom(__DIR__ . '/../../config/auth.php', 'auth');
        $this->mergeConfigFrom(__DIR__ . '/../../config/breadcrumbs.php', 'breadcrumbs');
        $this->mergeConfigFrom(__DIR__ . '/../../config/database.php', 'database');
        $this->mergeConfigFrom(__DIR__ . '/../../config/pages.php', 'pages');
        $this->mergeConfigFrom(__DIR__ . '/../../config/setting.php', 'setting');
    }

    public function boot(\Illuminate\Routing\Router $router, \Illuminate\Contracts\Http\Kernel $kernel)
    {
        //Middleware
        $router->aliasMiddleware('role', \Maklad\Permission\Middlewares\RoleMiddleware::class);
        $router->aliasMiddleware('permission', \Maklad\Permission\Middlewares\PermissionMiddleware::class);
        $router->aliasMiddleware('registration', \Nabre\Http\Middleware\RegisterPagesMiddleware::class);
        $router->pushMiddlewareToGroup('web', DisabledPagesMiddleware::class);
        $router->pushMiddlewareToGroup('web', LocalizationMiddleware::class);
        $kernel->pushMiddleware(StartSession::class);
        $kernel->pushMiddleware(ImpersonateMiddleware::class);
        $kernel->pushMiddleware(DefaultAccountMiddleware::class);
        $kernel->pushMiddleware(SettingOverrideMiddleware::class);

        //disk
        $this->app->config["filesystems.disks.backup"] = [
            'driver' => 'local',
            'root' => storage_path('backup'),
        ];

        //view
        $dir_views_package   = __DIR__ . '/../../resources/views';
        $dir_views_resources = base_path('/resources/views');
        $this->loadViewsFrom($dir_views_resources, 'Nabre');
        $this->loadViewsFrom($dir_views_package, 'Nabre');
        $this->publishes([$dir_views_package => $dir_views_resources], 'views');

        //routes
        $this->loadRoutesFrom(__DIR__ . '/../../routes/web.php');

        //Translation
        $this->loadTranslationsFrom(__DIR__ . '/../../lang', 'Nabre');
        //$this->publishes([__DIR__ . '/../../lang'=>base_path('/lang')],'lang');

        //Commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                MongoDBDumpCommand::class,
                MongoDBRestoreCommand::class,
                PageInstallCommand::class,
                RolesUpdateCommand::class,
            ]);
        }

        // Facades
        $this->app->singleton('Form', function () {
            return new \Collective\Html\FormFacade();
        });
        $this->app->singleton('Html', function () {
            return new \Collective\Html\HtmlFacade();
        });
        $this->app->singleton('Setting', function () {
            return new Facade();
        });


        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        //Setting
        $this->publishes([
            __DIR__ . '/../../config/breadcrumbs.php' => config_path('breadcrumbs.php'),
            __DIR__ . '/../../config/setting.php' => config_path('setting.php'),
        ], 'setting');

        if (config('setting.auto_save')) {
            $kernel->pushMiddleware(SettingAutoSaveMiddleware::class);
        }

        Blade::directive('setting', function ($expression) {
            return "<?php echo setting($expression); ?>";
        });


    }
}
