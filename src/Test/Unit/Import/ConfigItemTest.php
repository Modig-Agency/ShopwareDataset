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

namespace Modig\Dataset\Test\Unit\Import;

use Modig\Dataset\Import\ConfigItem;
use PHPUnit\Framework\TestCase;

class ConfigItemTest extends TestCase
{
    /**
     * @var ConfigItem
     */
    private ConfigItem $configItem;

    /**
     * Setup tests
     */
    protected function setUp(): void
    {
        $this->configItem = new ConfigItem("label", "value", true);
    }

    /**
     * @covers \Modig\Dataset\Import\ConfigItem::getLabel
     * @covers \Modig\Dataset\Import\ConfigItem::__construct
     */
    public function testGetLabel()
    {
        $this->assertEquals('label', $this->configItem->getLabel());
    }

    /**
     * @covers \Modig\Dataset\Import\ConfigItem::getValue
     * @covers \Modig\Dataset\Import\ConfigItem::__construct
     */
    public function testGetValue()
    {
        $this->assertEquals('value', $this->configItem->getValue());
    }

    /**
     * @covers \Modig\Dataset\Import\ConfigItem::isValid
     * @covers \Modig\Dataset\Import\ConfigItem::__construct
     */
    public function testIsValid()
    {
        $this->assertTrue($this->configItem->isValid());
    }
}
