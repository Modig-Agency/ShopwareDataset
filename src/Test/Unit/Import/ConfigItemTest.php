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
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(ConfigItem::class)]
class ConfigItemTest extends TestCase
{
    private ConfigItem $configItem;

    /**
     * Setup tests
     */
    protected function setUp(): void
    {
        $this->configItem = new ConfigItem("label", "value", true);
    }

    #[Test]
    public function testGetLabel()
    {
        $this->assertEquals('label', $this->configItem->getLabel());
    }

    #[Test]
    public function testGetValue()
    {
        $this->assertEquals('value', $this->configItem->getValue());
    }

    #[Test]
    public function testIsValid()
    {
        $this->assertTrue($this->configItem->isValid());
    }
}
