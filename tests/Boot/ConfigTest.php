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
 * ConfigTest
 */
class ConfigTest extends TestCase
{
    public function testBoot()
    {
        $app = (new AppFactory())->createApp();

        $app->boot(\Tobento\App\Boot\Config::class);

        $app->booting();

        $value = $app->get(ConfigInterface::class)->get(
            key: 'app.key',
            default: 'bar',
            locale: 'de'
        );
        
        $this->assertSame('bar', $value);

        $value = $app->config('app.key', 'foo');

        $this->assertSame('foo', $value);

        $app->run();
    }
    
    public function testBootMethods()
    {
        $app = (new AppFactory())->createApp();

        $app->dirs()
            ->dir(dir: realpath(__DIR__.'/../config'), name: 'config', group: 'config');
        
        $app->boot(\Tobento\App\Boot\Config::class);

        $app->booting();

        $value = $app->get(\Tobento\App\Boot\Config::class)->load(
            file: 'app.php',
            key: 'app',
        );
        
        $value = $app->get(\Tobento\App\Boot\Config::class)->get(
            key: 'app.locale',
            default: 'en',
            locale: null
        );
        
        $this->assertSame('de-DE', $value);
    }
}