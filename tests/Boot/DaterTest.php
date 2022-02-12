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
 * DaterTest
 */
class DaterTest extends TestCase
{
    public function testBoot()
    {
        $app = (new AppFactory())->createApp();

        $app->boot(\Tobento\App\Boot\Dater::class);

        $app->booting();
        
        $this->assertInstanceof(
            DateFormatter::class,
            $app->get(DateFormatter::class)
        );

        $app->run();
    }
}