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

use Modig\Dataset\Import\FileLoader;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Bundle;
use Symfony\Component\HttpKernel\KernelInterface;

class FileLoaderTest extends TestCase
{
    /**
     * @var KernelInterface | MockObject
     */
    private KernelInterface $kernel;
    /**
     * @var FileLoader
     */
    private FileLoader $fileLoader;

    /**
     * Setup tests
     */
    protected function setUp(): void
    {
        $this->kernel = $this->createMock(KernelInterface::class);
        $this->fileLoader = new FileLoader($this->kernel);
    }

    /**
     * @covers \Modig\Dataset\Import\FileLoader::getFiles
     * @covers \Modig\Dataset\Import\FileLoader::__construct
     */
    public function testGetFiles()
    {
        $expected = [
            __DIR__ . '/../_fixtures/files/file1.json',
            __DIR__ . '/../_fixtures/files/file2.json'
        ];
        $this->assertEquals($expected, $this->fileLoader->getFiles(__DIR__ . '/../_fixtures/files/*'));
    }

    /**
     * @covers \Modig\Dataset\Import\FileLoader::readFile
     * @covers \Modig\Dataset\Import\FileLoader::__construct
     */
    public function testReadFile()
    {
        $expected = [
            [
                'prop1' => 'value1',
                'prop2' => 'value2'
            ]
        ];
        $this->assertEquals($expected, $this->fileLoader->readFile(__DIR__ . '/../_fixtures/files/file1.json'));
    }

    /**
     * @covers \Modig\Dataset\Import\FileLoader::getSource
     * @covers \Modig\Dataset\Import\FileLoader::__construct
     */
    public function testGetSourceWithoutBundle()
    {
        $this->kernel->expects($this->never())->method('getBundle');
        $this->assertEquals('file', $this->fileLoader->getSource('file'));
    }

    /**
     * @covers \Modig\Dataset\Import\FileLoader::getSource
     * @covers \Modig\Dataset\Import\FileLoader::__construct
     */
    public function testGetSourceWithNullValid()
    {
        $this->kernel->expects($this->never())->method('getBundle');
        $this->assertNull($this->fileLoader->getSource(null));
    }

    /**
     * @covers \Modig\Dataset\Import\FileLoader::getSource
     * @covers \Modig\Dataset\Import\FileLoader::__construct
     */
    public function testGetSourceWithBundle()
    {
        $bundle = $this->createMock(Bundle::class);
        $this->kernel->expects($this->once())->method('getBundle')->willReturn($bundle);
        $bundle->expects($this->once())->method('getPath')->willReturn('path/to/bundle');
        $this->assertEquals('path/to/bundle/file/name', $this->fileLoader->getSource('@bundle/file/name'));
    }
}
