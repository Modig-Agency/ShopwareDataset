<?php

/**
 * Modig Dataset
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @copyright Modig Agency
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 * @author    Modig Agency <http://www.modigagency.com/>
 */

declare(strict_types=1);

namespace Modig\Dataset\Import\ConfigCollector;

use Modig\Dataset\Validator\ArrayTypeValidator;

class CompositeConfigCollector implements ConfigCollectorInterface
{
    /**
     * @var ConfigCollectorInterface[]
     */
    private array $collectors;

    /**
     * @param array $collectors
     */
    public function __construct(iterable $collectors)
    {
        $this->collectors = ArrayTypeValidator::getValid(
            $collectors,
            ConfigCollectorInterface::class,
            static::class
        );
    }

    /**
     * {@inheritDoc}
     */
    public function collect(array $config): array
    {
        $all = [];
        foreach ($this->collectors as $collector) {
            $all = array_merge($all, $collector->collect($config ?? []));
        }
        return $all;
    }
}
