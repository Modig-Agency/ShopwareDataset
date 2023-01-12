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
use Modig\Dataset\Import\DataProcessor\Category;
use Modig\Dataset\Import\Locator\Pool;

class RootCategory implements ConfigCollectorInterface
{
    private Pool $pool;

    /**
     * @param Pool $pool
     */
    public function __construct(Pool $pool)
    {
        $this->pool = $pool;
    }

    /**
     * {@inheritDoc}
     */
    public function collect(array $config): array
    {
        try {
            $root = $this->pool->getLocator('category')->locate($config['root'] ?? []);
        } catch (InvalidArgumentException $exception) {
            $root = null;
        }
        $value = $root
            ? $root->getName() . ' (ID: ' . $root->getId() . ')'
            : '<info>None</info> ' . 'Category named ' . Category::DEFAULT_ROOT_CATEGORY_NAME . ' will be created';
        return [new ConfigItem('Root Category', $value, true)];
    }
}
