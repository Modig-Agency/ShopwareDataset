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

namespace Modig\Dataset\Test\Unit\Import\DataProcessor;

use Modig\Dataset\Exception\MissingConfigValueException;
use Modig\Dataset\Import\DataProcessor\Media;
use Modig\Dataset\Import\Locator\LocatorInterface;
use Modig\Dataset\Import\Locator\Pool;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Content\Media\Aggregate\MediaDefaultFolder\MediaDefaultFolderEntity;
use Shopware\Core\Content\Media\Aggregate\MediaFolder\MediaFolderEntity;

#[CoversClass(Media::class)]
class MediaTest extends TestCase
{
    private Pool|MockObject $locatorPool;
    private Media $media;
    private LocatorInterface|MockObject $locator;

    /**
     * Setup tests
     */
    protected function setUp(): void
    {
        $this->locatorPool = $this->createMock(Pool::class);
        $this->media = new Media($this->locatorPool);
        $this->locator = $this->createMock(LocatorInterface::class);
    }

    #[Test]
    public function testProcessWithMissingMediaFolder()
    {
        $this->locatorPool->method('getLocator')->willReturn($this->locator);
        $this->locator->method('locate')->willReturn(null);
        $this->expectException(MissingConfigValueException::class);
        $this->media->process([], []);
    }

    #[Test]
    public function testProcessWithLocatorException()
    {
        $this->locatorPool->method('getLocator')
            ->willThrowException($this->createMock(\InvalidArgumentException::class));
        $this->expectException(MissingConfigValueException::class);
        $this->media->process([], []);
    }

    #[Test]
    public function testProcessWithMissingImagesSource()
    {
        $this->locatorPool->method('getLocator')->willReturn($this->locator);
        $folderMock = $this->createMock(MediaFolderEntity::class);
        $this->locator->method('locate')->willReturn($folderMock);
        $this->expectException(MissingConfigValueException::class);
        $this->media->process([], []);
    }

    #[Test]
    public function testProcess()
    {
        $this->locatorPool->method('getLocator')->willReturn($this->locator);
        $defaultFolder = $this->createMock(MediaDefaultFolderEntity::class);
        $folderMock = $this->createMock(MediaFolderEntity::class);
        $folderMock->method('getId')->willReturn('folder');
        $defaultFolder->method('getFolder')->willReturn($folderMock);
        $this->locator->method('locate')->willReturn($defaultFolder);
        $data = [
            [
                'id' => 'id1',
                'file' => 'file1'
            ],
            [
                'id' => 'id2',
                'file' => 'file2'
            ],
        ];
        $expected = [
            'to_save' => [
                [
                    'id' => 'id1',
                    'mediaFolderId' => 'folder'
                ],
                [
                    'id' => 'id2',
                    'mediaFolderId' => 'folder'
                ],
            ],
            'to_copy' => [
                [
                    'file' => 'images/file1',
                    'id' => 'id1'
                ],
                [
                    'file' => 'images/file2',
                    'id' => 'id2'
                ],
            ]
        ];
        $this->assertEquals($expected, $this->media->process($data, ['images' => 'images/']));
    }
}
