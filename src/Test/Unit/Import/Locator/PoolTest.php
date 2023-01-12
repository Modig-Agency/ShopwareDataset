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
use PHPUnit\Framework\TestCase;

class PoolTest extends TestCase
{
    /**
     * @var Pool
     */
    private Pool $pool;

    protected LocatorInterface $locatorOne;
    protected LocatorInterface $locatorTwo;

    /**
     * Setup tests
     */
    protected function setUp(): void
    {
        $this->locatorOne = $this->createMock(LocatorInterface::class);
        $this->locatorTwo = $this->createMock(LocatorInterface::class);
        $this->pool = new Pool(['one' => $this->locatorOne, 'two' => $this->locatorTwo]);
    }

    /**
     * @covers \Modig\Dataset\Import\Locator\Pool::getLocator
     * @covers \Modig\Dataset\Import\Locator\Pool::__construct
     */
    public function testGetLocator()
    {
        $this->assertEquals($this->locatorOne, $this->pool->getLocator("one"));
        $this->assertEquals($this->locatorTwo, $this->pool->getLocator("two"));
    }

    /**
     * @covers \Modig\Dataset\Import\Locator\Pool::getLocator
     * @covers \Modig\Dataset\Import\Locator\Pool::__construct
     */
    public function testGetLocatorMissingLocator()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->pool->getLocator('three');
    }
}
