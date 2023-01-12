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

namespace Modig\Dataset\Import\DataProcessor;

use InvalidArgumentException;
use Modig\Dataset\Exception\MissingConfigValueException;
use Modig\Dataset\Import\Locator\Pool;

class Manufacturer implements DataProcessorInterface
{
    private Pool $locatorPool;

    /**
     * @param Pool $locatorPool
     */
    public function __construct(Pool $locatorPool)
    {
        $this->locatorPool = $locatorPool;
    }

    /**
     * {@inheritDoc}
     */
    public function process(array $data, array $config): array
    {
        $result = [];
        try {
            $language = $this->locatorPool->getLocator('language')->locate($config['language'] ?? []);
        } catch (InvalidArgumentException $exception) {
            $language = null;
        }

        if (!$language) {
            throw new MissingConfigValueException("Language", "Manufacturer");
        }
        $languageId = $language->getId();
        foreach ($data as $manufacturer) {
            $name = (string)$manufacturer['name'];
            $result[$name] = [
                'id' => $manufacturer['id'],
                'name' => [
                    $languageId => $name
                ],
                'translations' => [
                    $languageId => ['name' => $name]
                ]
            ];
        }
        return array_values($result);
    }
}
