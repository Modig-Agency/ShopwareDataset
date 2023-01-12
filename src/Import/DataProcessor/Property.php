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
use Shopware\Core\Content\Property\PropertyGroupDefinition;

class Property implements DataProcessorInterface
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
            throw new MissingConfigValueException("Language", "Properties");
        }
        $languageId = $language->getId();
        foreach ($data as $property) {
            $name = (string)$property['name'];
            $result[$name] = [
                'id' => $property['id'],
                'name' => [
                    $languageId => $name
                ],
                'translations' => [
                    $languageId => ['name' => $name]
                ],
                'sortingType' => PropertyGroupDefinition::SORTING_TYPE_ALPHANUMERIC,
                'displayType' => PropertyGroupDefinition::DISPLAY_TYPE_TEXT,
                'options' => array_map(
                    function ($option) use ($languageId, $name) {
                        return [
                            'id' => $option['id'],
                            'name' => [
                                $languageId => strval($option['name']),
                            ],
                            'translations' => [
                                $languageId => ['name' => strval($option['name'])],
                            ],
                        ];
                    },
                    array_filter(
                        $property['options'],
                        function ($option) {
                            return !empty($option['name']);
                        }
                    )
                )
            ];
        }
        return array_values($result);
    }
}
