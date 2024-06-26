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
use Modig\Dataset\Import\DataProcessor\Product;
use Modig\Dataset\Import\Locator\LocatorInterface;
use Modig\Dataset\Import\Locator\Pool;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\IdSearchResult;
use Shopware\Core\System\Language\LanguageEntity;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;
use Shopware\Core\System\Tax\TaxEntity;

#[CoversClass(Product::class)]
class ProductTest extends TestCase
{
    private Pool|MockObject $locatorPool;
    private EntityRepository|MockObject $repository;
    private IdSearchResult|MockObject $idsSearch;
    private Product $product;

    /**
     * Setup tests
     */
    protected function setUp(): void
    {
        $this->locatorPool = $this->createMock(Pool::class);
        $this->repository = $this->createMock(EntityRepository::class);
        $this->idsSearch = $this->createMock(IdSearchResult::class);
        $this->product = new Product($this->locatorPool, $this->repository);
    }

    #[Test]
    public function testProcessWithTaxLocatorException()
    {
        $this->repository->expects($this->never())->method('searchIds');
        $this->locatorPool->method('getLocator')
            ->willThrowException($this->createMock(InvalidArgumentException::class));
        $this->expectException(MissingConfigValueException::class);
        $this->product->process([], []);
    }

    #[Test]
    public function testProcessWithNoTax()
    {
        $this->repository->expects($this->never())->method('searchIds');
        $this->locatorPool->method('getLocator')->willReturn($this->getLocatorMock(null));
        $this->expectException(MissingConfigValueException::class);
        $this->product->process([], []);
    }

    #[Test]
    public function testProcessWithLanguageLocatorException()
    {
        $counter = 0;
        $this->repository->expects($this->never())->method('searchIds');
        $this->locatorPool->expects($this->exactly(2))->method('getLocator')
            ->willReturnCallback(
                function () use (&$counter) {
                    $counter++;
                    if ($counter === 1) {
                        return $this->getLocatorMock($this->getTaxMock());
                    }
                    throw $this->createMock(InvalidArgumentException::class);
                }
            );
        $this->expectException(MissingConfigValueException::class);
        $this->product->process([], []);
    }

    #[Test]
    public function testProcessWithMissingLanguage()
    {
        $this->repository->expects($this->never())->method('searchIds');
        $this->locatorPool->expects($this->exactly(2))->method('getLocator')
            ->willReturnCallback(
                function () use (&$counter) {
                    $counter++;
                    return $counter === 1
                        ? $this->getLocatorMock($this->getTaxMock())
                        : $this->getLocatorMock(null);
                }
            );
        $this->expectException(MissingConfigValueException::class);
        $this->product->process([], []);
    }

    #[Test]
    public function testProcessWithSalesChannelLocatorException()
    {
        $this->repository->expects($this->never())->method('searchIds');
        $this->locatorPool->expects($this->exactly(3))->method('getLocator')
            ->willReturnCallback(
                function () use (&$counter) {
                    $counter++;
                    return match ($counter) {
                        1 => $this->getLocatorMock($this->getTaxMock()),
                        2 => $this->getLocatorMock($this->getLanguageMock()),
                        default => throw $this->createMock(InvalidArgumentException::class),
                    };
                }
            );
        $this->expectException(MissingConfigValueException::class);
        $this->product->process([], []);
    }

    #[Test]
    public function testProcessWithMissingSalesChannel()
    {
        $this->repository->expects($this->never())->method('searchIds');
        $this->locatorPool->expects($this->exactly(3))->method('getLocator')
            ->willReturnCallback(
                function () use (&$counter) {
                    $counter++;
                    return match ($counter) {
                        1 => $this->getLocatorMock($this->getTaxMock()),
                        2 => $this->getLocatorMock($this->getLanguageMock()),
                        default => $this->getLocatorMock(null),
                    };
                }
            );
        $this->expectException(MissingConfigValueException::class);
        $this->product->process([], []);
    }

    #[Test]
    public function testProcess()
    {
        $this->repository->method('searchIds')->willReturn($this->idsSearch);
        $this->locatorPool->expects($this->exactly(3))->method('getLocator')
            ->willReturnCallback(
                function () use (&$counter) {
                    $counter++;
                    return match ($counter) {
                        1 => $this->getLocatorMock($this->getTaxMock()),
                        2 => $this->getLocatorMock($this->getLanguageMock()),
                        default => $this->getLocatorMock($this->getSalesChannelMock()),
                    };
                }
            );

        $productData = [
            [
                'id' => 'product1_id',
                'name' => 'product1',
                'description' => 'description1',
                'price' => 100,
                'children' => [
                    [
                        'id' => 'child11',
                    ],
                    [
                        'id' => 'child12',
                    ],
                ],
                'visibilities' => [
                    [
                        'id' => 'visibility_id',
                    ]
                ]
            ],
            [
                'id' => 'product2_id',
                'name' => 'product2',
                'price' => 50,
            ],
            [
                'id' => 'product3_id',
                'name' => 'product2',
                'price' => 50,
            ]
        ];
        $this->idsSearch->method('getIds')->willReturn(['product3_id']);
        $result = $this->product->process($productData, ['stock' => 50]);
        $this->assertCount(2, $result);
        $this->assertEquals(['language_id' => 'description1'], $result[0]['description']);
        $this->assertEquals(['language_id' => 'product1'], $result[0]['name']);
        $this->assertEquals(50, $result[0]['stock']);
        $this->assertEquals('sales_channel_id', $result[0]['visibilities'][0]['salesChannelId']);
        $this->assertEquals(100, $result[0]['price'][0]['gross']);
        $this->assertEquals(100 / 1.2, $result[0]['price'][0]['net']);
        $this->assertCount(2, $result[0]['children']);
        $this->assertEquals(50, $result[0]['children'][0]['stock']);

        $this->assertEquals(['language_id' => ''], $result[1]['description']);
        $this->assertEquals(50, $result[1]['stock']);
        $this->assertArrayNotHasKey('children', $result[1]);
    }

    #[Test]
    private function getLocatorMock(?Entity $result)
    {
        $mock = $this->createMock(LocatorInterface::class);
        $mock->method('locate')->willReturn($result);
        return $mock;
    }

    /**
     * @return MockObject|TaxEntity
     * @throws Exception
     */
    private function getTaxMock(): MockObject|TaxEntity
    {
        $mock = $this->createMock(TaxEntity::class);
        $mock->method('getId')->willReturn('id');
        $mock->method('getTaxRate')->willReturn(20.00);
        return $mock;
    }

    /**
     * @return MockObject|LanguageEntity
     * @throws Exception
     */
    private function getLanguageMock(): LanguageEntity|MockObject
    {
        $mock = $this->createMock(LanguageEntity::class);
        $mock->method('getId')->willReturn('language_id');
        return $mock;
    }

    /**
     * @return MockObject|SalesChannelEntity
     * @throws Exception
     */
    private function getSalesChannelMock(): SalesChannelEntity|MockObject
    {
        $mock = $this->createMock(SalesChannelEntity::class);
        $mock->method('getId')->willReturn('sales_channel_id');
        return $mock;
    }
}
