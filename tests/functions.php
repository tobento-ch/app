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

use Psr\Container\ContainerInterface;
use Tobento\App\AppInterface;
use Tobento\Service\HelperFunction\Functions;

if (!function_exists(__NAMESPACE__.'\environment')) {
    /**
     * Returns the app environment.
     *
     * @return string
     */
    function environment(): string
    {
        return Functions::get(ContainerInterface::class)->get(AppInterface::class)->getEnvironment();
    }
}