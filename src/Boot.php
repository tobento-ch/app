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

use Tobento\Service\Booting\Boot as BaseBoot;

/**
 * Boot
 */
abstract class Boot extends BaseBoot
{    
    /**
     * Create a new Boot.
     *
     * @param AppInterface $app
     */
    public function __construct(
        protected AppInterface $app,
    ) {}  
}