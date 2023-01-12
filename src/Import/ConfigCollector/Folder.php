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

use Modig\Dataset\Import\ConfigItem;
use Modig\Dataset\Import\Locator\Pool;

class Folder implements ConfigCollectorInterface
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
        $value = 'Missing Media Destination Folder';
        try {
            $folder = $this->pool->getLocator('folder')->locate($config['folder'] ?? []);
            $valid = false;
            if ($folder) {
                $productFolder = $folder->getFolder();
                if ($productFolder) {
                    $value = $productFolder->getName() . ' (ID: ' . $productFolder->getId() . ')';
                    $valid = true;
                }
            }
        } catch (\InvalidArgumentException $exception) {
            $valid = false;
        }
        return [new ConfigItem('Media Destination Folder', $value, $valid)];
    }
}
