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

use Modig\Dataset\Import\ConfigCollector\NotEmpty;
use PHPUnit\Framework\TestCase;

class NotEmptyTest extends TestCase
{
    /**
     * @var NotEmpty
     */
    private NotEmpty $notEmpty;

    /**
     * Setup tests
     */
    protected function setUp(): void
    {
        $this->notEmpty = new NotEmpty("key", "setting", "error");
    }

    /**
     * @covers \Modig\Dataset\Import\ConfigCollector\NotEmpty::collect
     * @covers \Modig\Dataset\Import\ConfigCollector\NotEmpty::__construct
     */
    public function testCollectNotValid()
    {
        $result = $this->notEmpty->collect([]);
        $this->assertCount(1, $result);
        $this->assertEquals('error', $result[0]->getValue());
        $this->assertEquals('setting', $result[0]->getLabel());
        $this->assertFalse($result[0]->isValid());
    }

    /**
     * @covers \Modig\Dataset\Import\ConfigCollector\NotEmpty::collect
     * @covers \Modig\Dataset\Import\ConfigCollector\NotEmpty::__construct
     */
    public function testCollect()
    {
        $result = $this->notEmpty->collect(['key' => 'data']);
        $this->assertCount(1, $result);
        $this->assertEquals('data', $result[0]->getValue());
        $this->assertEquals('setting', $result[0]->getLabel());
        $this->assertTrue($result[0]->isValid());
    }
}
