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

use InvalidArgumentException;
use Modig\Dataset\Import\Locator\LocatorInterface;
use Modig\Dataset\Import\Locator\Pool;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(Pool::class)]
class PoolTest extends TestCase
{
    private Pool $pool;
    protected LocatorInterface|MockObject $locatorOne;
    protected LocatorInterface|MockObject $locatorTwo;

    /**
     * Setup tests
     */
    protected function setUp(): void
    {
        $this->locatorOne = $this->createMock(LocatorInterface::class);
        $this->locatorTwo = $this->createMock(LocatorInterface::class);
        $this->pool = new Pool(['one' => $this->locatorOne, 'two' => $this->locatorTwo]);
    }

    #[Test]
    public function testGetLocator()
    {
        $this->assertEquals($this->locatorOne, $this->pool->getLocator("one"));
        $this->assertEquals($this->locatorTwo, $this->pool->getLocator("two"));
    }

    #[Test]
    public function testGetLocatorMissingLocator()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->pool->getLocator('three');
    }
}
