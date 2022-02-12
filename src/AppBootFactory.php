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

use Tobento\Service\Resolver\ResolverInterface;
use Tobento\Service\Booting\BootFactoryInterface;
use Tobento\Service\Booting\BootInterface;
use Tobento\Service\Booting\InvalidBootException;
use Throwable;

/**
 * AppBootFactory
 */
class AppBootFactory implements BootFactoryInterface
{
    /**
     * Create a new AppBootFactory.
     *
     * @param ResolverInterface $resolver
     */
    public function __construct(
        protected ResolverInterface $resolver
    ) {}
    
    /**
     * Create a new Boot.
     *
     * @param mixed $boot
     * @return BootInterface
     * @throws InvalidBootException
     */
    public function createBoot(mixed $boot): BootInterface
    {
        // if it is already an instance, just return.
        if ($boot instanceof BootInterface) {
            return $this->bindToContainer($boot);
        }
        
        $parameters = [];
        
        if (
            is_array($boot) 
            && isset($boot[0])
            && is_string($boot[0])
        ) {
            $parameters = $boot;
            
            // remove boot
            array_shift($parameters);

            $boot = $boot[0];
        }
        
        if (!is_string($boot)) {
            throw new InvalidBootException($boot);
        }    
        
        try {
            $boot = $this->resolver->make($boot, $parameters);
        } catch (Throwable $e) {
            throw new InvalidBootException($boot, $e->getMessage());
        }
        
        if (! $boot instanceof BootInterface) {
            throw new InvalidBootException($boot);
        }
        
        return $this->bindToContainer($boot);
    }
    
    /**
     * Call a boot method.
     *
     * @param BootInterface $boot
     * @param string $method
     * @return void
     */
    public function callBootMethod(BootInterface $boot, string $method): void
    {
        $this->resolver->call([$boot, $method]);
    }
    
    /**
     * Bind boot to container for autowiring boot.
     *
     * @param BootInterface $boot
     * @return BootInterface
     */
    protected function bindToContainer(BootInterface $boot): BootInterface
    {
        $this->resolver->set($boot::class, $boot);         
        
        return $boot;
    }
}