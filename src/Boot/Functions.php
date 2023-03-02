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
use Tobento\Service\HelperFunction\Functions as HelperFunctions;
use Psr\Container\ContainerInterface;

/**
 * Functions boot.
 */
class Functions extends Boot
{
    public const INFO = [
        'boot' => 'App helper functions',
    ];
    
    /**
     * Boot application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $functions = $this->app->get(HelperFunctions::class);
        
        // Set container:
        $functions->set(ContainerInterface::class, $this->app->container());
        
        // Register app functions:
        $functions->register(__DIR__.'/../functions.php');
        
        // App macro functions:
        $this->app->addMacro('functions', [$this, 'register']);
    }
    
    /**
     * Registers a function.
     *
     * @param string $functionFile
     * @return void
     */
    public function register(string $functionFile): void
    {
        $this->app->get(HelperFunctions::class)->register($functionFile);
    }
}