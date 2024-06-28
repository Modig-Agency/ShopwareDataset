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
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;

#[CoversClass(Locator::class)]
class LocatorTest extends TestCase
{
    private EntityRepository|MockObject $repository;
    private Locator $locator;
    private EntitySearchResult|MockObject $searchResult;

    /**
     * Setup tests
     */
    protected function setUp(): void
    {
        $this->repository = $this->createMock(EntityRepository::class);
        $this->locator = new Locator($this->repository);
        $this->searchResult = $this->createMock(EntitySearchResult::class);
    }

    #[Test]
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

    #[Test]
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

    #[Test]
    public function testLocateWithNoResult()
    {
        $this->repository->expects($this->once())->method('search')->willReturn($this->searchResult);
        $entity = $this->createMock(ProductEntity::class);
        $entity->method('getId')->willReturn('id');
        $this->searchResult->expects($this->once())->method('first')->willReturn(null);
        $this->assertNull($this->locator->locate(['name' => 'name']));
    }
}
