<?php

namespace Victordrnd\Recaptcha;

use Illuminate\Container\Container;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use ReCaptcha\ReCaptcha;

/**
 * Class RecaptchaServiceProvider
 * @package Huangdijia\Recaptcha
 */
class RecaptchaServiceProvider extends ServiceProvider
{
    protected $defer = false;

    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        $this->bootConfig();


        // v3
        $this->app['validator']->extend('recaptcha', function ($attribute, $value, $parameters, $validator) {
            $app       = Container::getInstance();
            $recaptcha = $app['recaptcha-v3']->setExpectedHostname($app['request']->getHost());

            if ($parameters[0] ?? '') {
                $recaptcha->setExpectedAction($parameters[0]);
            }

            if ($parameters[1] ?? '') {
                $recaptcha->setScoreThreshold($parameters[1]);
            }

            return $recaptcha->verify($value, $app['request']->getClientIp())->isSuccess();
        });

        $this->app['validator']->extend('recaptcha-v3', function ($attribute, $value, $parameters, $validator) {
            $app       = Container::getInstance();
            $recaptcha = $app['recaptcha-v3']->setExpectedHostname($app['request']->getHost());

            if ($parameters[0] ?? '') {
                $recaptcha->setExpectedAction($parameters[0]);
            }

            if ($parameters[1] ?? '') {
                $recaptcha->setScoreThreshold($parameters[1]);
            }

            return $recaptcha->verify($value, $app['request']->getClientIp())->isSuccess();
        });


     
    }

    /**
     * Booting configure.
     */
    protected function bootConfig()
    {
        $configV3 = __DIR__ . '/../config/recaptcha-v3.php';

        $this->mergeConfigFrom($configV3, 'recaptcha-v3');

        if ($this->app->runningInConsole()) {
            $this->publishes([$configV3 => $this->app->basePath('config/recaptcha-v3.php')], 'config');
        }
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        // bind
        $this->app->bind('recaptcha-v3', function ($app) {
            return new ReCaptcha($app['config']['recaptcha-v3.secret_key']);
        });


        // alias
        $this->app->alias('recaptcha-v3', 'recaptcha');
    }

    /**
     * Get the services provided by the provider.
     * @return array
     */
    public function provides()
    {
        return [
            'recaptcha',
            'recaptcha-v3',
        ];
    }
}
