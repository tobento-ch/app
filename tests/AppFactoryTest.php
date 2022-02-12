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
use Tobento\App\AppFactoryInterface;
use Tobento\App\AppInterface;
use Tobento\Service\Resolver\ResolverFactoryInterface;
use Tobento\Service\Booting\BooterInterface;
use Tobento\Service\Dir\DirsInterface;
    
/**
 * AppFactoryTest
 */
class AppFactoryTest extends TestCase
{
    public function testThatImplementsAppFactoryInterface()
    {
        $this->assertInstanceof(
            AppFactoryInterface::class,
            new AppFactory()
        );
    }
    
    public function testCreateDefault()
    {
        $app = (new AppFactory())->createApp();
        
        $this->assertInstanceof(
            AppInterface::class,
            (new AppFactory())->createApp()
        );
    }
}