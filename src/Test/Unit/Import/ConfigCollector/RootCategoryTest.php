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

namespace Modig\Dataset\Test\Unit\Import\ConfigCollector;

use InvalidArgumentException;
use Modig\Dataset\Import\ConfigCollector\RootCategory;
use Modig\Dataset\Import\Locator\LocatorInterface;
use Modig\Dataset\Import\Locator\Pool;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Content\Category\CategoryEntity;

#[CoversClass(RootCategory::class)]
class RootCategoryTest extends TestCase
{
    private Pool|MockObject $pool;
    private RootCategory $rootCategory;
    private LocatorInterface|MockObject $entityLocator;

    /**
     * Setup tests
     */
    protected function setUp(): void
    {
        $this->pool = $this->createMock(Pool::class);
        $this->entityLocator = $this->createMock(LocatorInterface::class);
        $this->rootCategory = new RootCategory($this->pool);
    }

    #[Test]
    public function testCollect()
    {
        $this->pool->expects($this->once())->method('getLocator')->willReturn($this->entityLocator);
        $category = $this->createMock(CategoryEntity::class);
        $category->expects($this->once())->method('getId')->willReturn('id');
        $category->expects($this->once())->method('getName')->willReturn('name');
        $this->entityLocator->method('locate')->willReturn($category);
        $expectedValue = 'name (ID: id)';
        $result = $this->rootCategory->collect([]);
        $this->assertCount(1, $result);
        $this->assertEquals($expectedValue, $result[0]->getValue());
        $this->assertTrue($result[0]->isValid());
    }

    #[Test]
    public function testCollectNoCategory()
    {
        $this->pool->expects($this->once())->method('getLocator')->willReturn($this->entityLocator);
        $this->entityLocator->method('locate')->willReturn(null);
        $expectedValue = '<info>None</info> Category named Root will be created';
        $result = $this->rootCategory->collect([]);
        $this->assertCount(1, $result);
        $this->assertEquals($expectedValue, $result[0]->getValue());
        $this->assertTrue($result[0]->isValid());
    }

    #[Test]
    public function testCollectWithException()
    {
        $this->pool->expects($this->once())->method('getLocator')
            ->willThrowException($this->createMock(InvalidArgumentException::class));
        $expectedValue = '<info>None</info> Category named Root will be created';
        $result = $this->rootCategory->collect([]);
        $this->assertCount(1, $result);
        $this->assertEquals($expectedValue, $result[0]->getValue());
        $this->assertTrue($result[0]->isValid());
    }
}
