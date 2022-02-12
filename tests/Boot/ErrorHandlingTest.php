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
use Tobento\Service\Dater\DateFormatter;
    
/**
 * ErrorHandlingTest
 */
class ErrorHandlingTest extends TestCase
{
    public function testBoot()
    {
        $app = (new AppFactory())->createApp();

        $app->boot(\Tobento\App\Boot\ErrorHandling::class);

        $app->booting();

        $app->run();
        
        $this->assertTrue(true);
    }
}