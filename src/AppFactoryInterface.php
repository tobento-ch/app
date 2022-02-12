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

use Tobento\App\AppInterface;
use Tobento\Service\Resolver\ResolverFactoryInterface;
use Tobento\Service\Booting\BooterInterface;
use Tobento\Service\Dir\DirsInterface;

/**
 * AppFactoryInterface
 */
interface AppFactoryInterface
{   
    /**
     * Create a new App.
     *
     * @param null|ResolverFactoryInterface $resolverFactory
     * @param null|BooterInterface $booter
     * @param null|DirsInterface $dirs
     * @return AppInterface
     */
    public function createApp(
        null|ResolverFactoryInterface $resolverFactory = null,
        null|BooterInterface $booter = null,
        null|DirsInterface $dirs = null,
    ): AppInterface;
}