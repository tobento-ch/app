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
use Tobento\Service\Dater\DateFormatter;
use Tobento\Service\Config\ConfigInterface;
use Psr\Container\ContainerInterface;

/**
 * Dater boot.
 */
class Dater extends Boot
{
    public const INFO = [
        'boot' => [
            'Configures DateFormatter with the app.timezone and app.locale',
        ],
    ];

    public const BOOT = [
        \Tobento\App\Boot\Config::class,
    ];
    
    public function boot(): void
    {
        $this->app->set(DateFormatter::class, function(ContainerInterface $container): DateFormatter {
            
            $config = $container->get(ConfigInterface::class);
            
            return new DateFormatter(
                locale: $config->get('app.locale', 'de_DE'),
                dateFormat: 'EEEE, dd. MMMM yyyy',
                dateTimeFormat: 'EEEE, dd. MMMM yyyy, HH:mm',
                timezone: $config->get('app.timezone', 'Europe/Berlin'),
                mutable: false,
            );            
        });
    }
}