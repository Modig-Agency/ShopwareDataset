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

use Modig\Dataset\Import\ConfigCollector\CompositeConfigCollector;
use Modig\Dataset\Import\ConfigCollector\ConfigCollectorInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(CompositeConfigCollector::class)]
class CompositeConfigCollectorTest extends TestCase
{
    #[Test]
    public function testCollect()
    {
        $collectorOne = $this->createMock(ConfigCollectorInterface::class);
        $collectorTwo = $this->createMock(ConfigCollectorInterface::class);
        $compositeCollector = new CompositeConfigCollector([$collectorOne, $collectorTwo]);
        $collectorOne->expects($this->once())->method('collect')->willReturn(['item1', 'item2']);
        $collectorTwo->expects($this->once())->method('collect')->willReturn(['item3']);
        $expected = ['item1', 'item2', 'item3'];
        $this->assertEquals($expected, $compositeCollector->collect(['config']));
    }
}
