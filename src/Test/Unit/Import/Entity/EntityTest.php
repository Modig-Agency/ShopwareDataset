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

namespace Modig\Dataset\Test\Unit\Import\Entity;

use Modig\Dataset\Exception\MissingConfigValueException;
use Modig\Dataset\IMport\Persistor\PersistorInterface;
use Modig\Dataset\Import\ConfigCollector\ConfigCollectorInterface;
use Modig\Dataset\Import\DataProcessor\DataProcessorInterface;
use Modig\Dataset\Import\Entity\Entity;
use Modig\Dataset\Import\FileLoader;
use Modig\Dataset\Import\OutputHandler;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Write\WriteContext;
use Symfony\Component\Console\Output\OutputInterface;

#[CoversClass(Entity::class)]
class EntityTest extends TestCase
{
    private FileLoader|MockObject $fileLoader;
    private ConfigCollectorInterface|MockObject $collector;
    private DataProcessorInterface|MockObject $processor;
    private OutputHandler|MockObject $outputHandler;
    private PersistorInterface|MockObject $persistor;
    private EntityDefinition|MockObject $definition;
    private WriteContext|MockObject $context;
    private OutputInterface|MockObject $output;
    private Entity $entity;

    /**
     * Setup tests
     */
    protected function setUp(): void
    {
        $this->fileLoader = $this->createMock(FileLoader::class);
        $this->collector = $this->createMock(ConfigCollectorInterface::class);
        $this->processor = $this->createMock(DataProcessorInterface::class);
        $this->outputHandler = $this->createMock(OutputHandler::class);
        $this->persistor = $this->createMock(PersistorInterface::class);
        $this->definition = $this->createMock(EntityDefinition::class);
        $this->context = $this->createMock(WriteContext::class);
        $this->output = $this->createMock(OutputInterface::class);
        $this->entity = new Entity(
            $this->fileLoader,
            $this->collector,
            $this->processor,
            $this->outputHandler,
            $this->persistor,
            $this->definition,
            "entity"
        );
    }

    #[Test]
    public function testGetTitle()
    {
        $this->assertEquals('entity', $this->entity->getTitle());
    }

    #[Test]
    public function testImport()
    {
        $this->fileLoader->expects($this->once())->method('getSource')->willReturn('source');
        $this->fileLoader->expects($this->once())->method('getFiles')->willReturn(['file1', 'file2']);
        $this->outputHandler->expects($this->once())->method('createProgressBar')->willReturn(null);
        $this->fileLoader->expects($this->exactly(2))->method('readFile')->willReturn([]);
        $this->outputHandler->expects($this->exactly(2))->method('setProgressBarMessage');
        $this->processor->expects($this->exactly(2))->method('process');
        $this->persistor->expects($this->exactly(2))->method('persist')->willReturnOnConsecutiveCalls(2, 3);
        $this->outputHandler->expects($this->exactly(2))->method('advanceProgressBar');
        $this->outputHandler->expects($this->once())->method('finishProgressBar')
            ->with(null, 'entity 5');
        $this->entity->import([], $this->context, $this->output);
    }

    #[Test]
    public function testImportWithMissingSource()
    {
        $this->fileLoader->expects($this->once())->method('getSource')->willReturn(null);
        $this->expectException(MissingConfigValueException::class);
        $this->entity->import([], $this->context, $this->output);
    }

    #[Test]
    public function testGetConfigValues()
    {
        $this->collector->expects($this->once())->method('collect')->willReturn(['item']);
        $this->assertEquals(['item'], $this->entity->getConfigValues([]));
    }
}
