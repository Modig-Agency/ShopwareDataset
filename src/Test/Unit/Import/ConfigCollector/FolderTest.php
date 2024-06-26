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

use InvalidArgumentException;
use Modig\Dataset\Import\ConfigCollector\Folder;
use Modig\Dataset\Import\Locator\LocatorInterface;
use Modig\Dataset\Import\Locator\Pool;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Content\Media\Aggregate\MediaDefaultFolder\MediaDefaultFolderEntity;
use Shopware\Core\Content\Media\Aggregate\MediaFolder\MediaFolderEntity;

#[CoversClass(Folder::class)]
class FolderTest extends TestCase
{
    private Pool|MockObject $pool;
    private Folder $folder;
    private LocatorInterface $entityLocator;

    /**
     * Setup tests
     */
    protected function setUp(): void
    {
        $this->pool = $this->createMock(Pool::class);
        $this->entityLocator = $this->createMock(LocatorInterface::class);
        $this->folder = new Folder($this->pool);
    }

    #[Test]
    public function testCollect()
    {
        $this->pool->expects($this->once())->method('getLocator')->willReturn($this->entityLocator);
        $defaultFolder = $this->createMock(MediaDefaultFolderEntity::class);
        $folder = $this->createMock(MediaFolderEntity::class);
        $this->entityLocator->method('locate')->willReturn($defaultFolder);
        $defaultFolder->expects($this->once())->method('getFolder')->willReturn($folder);
        $folder->expects($this->once())->method('getId')->willReturn('id');
        $folder->expects($this->once())->method('getName')->willReturn('name');
        $expectedValue = 'name (ID: id)';
        $result = $this->folder->collect(['config']);
        $this->assertCount(1, $result);
        $this->assertEquals($expectedValue, $result[0]->getValue());
        $this->assertTrue($result[0]->isValid());
    }

    #[Test]
    public function testCollectMissingFolder()
    {
        $this->pool->expects($this->once())->method('getLocator')->willReturn($this->entityLocator);
        $defaultFolder = $this->createMock(MediaDefaultFolderEntity::class);
        $this->entityLocator->method('locate')->willReturn($defaultFolder);
        $defaultFolder->expects($this->once())->method('getFolder')->willReturn(null);
        $expectedValue = 'Missing Media Destination Folder';
        $result = $this->folder->collect(['config']);
        $this->assertCount(1, $result);
        $this->assertEquals($expectedValue, $result[0]->getValue());
        $this->assertFalse($result[0]->isValid());
    }

    #[Test]
    public function testCollectMissingDefaultFolder()
    {
        $this->pool->expects($this->once())->method('getLocator')->willReturn($this->entityLocator);
        $this->entityLocator->method('locate')->willReturn(null);
        $expectedValue = 'Missing Media Destination Folder';
        $result = $this->folder->collect(['config']);
        $this->assertCount(1, $result);
        $this->assertEquals($expectedValue, $result[0]->getValue());
        $this->assertFalse($result[0]->isValid());
    }

    #[Test]
    public function testCollectMissingLocator()
    {
        $this->pool->expects($this->once())->method('getLocator')
            ->willThrowException($this->createMock(InvalidArgumentException::class));
        $expectedValue = 'Missing Media Destination Folder';
        $result = $this->folder->collect(['config']);
        $this->assertCount(1, $result);
        $this->assertEquals($expectedValue, $result[0]->getValue());
        $this->assertFalse($result[0]->isValid());
    }
}
