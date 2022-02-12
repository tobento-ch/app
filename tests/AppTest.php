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

namespace Tobento\App\Test;

use PHPUnit\Framework\TestCase;
use Tobento\App\AppFactory;
use Tobento\App\BootErrorHandlersInterface;
use Tobento\App\BootErrorHandlers;
use Tobento\Service\Booting\BootException;
use Tobento\Service\ErrorHandler\AutowiringThrowableHandlerFactory;
use Tobento\Service\Dir\DirNotFoundException;
use Tobento\Service\Resolver\OnRule;
use Tobento\Service\Resolver\ResolverInterface;
use Psr\Container\ContainerInterface;
use Throwable;
    
/**
 * AppTest
 */
class AppTest extends TestCase
{    
    public function testEnvironmentMethods()
    {
        $app = (new AppFactory())->createApp();
        
        $this->assertSame('production', $app->getEnvironment());
        
        $app->setEnvironment('dev');
        
        $this->assertSame('dev', $app->getEnvironment());
    }
    
    public function testBooting()
    {
        $app = (new AppFactory())->createApp();
        
        $app->boot(\Tobento\App\Boot\Config::class);
        
        $app->boot(\Tobento\App\Boot\Dater::class);
                
        $this->assertSame([], $app->booter()->getBooted());
        
        $app->booting();
        
        $this->assertSame(
            ['Tobento\App\Boot\Config', 'Tobento\App\Boot\Dater'],
            [
                $app->booter()->getBooted()[0]['boot'],
                $app->booter()->getBooted()[1]['boot'],
            ]
        );
    }
    
    public function testRunMethodDoesBooting()
    {
        $app = (new AppFactory())->createApp();
        
        $app->boot(\Tobento\App\Boot\Config::class);
        
        $app->boot(\Tobento\App\Boot\Dater::class);
                
        $this->assertSame([], $app->booter()->getBooted());
        
        $app->run();
        
        $this->assertSame(
            ['Tobento\App\Boot\Config', 'Tobento\App\Boot\Dater'],
            [
                $app->booter()->getBooted()[0]['boot'],
                $app->booter()->getBooted()[1]['boot'],
            ]
        );
    }
    
    public function testRunCyclesMethod()
    {
        $app = (new AppFactory())->createApp();
        
        $this->assertSame(0, $app->getRunCycles());
        
        $app->run();
        
        $this->assertSame(1, $app->getRunCycles());
        
        $app->run();
        
        $this->assertSame(2, $app->getRunCycles());
    }    
    
    public function testThrowsBootExceptionIfBootCausesError()
    {
        $this->expectException(BootException::class);
        
        $app = (new AppFactory())->createApp();
        
        $app->boot(\Tobento\App\Test\Mock\CausesErrorBoot::class);
        
        $app->run();
    }
    
    public function testHandleBootExceptionWithBootErrorHandlersIfBootCausesError()
    {
        $app = (new AppFactory())->createApp();
        
        $app->boot(\Tobento\App\Test\Mock\CausesErrorBoot::class);
        
        $app->set(BootErrorHandlersInterface::class, function() use ($app) {

            $handlers = new BootErrorHandlers(
                new AutowiringThrowableHandlerFactory($app->container())
            );

            $handlers->add(function(Throwable $t): mixed {
                $this->assertTrue(true);
                return null;
            });

            return $handlers;
        });
        
        $app->run();
    }
    
    public function testDirMethods()
    {
        $app = (new AppFactory())->createApp();
        
        $app->dirs()
            ->dir(dir: 'path/to/config', name: 'config')
            ->dir(dir: 'path/to/view', name: 'view');
                
        $this->assertSame(
            'path/to/view/',
            $app->dir(name: 'view')
        );
    }
    
    public function testDirMethodThrowsDirNotFoundException()
    {
        $this->expectException(DirNotFoundException::class);
        
        $app = (new AppFactory())->createApp();
        
        $app->dir(name: 'foo');
    }
    
    public function testHasSetAndGetMethod()
    {
        $app = (new AppFactory())->createApp();
        
        $this->assertFalse($app->has('key'));
        
        $app->set('key', 'value');
        
        $this->assertTrue($app->has('key'));
        
        $this->assertSame('value', $app->get('key'));
    }
    
    public function testMakeMethod()
    {
        $app = (new AppFactory())->createApp();
        
        $dater = $app->make(\Tobento\App\Boot\Dater::class);
        
        $this->assertInstanceof(\Tobento\App\Boot\Dater::class, $dater);
    }

    public function testCallMethod()
    {
        $app = (new AppFactory())->createApp();
        
        $class = new class() {
            public function index(string $name): string
            {
                return $name;
            }
        };
        
        $name = $app->call([$class, 'index'], ['name' => 'value']);
        
        $this->assertSame('value', $name);
    }
    
    public function testOnMethod()
    {
        $app = (new AppFactory())->createApp();
        
        $this->assertInstanceof(
            OnRule::class,
            $app->on(Foo::class)
        );
    }
    
    public function testResolverMethod()
    {
        $app = (new AppFactory())->createApp();
        
        $this->assertInstanceof(
            ResolverInterface::class,
            $app->resolver()
        );
    }
    
    public function testContainerMethod()
    {
        $app = (new AppFactory())->createApp();
        
        $this->assertInstanceof(
            ContainerInterface::class,
            $app->container()
        );
    }
    
    public function testAddMacroMethod()
    {
        $app = (new AppFactory())->createApp();
        
        $app->addMacro('lowercase', function(string $string): string {
            return strtolower($string);
        });
        
        $this->assertSame(
            'lorem',
            $app->lowercase('Lorem')
        );
    }    
}