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

namespace Tobento\App\Test\Mock;

use Tobento\Service\Booting\Boot;

/**
 * CausesErrorBoot
 */
class CausesErrorBoot extends Boot
{
    public function boot(): void
    {
        // Do something which causes an error
        echo $test();
    }
}