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
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(NotEmpty::class)]
class NotEmptyTest extends TestCase
{
    private NotEmpty $notEmpty;

    /**
     * Setup tests
     */
    protected function setUp(): void
    {
        $this->notEmpty = new NotEmpty("key", "setting", "error");
    }

    #[Test]
    public function testCollectNotValid()
    {
        $result = $this->notEmpty->collect([]);
        $this->assertCount(1, $result);
        $this->assertEquals('error', $result[0]->getValue());
        $this->assertEquals('setting', $result[0]->getLabel());
        $this->assertFalse($result[0]->isValid());
    }

    #[Test]
    public function testCollect()
    {
        $result = $this->notEmpty->collect(['key' => 'data']);
        $this->assertCount(1, $result);
        $this->assertEquals('data', $result[0]->getValue());
        $this->assertEquals('setting', $result[0]->getLabel());
        $this->assertTrue($result[0]->isValid());
    }
}
