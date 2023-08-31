<?php

/**
 * TOBENTO
 *
 * @copyright    Tobias Strub, TOBENTO
 * @license     MIT License, see LICENSE file distributed with this source code.
 * @author      Tobias Strub
 * @link        https://www.tobento.ch
 */

declare(strict_types=1);
 
namespace Tobento\App\Boot;

use Tobento\App\Boot;
use Tobento\Service\Config\ConfigInterface;
use Tobento\Service\Config\PhpLoader;
use Tobento\Service\Config\ConfigLoadException;
use Tobento\Service\Clock\SystemClock;

/**
 * App boot.
 */
class App extends Boot
{
    public const INFO = [
        'boot' => [
            'boots config and functions boot',
            'loads app config file if exists',
            'sets app environment based on app config',
            'adds specific config directory for environment',
            'sets timezone based on app config',
            'boots the specified boots from app config',
        ],
    ];
    
    public const BOOT = [
        \Tobento\App\Boot\Config::class,
        \Tobento\App\Boot\Functions::class,
    ];
    
    /**
     * Boot application services.
     *
     * @return void
     */
    public function boot(): void
    {
        // Load the app configuration.
        $config = $this->app->get(ConfigInterface::class);
        
        try {
            $config->load('app.php', 'app');
        } catch (ConfigLoadException $e) {
            //
        }
                
        // Set the app environment.
        $this->app->setEnvironment(
            $config->get('app.environment', $this->app->getEnvironment())
        );
        
        // Load environment specific config.
        if (
            $this->app->getEnvironment() !== 'production'
            && ! $this->app->dirs()->has('config.'.$this->app->getEnvironment())
        ) {
            $this->app->dirs()->dir(
                dir: $this->app->dir('config').$this->app->getEnvironment(),
                name: 'config.'.$this->app->getEnvironment(),
                group: 'config',
                priority: 15,
            );
            
            $config->addLoader(
                new PhpLoader($this->app->dirs()->sort()->group('config'))
            );
            
            $this->boot();
            return;
        }
        
        // Handle timezone:
        $timezone = $config->get('app.timezone', 'Europe/Berlin');
        
        date_default_timezone_set($timezone);

        $this->app->setClock(new SystemClock(timezone: $timezone));
        
        // Boot.
        $this->app->boot(...$config->get('app.boots', []));
    }
}