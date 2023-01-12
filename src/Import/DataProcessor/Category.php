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
use Shopware\Core\Framework\Uuid\Uuid;

class Category implements DataProcessorInterface
{
    public const DEFAULT_PAGE_TYPE = 'page';
    public const DEFAULT_ROOT_CATEGORY_NAME = 'Root';
    private Pool $locatorPool;
    private ?string $generatedRootId = null;

    /**
     * @param Pool $locatorPool
     */
    public function __construct(Pool $locatorPool)
    {
        $this->locatorPool = $locatorPool;
    }

    /**
     * @return string
     */
    protected function getGeneratedRoot(): string
    {
        if ($this->generatedRootId === null) {
            $this->generatedRootId = Uuid::randomHex();
        }
        return $this->generatedRootId;
    }

    /**
     * {@inheritDoc}
     */
    public function process(array $data, array $config): array
    {
        try {
            $layout = $this->locatorPool->getLocator('layout')->locate($config['layout'] ?? []);
        } catch (InvalidArgumentException $exception) {
            $layout = null;
        }
        if (!$layout) {
            throw new MissingConfigValueException("Layout", "Category");
        }
        $layoutId = $layout->getId();
        try {
            $language = $this->locatorPool->getLocator('language')->locate($config['language'] ?? []);
        } catch (InvalidArgumentException $exception) {
            $language = null;
        }
        if (!$language) {
            throw new MissingConfigValueException("Language", "Category");
        }
        $languageId = $language->getId();
        try {
            $root = $this->locatorPool->getLocator('category')->locate($config['root'] ?? []);
        } catch (InvalidArgumentException $exception) {
            $root = null;
        }
        $rootData = $root
            ? [
                'id' => $root->getId(),
                'name' => [
                    $languageId => $root->getName(),
                ],
                'active' => $root->getActive(),
                'displayNestedProducts' => $root->getDisplayNestedProducts(),
                'type' => $root->getType(),
            ]
            : [
                'id' => $this->getGeneratedRoot(),
                'name' => [
                    $languageId => self::DEFAULT_ROOT_CATEGORY_NAME
                ],
                'active' => true,
                'displayNestedProducts' => true,
                'type' => self::DEFAULT_PAGE_TYPE,
                'translations' => [
                    $languageId => ['name' => self::DEFAULT_ROOT_CATEGORY_NAME]
                ],
            ];
        $rootData['children'] = $this->buildTree($data, $languageId, $layoutId);
        return [$rootData];
    }

    /**
     * @param array $data
     * @param string $languageId
     * @param string $layoutId
     * @return array
     */
    private function buildTree(array $data, string $languageId, string $layoutId): array
    {
        $result = [];
        foreach ($data as $category) {
            $name = $category['name'];
            $processed = $category;
            $processed['name'] = [$languageId => $name];
            $processed['active'] = true;
            $processed['displayNestedProducts'] = true;
            $processed['type'] = self::DEFAULT_PAGE_TYPE;
            $processed['cmsPageId'] = $layoutId;
            $processed['translations'] = [
                $languageId => ['name' => $name]
            ];
            if (isset($category['children'])) {
                $processed['children'] = $this->buildTree($category['children'] ?? [], $languageId, $layoutId);
            }
            $result[] = $processed;
        }
        return $result;
    }
}
