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
    
    /**
     * @psalm-suppress InvalidArgument
     * @psalm-suppress UnusedClosureParam
     */
    public function terminate(): void
    {
        if (str_contains($_SERVER['argv'][0] ?? '', 'phpunit')) {
            // exception handlers:
            $res = [];

            while (true) {
                $previousHandler = set_exception_handler(static fn () => null);
                restore_exception_handler();
                
                if (
                    is_array($previousHandler)
                    && $previousHandler[0] instanceof \Tobento\Service\ErrorHandler\ErrorHandling
                    && $previousHandler[1] === 'handleException'
                ) {
                    restore_exception_handler();
                    continue;
                }

                if ($previousHandler === null) {
                    break;
                }

                $res[] = $previousHandler;
                restore_exception_handler();
            }

            $res = array_reverse($res);

            foreach ($res as $handler) {
                set_exception_handler($handler);
            }
            
            // error handlers:
            $res = [];

            while (true) {
                $previousHandler = set_error_handler(
                    static fn (int $errno, string $errstr, string $errfile = '', int $errline = 0, array $errcontext = []): ?bool => null
                );
                restore_error_handler();

                if (
                    is_array($previousHandler)
                    && $previousHandler[0] instanceof \Tobento\Service\ErrorHandler\ErrorHandling
                    && $previousHandler[1] === 'handleError'
                ) {
                    restore_error_handler();
                    continue;
                }

                if ($previousHandler === null) {
                    break;
                }

                $res[] = $previousHandler;
                restore_error_handler();
            }

            $res = array_reverse($res);

            foreach ($res as $handler) {
                set_error_handler($handler);
            }    
        }
    }
}