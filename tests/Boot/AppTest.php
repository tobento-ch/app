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

namespace Tobento\App\Test\Boot;

use PHPUnit\Framework\TestCase;
use Tobento\App\AppFactory;
use Tobento\Service\Config\ConfigInterface;
    
/**
 * AppTest
 */
class AppTest extends TestCase
{
    public function testBoot()
    {
        $app = (new AppFactory())->createApp();
        
        $app->dirs()->dir(dir: realpath(__DIR__.'/../config'), name: 'config', group: 'config');
        
        $app->boot(\Tobento\App\Boot\App::class);

        $app->booting();

        $value = $app->get(ConfigInterface::class)->get(
            key: 'app.locale',
        );
        
        $this->assertSame('de-DE', $value);
        
        $this->assertSame(
            [
                'Tobento\App\Boot\Config',
                'Tobento\App\Boot\Functions',
                'Tobento\App\Boot\App',
                'Tobento\App\Boot\ErrorHandling'
            ],
            [
                $app->booter()->getBooted()[0]['boot'],
                $app->booter()->getBooted()[1]['boot'],
                $app->booter()->getBooted()[2]['boot'],
                $app->booter()->getBooted()[3]['boot'],
            ]
        );
        
        $app->run();
    }
    
    public function testConfigEnvironmentBased()
    {
        $app = (new AppFactory())->createApp();
        
        $app->dirs()->dir(dir: realpath(__DIR__.'/../config-env'), name: 'config', group: 'config');
        
        $app->boot(\Tobento\App\Boot\App::class);

        $app->booting();

        $value = $app->get(ConfigInterface::class)->get(
            key: 'app.locale',
        );
        
        $this->assertSame('en-US', $value);
        
        $app->run();
    }    
}