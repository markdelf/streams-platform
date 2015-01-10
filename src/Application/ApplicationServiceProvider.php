<?php namespace Anomaly\Streams\Platform\Application;

use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Support\ServiceProvider;

/**
 * Class ApplicationServiceProvider
 *
 * @link    http://anomaly.is/streams-platform
 * @author  AnomalyLabs, Inc. <hello@anomaly.is>
 * @author  Ryan Thompson <ryan@anomaly.is>
 * @package Anomaly\Streams\Platform\Application
 */
class ApplicationServiceProvider extends ServiceProvider
{

    use DispatchesCommands;

    /**
     * Boot the service provider.
     */
    public function boot()
    {
        $this->setCommandBusMapper();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerApplication();
        $this->registerListeners();
        $this->configurePackages();
    }

    /**
     * Register the application.
     */
    protected function registerApplication()
    {
        $this->app->instance('streams.application', app('Anomaly\Streams\Platform\Application\Application'));

        $this->app->instance('streams.path', base_path('vendor/anomaly/streams-platform'));

        $this->app['config']->set(
            'streams::config',
            $this->app['files']->getRequire(__DIR__ . '/../../resources/config/config.php')
        );

        $this->app->make('view')->addNamespace('streams', $this->app['streams.path'] . '/resources/views');

        if (file_exists(base_path('config/distribution.php'))) {

            app('streams.application')->locate();

            if (file_exists(base_path('config/database.php'))) {

                app('streams.application')->setup();
            }
        }
    }

    /**
     * Register the application listener.
     */
    protected function registerListeners()
    {
        $this->app->make('events')->listen(
            'streams::application.booting',
            'Anomaly\Streams\Platform\Application\Listener\ApplicationBootingListener'
        );
    }

    /**
     * Manually configure 3rd party packages.
     */
    protected function configurePackages()
    {
        // Configure Translatable
        $this->app->make('config')->set('translatable::locales', ['en', 'es']);
        $this->app->make('config')->set('translatable::translation_suffix', 'Translation');

        // Bind a string loader version of twig.
        $this->app->bind(
            'twig.string',
            function () {
                $twig = clone(app('twig'));

                $twig->setLoader(new \Twig_Loader_String());

                return $twig;
            }
        );
    }

    /**
     * Use a custom mapper for commands.
     */
    protected function setCommandBusMapper()
    {
        // Set the default command mapper.
        $this->app->make('Illuminate\Bus\Dispatcher')->mapUsing(
            function ($command) {
                return get_class($command) . 'Handler@handle';
            }
        );
    }
}
