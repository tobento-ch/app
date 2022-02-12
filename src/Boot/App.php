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
use Tobento\Service\HelperFunction\Functions;
use Psr\Container\ContainerInterface;

/**
 * App boot.
 */
class App extends Boot
{
    public const INFO = [
        'boot' => [
            'loads app config file',
            'sets app environment based on app config',
            'adds specific config directory for environment',
            'sets timezone based on app config',
            'helper functions',
            'boots the specified boots from app config',
        ],
    ];
    
    public const BOOT = [
        \Tobento\App\Boot\Config::class,
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
        
        // Set the timezone.
        date_default_timezone_set($config->get('app.timezone', 'Europe/Berlin'));
        
        // Helper Functions.
        $this->app->get(Functions::class)->set(ContainerInterface::class, $this->app->container());
        
        $this->app->addMacro('functions', [$this->app->get(Functions::class), 'register']);
        
        // Boot.
        $this->app->boot(...$config->get('app.boots', []));
    }
}