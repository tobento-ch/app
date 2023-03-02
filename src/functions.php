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
use Tobento\Service\Config\ConfigInterface;
use Tobento\Service\Config\ConfigNotFoundException;

if (!function_exists(__NAMESPACE__.'\app')) {
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

if (!function_exists(__NAMESPACE__.'\directory')) {
    /**
     * Returns the specified directory.
     *
     * @param string $name
     * @return string
     */
    function directory(string $name): string
    {
        return app()->dir($name);
    }
}

if (!function_exists(__NAMESPACE__.'\config')) {
    /**
     * Returns a config value by key.
     *
     * @param string $key The key.
     * @param mixed $default A default value.
     * @param null|int|string|array $locale 
     *        string: locale,
     *        array: [] if empty gets all languages,
     *        otherwise the keys set ['de', 'en']
     * @return mixed The value or the default value if not exist.
     * throws ConfigNotFoundException
     */
    function config(string $key, mixed $default = null, null|int|string|array $locale = null): mixed
    {
        return app()->get(ConfigInterface::class)->get($key, $default, $locale);
    }
}