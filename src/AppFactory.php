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

use Tobento\App\AppFactoryInterface;
use Tobento\App\AppInterface;
use Tobento\App\App;
use Tobento\Service\Resolver\ResolverFactoryInterface;
use Tobento\Service\ResolverContainer\ResolverFactory;
use Tobento\Service\Booting\BooterInterface;
use Tobento\Service\Booting\Booter;
use Tobento\Service\Booting\AutowiringBootFactory;
use Tobento\Service\Dir\DirsInterface;
use Tobento\Service\Dir\Dirs;

/**
 * AppFactory
 */
class AppFactory implements AppFactoryInterface
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
    ): AppInterface {
        
        $resolverFactory = $resolverFactory ?: new ResolverFactory();
        $resolver = $resolverFactory->createResolver();
        $dirs = $dirs ?: new Dirs();
        
        $booter = $booter ?: new Booter(
            bootFactory: new AppBootFactory($resolver),
            //bootFactory: new AutowiringBootFactory($resolver->container()),
            name: 'app',
            bootMethods: ['boot'],
            terminateMethods: ['terminate'],
        );
        
        return new App($resolver, $booter, $dirs);
    }
}