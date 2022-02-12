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

namespace Tobento\App;

use Psr\Container\ContainerInterface;
use Tobento\Service\HelperFunction\Functions;

if (!function_exists('app')) {
    /**
     * Returns the app.
     *
     * @return AppInterface
     */
    function app(): AppInterface
    {
        return Functions::get(ContainerInterface::class)->get(AppInterface::class);
    }
}