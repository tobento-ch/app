<?php

/**
 * TOBENTO
 *
 * @copyright   Tobias Strub, TOBENTO
 * @license     MIT License, see LICENSE file distributed with this source code.
 * @author      Tobias Strub
 * @link        https://www.tobento.ch
 */

declare(strict_types=1);
 
namespace Tobento\App\Boot;

use Tobento\App\Boot;
use Tobento\Service\ErrorHandler\ErrorHandling as ErrorHandlingService;
use Tobento\Service\ErrorHandler\ThrowableHandlers;
use Tobento\Service\ErrorHandler\ThrowableHandlersInterface;
use Tobento\Service\ErrorHandler\AutowiringThrowableHandlerFactory;
use Tobento\Service\ErrorHandler\Handler;
use Tobento\Service\Config\ConfigInterface;

/**
 * ErrorHandling boot.
 */
class ErrorHandling extends Boot
{
    public const INFO = [
        'boot' => 'app error handling implementation',
    ];

    public const BOOT = [
        \Tobento\App\Boot\Config::class,
    ];
    
    public function boot(): void
    {
        if (! $this->app->has(ThrowableHandlersInterface::class)) {

            $throwableHandlers = new ThrowableHandlers(
                new AutowiringThrowableHandlerFactory($this->app->container())
            );
            
            if ($this->app->get(ConfigInterface::class)->get('app.debug', false)) {
                $throwableHandlers->add(Handler\Debug::class)->priority(1200);
            }

            $throwableHandlers->add(Handler\Errors::class)->priority(1000);
            
            $this->app->set(ThrowableHandlersInterface::class, $throwableHandlers);
        }
        
        (new ErrorHandlingService($this->app->get(ThrowableHandlersInterface::class)))->register();
    }
}