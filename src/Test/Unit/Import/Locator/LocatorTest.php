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

namespace Modig\Dataset\Test\Unit\Import\Locator;

use Modig\Dataset\Import\Locator\Locator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;

class LocatorTest extends TestCase
{
    /**
     * @var EntityRepositoryInterface | MockObject
     */
    private EntityRepositoryInterface $repository;
    /**
     * @var Locator
     */
    private Locator $locator;
    /**
     * @var EntitySearchResult | MockObject
     */
    private EntitySearchResult $searchResult;

    /**
     * Setup tests
     */
    protected function setUp(): void
    {
        $this->repository = $this->createMock(EntityRepositoryInterface::class);
        $this->locator = new Locator($this->repository);
        $this->searchResult = $this->createMock(EntitySearchResult::class);
    }

    /**
     * @covers \Modig\Dataset\Import\Locator\Locator::locate
     * @covers \Modig\Dataset\Import\Locator\Locator::getKey
     * @covers \Modig\Dataset\Import\Locator\Locator::getContext
     * @covers \Modig\Dataset\Import\Locator\Locator::__construct
     */
    public function testLocateWithValidId()
    {
        $this->repository->expects($this->once())->method('search')->willReturn($this->searchResult);
        $entity = $this->createMock(ProductEntity::class);
        $entity->method('getId')->willReturn('id');
        $this->searchResult->expects($this->once())->method('first')->willReturn($entity);
        $this->assertEquals($entity, $this->locator->locate(['id' => 'id']));
        //call twice to test memoizing
        $this->assertEquals($entity, $this->locator->locate(['id' => 'id']));
    }

    /**
     * @covers \Modig\Dataset\Import\Locator\Locator::locate
     * @covers \Modig\Dataset\Import\Locator\Locator::getKey
     * @covers \Modig\Dataset\Import\Locator\Locator::getContext
     * @covers \Modig\Dataset\Import\Locator\Locator::__construct
     */
    public function testLocateWithoutId()
    {
        $this->repository->expects($this->once())->method('search')->willReturn($this->searchResult);
        $entity = $this->createMock(ProductEntity::class);
        $entity->method('getId')->willReturn('id');
        $this->searchResult->expects($this->once())->method('first')->willReturn($entity);
        $this->assertEquals($entity, $this->locator->locate(['name' => 'name']));
        //call twice to test memoizing
        $this->assertEquals($entity, $this->locator->locate(['name' => 'name']));
    }

    /**
     * @covers \Modig\Dataset\Import\Locator\Locator::locate
     * @covers \Modig\Dataset\Import\Locator\Locator::getKey
     * @covers \Modig\Dataset\Import\Locator\Locator::getContext
     * @covers \Modig\Dataset\Import\Locator\Locator::__construct
     */
    public function testLocateWithNoResult()
    {
        $this->repository->expects($this->once())->method('search')->willReturn($this->searchResult);
        $entity = $this->createMock(ProductEntity::class);
        $entity->method('getId')->willReturn('id');
        $this->searchResult->expects($this->once())->method('first')->willReturn(null);
        $this->assertNull($this->locator->locate(['name' => 'name']));
    }
}
