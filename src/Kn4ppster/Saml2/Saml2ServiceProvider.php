<?php
namespace Kn4ppster\Saml2;

use Config;
use Route;
use URL;
use Illuminate\Support\ServiceProvider;

class Saml2ServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('kn4ppster/laravel4-saml2');

        include __DIR__ . '/../../routes.php';
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app['saml2auth'] = $this->app->share(function ($app) {
            $config = Config::get('laravel4-saml2::saml_settings');

            if(empty($config['sp']['entityId'])){
                $config['sp']['entityId'] = URL::route('saml_metadata');
            }

            if(!empty($config['sp']['assertionConsumerService']['url'])){
                $config['sp']['assertionConsumerService']['url'] = str_replace("http://","https://",URL::route('saml_acs'));
            }

            if(empty($config['sp']['singleLogoutService']['url'])){
                $config['sp']['singleLogoutService']['url'] = URL::route('saml_sls');
            }

            return new \Kn4ppster\Saml2\Saml2Auth($config);
        });

        $this->app->booting(function () {
            $loader = \Illuminate\Foundation\AliasLoader::getInstance();
            $loader->alias('Saml2Auth', 'Kn4ppster\Saml2\Facades\Saml2Auth');
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }

}
