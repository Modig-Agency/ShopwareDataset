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

namespace Modig\Dataset\Test\Unit\Import\DataProcessor;

use InvalidArgumentException;
use Modig\Dataset\Exception\MissingConfigValueException;
use Modig\Dataset\Import\DataProcessor\Category;
use Modig\Dataset\Import\Locator\LocatorInterface;
use Modig\Dataset\Import\Locator\Pool;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Content\Category\CategoryEntity;
use Shopware\Core\Content\Cms\CmsPageEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\System\Language\LanguageEntity;

class CategoryTest extends TestCase
{
    /**
     * @var Pool | MockObject
     */
    private Pool $locatorPool;
    /**
     * @var Category
     */
    private Category $category;

    /**
     * Setup tests
     */
    protected function setUp(): void
    {
        $this->locatorPool = $this->createMock(Pool::class);
        $this->category = new Category($this->locatorPool);
    }

    /**
     * @covers \Modig\Dataset\Import\DataProcessor\Category::process
     * @covers \Modig\Dataset\Import\DataProcessor\Category::__construct
     */
    public function testProcessWithLayoutLocatorException()
    {
        $this->locatorPool->expects($this->once())->method('getLocator')
            ->willThrowException($this->createMock(InvalidArgumentException::class));
        $this->expectException(MissingConfigValueException::class);
        $this->category->process([], []);
    }

    /**
     * @covers \Modig\Dataset\Import\DataProcessor\Category::process
     * @covers \Modig\Dataset\Import\DataProcessor\Category::__construct
     */
    public function testProcessWithMissingLayout()
    {
        $this->locatorPool->expects($this->once())->method('getLocator')->willReturn($this->getLocatorMock(null));
        $this->expectException(MissingConfigValueException::class);
        $this->category->process([], []);
    }

    /**
     * @covers \Modig\Dataset\Import\DataProcessor\Category::process
     * @covers \Modig\Dataset\Import\DataProcessor\Category::__construct
     */
    public function testProcessWithLanguageLocatorException()
    {
        $this->locatorPool->expects($this->exactly(2))->method('getLocator')
            ->will($this->onConsecutiveCalls(
                $this->getLocatorMock($this->getLayoutMock()),
                $this->throwException($this->createMock(InvalidArgumentException::class))
            ));
        $this->expectException(MissingConfigValueException::class);
        $this->category->process([], []);
    }

    /**
     * @covers \Modig\Dataset\Import\DataProcessor\Category::process
     * @covers \Modig\Dataset\Import\DataProcessor\Category::__construct
     */
    public function testProcessWithMissingLanguage()
    {
        $this->locatorPool->expects($this->exactly(2))->method('getLocator')
            ->will($this->onConsecutiveCalls(
                $this->getLocatorMock($this->getLayoutMock()),
                $this->getLocatorMock(null)
            ));
        $this->expectException(MissingConfigValueException::class);
        $this->category->process([], []);
    }

    /**
     * @covers \Modig\Dataset\Import\DataProcessor\Category::process
     * @covers \Modig\Dataset\Import\DataProcessor\Category::buildTree
     * @covers \Modig\Dataset\Import\DataProcessor\Category::getGeneratedRoot
     * @covers \Modig\Dataset\Import\DataProcessor\Category::__construct
     */
    public function testProcessWithCategoryLocatorException()
    {
        $this->locatorPool->expects($this->exactly(3))->method('getLocator')
            ->will($this->onConsecutiveCalls(
                $this->getLocatorMock($this->getLayoutMock()),
                $this->getLocatorMock($this->getLanguageMock()),
                $this->throwException($this->createMock(InvalidArgumentException::class))
            ));
        $result = $this->category->process($this->getCategoryData(), []);
        $this->assertCount(1, $result);
        $this->assertEquals(['language_id' => 'Root'], $result[0]['name']);
        $this->assertTrue($result[0]['active']);
        $this->assertEquals($this->getExpectedTree(), $result[0]['children']);
    }

    /**
     * @covers \Modig\Dataset\Import\DataProcessor\Category::process
     * @covers \Modig\Dataset\Import\DataProcessor\Category::buildTree
     * @covers \Modig\Dataset\Import\DataProcessor\Category::getGeneratedRoot
     * @covers \Modig\Dataset\Import\DataProcessor\Category::__construct
     */
    public function testProcessWithNoRootSpecified()
    {
        $this->locatorPool->expects($this->exactly(3))->method('getLocator')
            ->will($this->onConsecutiveCalls(
                $this->getLocatorMock($this->getLayoutMock()),
                $this->getLocatorMock($this->getLanguageMock()),
                $this->getLocatorMock(null),
            ));
        $result = $this->category->process($this->getCategoryData(), []);
        $this->assertCount(1, $result);
        $this->assertEquals(['language_id' => 'Root'], $result[0]['name']);
        $this->assertTrue($result[0]['active']);
        $this->assertEquals($this->getExpectedTree(), $result[0]['children']);
    }

    /**
     * @covers \Modig\Dataset\Import\DataProcessor\Category::process
     * @covers \Modig\Dataset\Import\DataProcessor\Category::buildTree
     * @covers \Modig\Dataset\Import\DataProcessor\Category::__construct
     */
    public function testProcessWithRoot()
    {
        $this->locatorPool->expects($this->exactly(3))->method('getLocator')
            ->will($this->onConsecutiveCalls(
                $this->getLocatorMock($this->getLayoutMock()),
                $this->getLocatorMock($this->getLanguageMock()),
                $this->getLocatorMock($this->getCategoryMock()),
            ));
        $expected = [
            [
                'id' => 'category_id',
                'name' => ['language_id' => 'Root category'],
                'active' => true,
                'displayNestedProducts' => true,
                'type' => 'page',
                'children' => $this->getExpectedTree()
            ]
        ];
        $this->assertEquals($expected, $this->category->process($this->getCategoryData(), []));
    }

    /**
     * @return MockObject|CategoryEntity
     */
    private function getCategoryMock(): CategoryEntity
    {
        $mock = $this->createMock(CategoryEntity::class);
        $mock->method('getName')->willReturn('Root category');
        $mock->method('getId')->willReturn('category_id');
        $mock->method('getActive')->willReturn(true);
        $mock->method('getDisplayNestedProducts')->willReturn(true);
        $mock->method('getType')->willReturn('page');
        return $mock;
    }

    /**
     * @return array[]
     */
    private function getCategoryData(): array
    {
        return [
            [
                'name' => 'category1',
                'children' => [
                    [
                        'name' => 'category11',
                        'children' => [
                            [
                                'name' => 'category111'
                            ]
                        ]
                    ],
                    [
                        'name' => 'category12'
                    ],
                ]
            ]
        ];
    }

    /**
     * @return array[]
     */
    private function getExpectedTree(): array
    {
        return [
            [
                'name' => ['language_id' => 'category1'],
                'active' => true,
                'displayNestedProducts' => true,
                'type' => 'page',
                'cmsPageId' => 'layout_id',
                'translations' => [
                    'language_id' => ['name' => 'category1']
                ],
                'children' => [
                    [
                        'name' => ['language_id' => 'category11'],
                        'active' => true,
                        'displayNestedProducts' => true,
                        'type' => 'page',
                        'cmsPageId' => 'layout_id',
                        'translations' => [
                            'language_id' => ['name' => 'category11']
                        ],
                        'children' => [
                            [
                                'name' => ['language_id' => 'category111'],
                                'active' => true,
                                'displayNestedProducts' => true,
                                'type' => 'page',
                                'cmsPageId' => 'layout_id',
                                'translations' => [
                                    'language_id' => ['name' => 'category111']
                                ],
                            ]
                        ]
                    ],
                    [
                        'name' => ['language_id' => 'category12'],
                        'active' => true,
                        'displayNestedProducts' => true,
                        'type' => 'page',
                        'cmsPageId' => 'layout_id',
                        'translations' => [
                            'language_id' => ['name' => 'category12']
                        ],
                    ],
                ]
            ]
        ];
    }

    /**
     * @param Entity|null $result
     * @return LocatorInterface|MockObject
     */
    private function getLocatorMock(?Entity $result)
    {
        $mock = $this->createMock(LocatorInterface::class);
        $mock->method('locate')->willReturn($result);
        return $mock;
    }

    /**
     * @return MockObject|CmsPageEntity
     */
    private function getLayoutMock(): CmsPageEntity
    {
        $mock = $this->createMock(CmsPageEntity::class);
        $mock->method('getId')->willReturn('layout_id');
        return $mock;
    }

    /**
     * @return MockObject|LanguageEntity
     */
    private function getLanguageMock()
    {
        $mock = $this->createMock(LanguageEntity::class);
        $mock->method('getId')->willReturn('language_id');
        return $mock;
    }
}
