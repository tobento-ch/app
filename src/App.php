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
use Psr\Container\NotFoundExceptionInterface;
use Psr\Container\ContainerExceptionInterface;
use Tobento\Service\Resolver\ResolverInterface;
use Tobento\Service\Resolver\DefinitionInterface;
use Tobento\Service\Resolver\OnRule;
use Tobento\Service\Booting\BooterInterface;
use Tobento\Service\Booting\InvalidBootException;
use Tobento\Service\Dir\DirsInterface;
use Tobento\Service\Macro\Macroable;
use Throwable;

/**
 * App
 */
class App implements AppInterface
{
    use Macroable;
    
    /**
     * @var string The current version.
     */
    public const VERSION = '1.0.0';

    /**
     * @var string The current environment.
     */
    protected string $environment = 'production';
    
    /**
     * @var array The registered boots.
     */
    protected array $boots = [];

    /**
     * @var int The run cycles
     */
    protected int $runCycles = 0;
    
    /**
     * Create a new App.
     *
     * @param ResolverInterface $resolver
     * @param BooterInterface $booter
     * @param DirsInterface $dirs
     */
    public function __construct(
        protected ResolverInterface $resolver,
        protected BooterInterface $booter,
        protected DirsInterface $dirs,
    ) {
        $this->set(AppInterface::class, $this);
        $this->set(DirsInterface::class, $dirs);
    }

    /**
     * Set the environment.
     *
     * @param string $environment
     * @return static $this
     */
    public function setEnvironment(string $environment): static
    {
        $this->environment = $environment;
        return $this;
    }

    /**
     * Returns the environment.
     *
     * @return string
     */
    public function getEnvironment(): string
    {
        return $this->environment;
    }

    /**
     * Register a boot or multiple. 
     *
     * @param mixed $boots
     * @return static $this
     * @throws InvalidBootException
     */
    public function boot(mixed ...$boots): static
    {
        $this->boots[] = $boots;
        return $this;
    }
    
    /**
     * Returns the booter.
     *
     * @return BooterInterface
     */
    public function booter(): BooterInterface
    {
        return $this->booter;
    }
    
    /**
     * Do the booting.
     *
     * @return static $this
     */
    public function booting(): static
    {
        if (!empty($this->boots))
        {
            $booter = $this->booter();
                        
            foreach($this->boots as $index => $boot)
            {
                // Register boots and remove it if not to register again.
                call_user_func_array([$booter, 'register'], $boot);
                unset($this->boots[$index]);
            }
            
            try { 
                $booter->boot();
            } catch (Throwable $t) {
                
                if (! $this->has(BootErrorHandlersInterface::class)) {
                    Throw $t;
                }
                
                $response = $this->get(BootErrorHandlersInterface::class)->handleThrowable($t);
                
                if ($response instanceof Throwable) {
                    Throw $t;
                }
            }
                        
            // Booting again if a boot registered any other boots.
            $this->booting();
        }
        
        return $this;
    }
                    
    /**
     * Returns the specified directory.
     *
     * @param string $name
     * @return string
     */
    public function dir(string $name): string
    {        
        return $this->dirs->get($name);
    }
    
    /**
     * Returns the directories.
     *
     * @return DirsInterface
     */
    public function dirs(): DirsInterface
    {        
        return $this->dirs;
    }

    /**
     * Sets an entry by its given identifier.
     *
     * @param string $id Identifier of the entry.
     * @param mixed $value Any value.
     * @return DefinitionInterface
     */
    public function set(string $id, mixed $value = null): DefinitionInterface
    {
        return $this->resolver->set($id, $value);
    }
    
    /**
     * If an entry by its given identifier exist.
     *
     * @param string $id Identifier of the entry.
     * @return bool Returns true if exist, otherwise false.
     */
    public function has(string $id): bool
    {
        return $this->resolver->has($id);
    }
    
    /**
     * Gets an entry by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     * @return mixed The value obtained from the identifier.
     */
    public function get(string $id): mixed
    {
        return $this->resolver->get($id);
    }

    /**
     * Makes an entry by its identifier.
     *
     * @param string $id Identifier of the entry.
     * @param array<int|string, mixed> $parameters The parameters.
     *
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     *
     * @return mixed The value obtained from the identifier.
     */
    public function make(string $id, array $parameters = []): mixed
    {
        return $this->resolver->make($id, $parameters);
    }

    /**
     * Call the given callable.
     *
     * @param mixed $callable A callable.
     * @param array<int|string, mixed> $parameters The parameters.
     *
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     *
     * @return mixed The called function result.
     */
    public function call(mixed $callable, array $parameters = []): mixed
    {
        return $this->resolver->call($callable, $parameters);
    }
    
    /**
     * Resolve on.
     *
     * @param string $id Identifier of the entry.
     * @param mixed Any value.
     * @return OnRule
     */
    public function on(string $id, mixed $value = null): OnRule
    {
        return $this->resolver->on($id, $value);
    }

    /**
     * Returns the resolver.
     * 
     * @return ResolverInterface
     */
    public function resolver(): ResolverInterface
    {
        return $this->resolver;
    }
    
    /**
     * Returns the container.
     * 
     * @return ContainerInterface
     */
    public function container(): ContainerInterface
    {
        return $this->resolver->container();
    }
    
    /**
     * Add a macro.
     *
     * @param string $name The macro name.
     * @param object|callable $macro
     * @return static
     */
    public function addMacro(string $name, object|callable $macro): static
    {
        $this->macro($name, $macro);
        
        return $this;
    }
    
    /**
     * Run the application.
     *
     * @return void
     */
    public function run(): void
    {
        $this->runCycles++;
        
        // do the booting and terminating.
        $this->booting();
        $this->terminating();
    }

    /**
     * Returns the run cycles.
     *
     * @return int
     */
    public function getRunCycles(): int
    {
        return $this->runCycles;
    }
    
    /**
     * Do the terminating.
     *
     * @return void
     */
    protected function terminating(): void
    {
        $this->booter->terminate();
    }
}