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

use Modig\Dataset\Import\ConfigCollector\Source;
use Modig\Dataset\Import\FileLoader;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SourceTest extends TestCase
{
    /**
     * @var FileLoader | MockObject
     */
    private FileLoader $fileLoader;
    /**
     * @var Source
     */
    private Source $source;

    /**
     * Setup tests
     */
    protected function setUp(): void
    {
        $this->fileLoader = $this->createMock(FileLoader::class);
        $this->source = new Source($this->fileLoader, "key", "error", "setting");
    }

    /**
     * @covers \Modig\Dataset\Import\ConfigCollector\Source::collect
     * @covers \Modig\Dataset\Import\ConfigCollector\Source::__construct
     */
    public function testCollect()
    {
        $this->fileLoader->expects($this->once())->method('getSource')
            ->willReturn(__DIR__ . '/../../_fixtures/files');
        $result = $this->source->collect(['key' => 'folder']);
        $this->assertCount(1, $result);
        $this->assertTrue($result[0]->isValid());
        $this->assertStringContainsString('2 file(s)', $result[0]->getValue());
        $this->assertEquals('setting', $result[0]->getLabel());
    }

    /**
     * @covers \Modig\Dataset\Import\ConfigCollector\Source::collect
     * @covers \Modig\Dataset\Import\ConfigCollector\Source::__construct
     */
    public function testCollectNotValid()
    {
        $this->fileLoader->expects($this->once())->method('getSource')
            ->willReturn('dummy');
        $result = $this->source->collect(['key' => 'folder']);
        $this->assertCount(1, $result);
        $this->assertFalse($result[0]->isValid());
        $this->assertStringContainsString('error', $result[0]->getValue());
        $this->assertEquals('setting', $result[0]->getLabel());
    }
}
