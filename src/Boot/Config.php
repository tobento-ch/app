<?php

/**
 * TOBENTO
 *
 * @copyright    Tobias Strub, TOBENTO
 * @license     MIT License, see LICENSE file distributed with this source code.
 * @author      Tobias Strub
 * @link        https://www.tobento.ch
 */

declare(strict_types=1);
 
namespace Tobento\App\Boot;

use Tobento\App\Boot;
use Tobento\Service\Config\Config as Configuration;
use Tobento\Service\Config\ConfigInterface;
use Tobento\Service\Config\PhpLoader;
use Tobento\Service\Config\ConfigLoadException;
use Tobento\Service\Config\ConfigNotFoundException;
use Tobento\Service\Collection\Translations;

/**
 * Config boot.
 */
class Config extends Boot
{
    public const INFO = [
        'boot' => 'implements '.ConfigInterface::class,
    ];
    
    /**
     * Boot application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->app->set(ConfigInterface::class, function() {
            
            $trans = new Translations();
            
            $config = new Configuration($trans);

            $config->addLoader(
                new PhpLoader($this->app->dirs()->sort()->group('config'))
            );
            
            return $config;
        });
        
        $this->app->addMacro('config', [$this, 'get']);
    }
    
    /**
     * Loads a file and stores config is set.
     *
     * @param string $file The file to load.
     * @param null|string $key If a key is set, it stores as such.
     * @param null|int|string $locale
     * @return array The loaded config data.
     */
    public function load(string $file, null|string $key = null, null|int|string $locale = null): array
    {
        try {
            return $this->app->get(ConfigInterface::class)->load($file, $key, $locale);
        } catch (ConfigLoadException $e) {
            return [];
        }
    }
    
    /**
     * Get a value by key.
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
    public function get(string $key, mixed $default = null, null|int|string|array $locale = null): mixed
    { 
        return $this->app->get(ConfigInterface::class)->get($key, $default, $locale);
    }
}