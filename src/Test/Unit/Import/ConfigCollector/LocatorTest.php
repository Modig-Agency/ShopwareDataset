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
use Modig\Dataset\Import\ConfigCollector\Locator;
use Modig\Dataset\Import\Locator\LocatorInterface;
use Modig\Dataset\Import\Locator\Pool;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopware\Core\System\Language\LanguageEntity;

#[CoversClass(Locator::class)]
class LocatorTest extends TestCase
{
    private Pool|MockObject $pool;
    private Locator $locator;
    private LocatorInterface $entityLocator;

    /**
     * Setup tests
     */
    protected function setUp(): void
    {
        $this->pool = $this->createMock(Pool::class);
        $this->entityLocator = $this->createMock(LocatorInterface::class);
        $this->locator = new Locator($this->pool, "key", "error", "setting");
    }

    #[Test]
    public function testCollect()
    {
        $language = $this->createMock(LanguageEntity::class);
        $language->expects($this->once())->method('getId')->willReturn('id');
        $language->expects($this->once())->method('getName')->willReturn('name');
        $this->entityLocator->expects($this->once())->method('locate')->willReturn($language);
        $this->pool->expects($this->once())->method('getLocator')->with('key')->willReturn($this->entityLocator);
        $expectedValue = 'name (ID: id)';
        $result = $this->locator->collect(['config']);
        $this->assertCount(1, $result);
        $this->assertEquals($expectedValue, $result[0]->getValue());
        $this->assertEquals('setting', $result[0]->getLabel());
        $this->assertTrue($result[0]->isValid());
    }

    #[Test]
    public function testCollectNotEntity()
    {
        $this->pool->expects($this->once())->method('getLocator')->with('key')->willReturn($this->entityLocator);
        $expectedValue = 'error';
        $result = $this->locator->collect(['config']);
        $this->assertCount(1, $result);
        $this->assertEquals($expectedValue, $result[0]->getValue());
        $this->assertEquals('setting', $result[0]->getLabel());
        $this->assertFalse($result[0]->isValid());
    }

    #[Test]
    public function testCollectNoLocator()
    {
        $this->pool->expects($this->once())->method('getLocator')->with('key')
            ->willThrowException($this->createMock(InvalidArgumentException::class));
        $expectedValue = 'error';
        $result = $this->locator->collect(['config']);
        $this->assertCount(1, $result);
        $this->assertEquals($expectedValue, $result[0]->getValue());
        $this->assertEquals('setting', $result[0]->getLabel());
        $this->assertFalse($result[0]->isValid());
    }
}
