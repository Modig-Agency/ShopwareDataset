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

use DateTimeImmutable;
use InvalidArgumentException;
use Modig\Dataset\Exception\MissingConfigValueException;
use Modig\Dataset\Import\Locator\Pool;
use Shopware\Core\Content\Product\Aggregate\ProductVisibility\ProductVisibilityDefinition;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Uuid\Uuid;

class Product implements DataProcessorInterface
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
        try {
            $tax = $this->locatorPool->getLocator('tax')->locate($config['tax'] ?? []);
        } catch (InvalidArgumentException $exception) {
            $tax = null;
        }
        if (!$tax) {
            throw new MissingConfigValueException("Tax", "Product");
        }
        $taxId = $tax->getId();
        $rate = $tax->getTaxRate();
        $factor = 1 + ($rate / 100);
        try {
            $language = $this->locatorPool->getLocator('language')->locate($config['language'] ?? []);
        } catch (InvalidArgumentException $exception) {
            $language = null;
        }
        if (!$language) {
            throw new MissingConfigValueException("Language", "Product");
        }
        $languageId = $language->getId();
        try {
            $salesChannel = $this->locatorPool->getLocator('sales_channel')->locate($config['sales_channel'] ?? []);
        } catch (InvalidArgumentException $exception) {
            $salesChannel = null;
        }
        if (!$salesChannel) {
            throw new MissingConfigValueException("Sales Channel", "Product");
        }
        $salesChannelId = $salesChannel->getId();
        $releaseDate = new DateTimeImmutable();
        $stock = $config['stock'] ?? 10000;
        $importData = [];
        foreach ($data as $product) {
            $price = $product['price'];
            $name = $product['name'];
            $description = $product['description'] ?? '';
            $product['price'] = [[
                'net' => $price / $factor,
                'gross' => $price,
                'linked' => true,
                'currencyId' => Defaults::CURRENCY,
            ]];
            $product['name'] = [
                $languageId => $name,
            ];
            $product['description'] = [
                $languageId => $description
            ];
            $product['stock'] = $stock;
            $product['purchasePrice'] = $price;
            $product['taxId'] = $taxId;
            $product['active'] = true;
            $product['purchaseUnit'] = 1;
            $product['referenceUnit'] = 1;
            $product['shippingFree'] = false;
            $product['purchasePrice'] = $price;
            $product['releaseDate'] = $releaseDate;
            $product['displayInListing'] = true;
            $product['visibilities'] = [
                [
                    'id' => Uuid::randomHex(),
                    'salesChannelId' => $salesChannelId,
                    'visibility' => ProductVisibilityDefinition::VISIBILITY_ALL,
                ],
            ];
            if (isset($product['children'])) {
                $product['children'] = array_map(
                    function ($child) use ($stock) {
                        $child['stock'] = $stock;
                        return $child;
                    },
                    $product['children']
                );
            }
            $importData[] = $product;
        }
        return $importData;
    }
}
