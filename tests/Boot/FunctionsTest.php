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
use Tobento\App\AppInterface;
use Tobento\Service\Config\ConfigInterface;
use function Tobento\App\{app, directory, config};
use function Tobento\App\Test\{environment};
    
/**
 * FunctionsTest
 */
class FunctionsTest extends TestCase
{
    public function testBoot()
    {
        $app = (new AppFactory())->createApp();
        
        $app->boot(\Tobento\App\Boot\Functions::class);

        $app->booting();
        
        $this->assertInstanceof(AppInterface::class, app());
        
        $app->dirs()->dir('dir/to/foo/', 'foo');
        
        $this->assertSame('dir/to/foo/', directory('foo'));

        $app->run();
    }
    
    public function testConfigMethod()
    {
        $app = (new AppFactory())->createApp();
        
        $app->boot(\Tobento\App\Boot\Functions::class);
        $app->boot(\Tobento\App\Boot\Config::class);

        $app->booting();
        
        $app->get(ConfigInterface::class)->set('app.foo', 'foo');
        
        $this->assertSame('foo', config('app.foo'));

        $app->run();
    }    
    
    public function testBootMethods()
    {
        $app = (new AppFactory())->createApp();
        
        $app->boot(\Tobento\App\Boot\Functions::class);

        $app->booting();

        $app->get(\Tobento\App\Boot\Functions::class)->register(
            functionFile: __DIR__.'/../functions.php',
        );
                
        $this->assertSame('production', environment());
    }
    
    public function testAppMacro()
    {
        $app = (new AppFactory())->createApp();
        
        $app->boot(\Tobento\App\Boot\Functions::class);

        $app->booting();

        $app->functions(
            functionFile: __DIR__.'/../functions.php',
        );
                
        $this->assertSame('production', environment());
    }
}