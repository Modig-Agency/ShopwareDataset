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
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;

class Product implements DataProcessorInterface
{
    private Pool $locatorPool;
    private EntityRepository $repository;

    /**
     * @param Pool $locatorPool
     * @param EntityRepository $repository
     */
    public function __construct(Pool $locatorPool, EntityRepository $repository)
    {
        $this->locatorPool = $locatorPool;
        $this->repository = $repository;
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
        $toSkip = $this->getIdsToSkip($data);
        foreach ($data as $product) {
            if (in_array($product['id'], $toSkip)) {
                continue;
            }
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
            foreach ($product['visibilities'] ?? [] as $key => $visibility) {
                $product['visibilities'][$key]['salesChannelId'] = $salesChannelId;
            }
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

    /**
     * @param array $data
     * @return array
     */
    private function getIdsToSkip(array $data): array
    {
        $dataIds = array_map(
            function (array $item) {
                return $item['id'];
            },
            $data
        );
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsAnyFilter('id', $dataIds));
        $resultIds = $this->repository->searchIds($criteria, Context::createDefaultContext());
        return $resultIds->getIds();
    }
}
