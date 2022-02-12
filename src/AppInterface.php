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

/**
 * AppInterface
 */
interface AppInterface
{
    /**
     * Set the environment.
     *
     * @param string $environment The environment such as PRODUCTION, DEVELOPMENT and TESTING.
     * @return static $this
     */    
    public function setEnvironment(string $environment): static;

    /**
     * Returns the environment.
     *
     * @return string
     */    
    public function getEnvironment(): string;   
            
    /**
     * Register a boot or multiple. 
     *
     * @param mixed $boots
     * @return static $this
     * @throws InvalidBootException
     */
    public function boot(mixed ...$boots): static;

    /**
     * Returns the booter.
     *
     * @return BooterInterface
     */
    public function booter(): BooterInterface;
    
    /**
     * Do the booting.
     *
     * @return static $this
     */
    public function booting(): static;
    
    /**
     * Returns the specified directory.
     *
     * @param string $name
     * @return string
     */
    public function dir(string $name): string;
    
    /**
     * Returns the directories.
     *
     * @return DirsInterface
     */
    public function dirs(): DirsInterface;    
        
    /**
     * Sets an entry by its given identifier.
     *
     * @param string $id Identifier of the entry.
     * @param mixed $value Any value.
     * @return DefinitionInterface
     */
    public function set(string $id, mixed $value = null): DefinitionInterface;

    /**
     * If an entry by its given identifier exist.
     *
     * @param string $id Identifier of the entry.
     * @return bool Returns true if exist, otherwise false.
     */
    public function has(string $id): bool;
    
    /**
     * Gets an entry by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     * @return mixed The value obtained from the identifier.
     */
    public function get(string $id): mixed;
    
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
    public function make(string $id, array $parameters = []): mixed;

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
    public function call(mixed $callable, array $parameters = []): mixed;
    
    /**
     * Resolve on.
     *
     * @param string $id Identifier of the entry.
     * @param mixed Any value.
     * @return OnRule
     */
    public function on(string $id, mixed $value = null): OnRule;

    /**
     * Returns the resolver.
     * 
     * @return ResolverInterface
     */
    public function resolver(): ResolverInterface;
    
    /**
     * Returns the container.
     * 
     * @return ContainerInterface
     */
    public function container(): ContainerInterface;

    /**
     * Add a macro.
     *
     * @param string $name The macro name.
     * @param object|callable $macro
     * @return static
     */
    public function addMacro(string $name, object|callable $macro): static;
    
    /**
     * Run the application.
     *
     * @return void
     */
    public function run(): void;

    /**
     * Returns the run cycles.
     *
     * @return int
     */
    public function getRunCycles(): int;
}