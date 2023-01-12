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

use InvalidArgumentException;
use Modig\Dataset\Import\ConfigItem;
use Modig\Dataset\Import\Locator\Pool;

class Locator implements ConfigCollectorInterface
{
    private Pool $pool;
    private string $locatorKey;
    private string $errorMessage;
    private string $settingName;

    /**
     * @param Pool $pool
     * @param string $locatorKey
     * @param string $errorMessage
     * @param string $settingName
     */
    public function __construct(Pool $pool, string $locatorKey, string $errorMessage, string $settingName)
    {
        $this->pool = $pool;
        $this->locatorKey = $locatorKey;
        $this->errorMessage = $errorMessage;
        $this->settingName = $settingName;
    }

    /**
     * {@inheritDoc}
     */
    public function collect(array $config): array
    {
        try {
            $entity = $this->pool->getLocator($this->locatorKey)->locate($config[$this->locatorKey] ?? []);
        } catch (InvalidArgumentException $exception) {
            $entity = null;
        }
        $value = $entity
            ? $entity->getName() . ' (ID: ' . $entity->getId() . ')'
            : $this->errorMessage;
        $valid = !!$entity;
        return [new ConfigItem($this->settingName, $value, $valid)];
    }
}
