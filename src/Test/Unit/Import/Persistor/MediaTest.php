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

namespace Modig\Dataset\Test\Unit\Import\Persistor;

use Modig\Dataset\Import\Persistor\Media;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Content\Media\File\FileSaver;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Write\EntityWriterInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Write\WriteContext;

#[CoversClass(Media::class)]
class MediaTest extends TestCase
{
    #[Test]
    public function testPersist()
    {
        $writer = $this->createMock(EntityWriterInterface::class);
        $fileSaver = $this->createMock(FileSaver::class);
        $definition = $this->createMock(EntityDefinition::class);
        $context = $this->createMock(WriteContext::class);
        $media = new Media($writer, $fileSaver);
        $root = __DIR__ . '/../../_fixtures/images/';
        $data = [
            'to_dave' => [[], []],
            'to_copy' => [
                [
                    'id' => "1",
                    'file' => $root . '1163.jpg'
                ],
                [
                    'id' => "2",
                    'file' => $root . '1164.jpg'
                ],
            ]
        ];
        $writer->expects($this->once())->method('upsert');
        $fileSaver->expects($this->exactly(2))->method('persistFileToMedia');
        $this->assertEquals(2, $media->persist($definition, $data, $context));
    }
}
