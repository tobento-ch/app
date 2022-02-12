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
use Tobento\Service\Config\Config as Configuration;
use Tobento\Service\Config\ConfigInterface;
use Tobento\Service\Config\PhpLoader;
use Tobento\Service\Config\ConfigLoadException;
use Tobento\Service\Collection\Translations;

/**
 * Config boot.
 */
class Config extends Boot
{
    public const INFO = [
        'boot' => 'implements '.ConfigInterface::class,
    ];
    
    /**
     * Boot application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->app->set(ConfigInterface::class, function() {
            
            $trans = new Translations();
            
            $config = new Configuration($trans);

            $config->addLoader(
                new PhpLoader($this->app->dirs()->sort()->group('config'))
            );
            
            return $config;
        });
        
        $this->app->addMacro('config', [$this->app->get(ConfigInterface::class), 'get']);
    }
}