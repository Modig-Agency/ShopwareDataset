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

namespace Modig\Dataset\Test\Unit\Console\Command;

use Modig\Dataset\Console\Command\Import;
use Modig\Dataset\Import\ConfigItem;
use Modig\Dataset\Import\Import as ImportInstance;
use Modig\Dataset\Import\ImportFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[CoversClass(Import::class)]
class ImportTest extends TestCase
{
    private ImportFactory|MockObject $importFactory;
    private InputInterface|MockObject $input;
    private OutputInterface|MockObject $output;
    private Import $importCommand;
    private ImportInstance $import;

    /**
     * Setup tests
     */
    protected function setUp(): void
    {
        $this->importFactory = $this->createMock(ImportFactory::class);
        $this->input = $this->createMock(InputInterface::class);
        $this->output = $this->createMock(OutputInterface::class);
        $this->import = $this->createMock(ImportInstance::class);
        $this->importCommand = new Import($this->importFactory);
    }

    #[Test]
    public function testExecuteWithValidConfigAndRunOption()
    {
        $this->input->method('getArgument')->willReturn('file');
        $this->input->method('getOption')->willReturn(true);
        $this->importFactory->method('create')->willReturn($this->import);
        $this->output->expects($this->never())->method('writeln');
        $this->import->expects($this->once())->method('isConfigValid')->willReturn(true);
        $this->import->expects($this->once())->method('import');
        $this->assertEquals(0, $this->importCommand->run($this->input, $this->output));
    }

    #[Test]
    public function testExecuteWithNotValidConfigAndRunOption()
    {
        $this->input->method('getArgument')->willReturn('file');
        $this->input->method('getOption')->willReturn(true);
        $this->importFactory->method('create')->willReturn($this->import);
        $this->import->expects($this->once())->method('isConfigValid')->willReturn(false);
        $this->import->expects($this->never())->method('import');
        $formatter = $this->createMock(OutputFormatterInterface::class);
        $formatter->method('isDecorated')->willReturn(false);
        $this->output->method('getFormatter')->willReturn($formatter);
        $this->assertEquals(1, $this->importCommand->run($this->input, $this->output));
    }

    #[Test]
    public function testExecuteWithValidConfigAndNoRunOption()
    {
        $this->input->method('getArgument')->willReturn('file');
        $this->input->method('getOption')->willReturn(false);
        $this->importFactory->method('create')->willReturn($this->import);
        $this->import->expects($this->once())->method('getConfigSettings')->willReturn([
            [new ConfigItem('', '', true)],
            [new ConfigItem('', '', false), new ConfigItem('', '', true)],
        ]);
        $this->import->expects($this->never())->method('import');
        $this->import->expects($this->once())->method('isConfigValid')->willReturn(true);
        $formatter = $this->createMock(OutputFormatterInterface::class);
        $formatter->method('isDecorated')->willReturn(false);
        $this->output->method('getFormatter')->willReturn($formatter);
        $this->assertEquals(0, $this->importCommand->run($this->input, $this->output));
    }

    #[Test]
    public function testExecuteWithNotValidConfigAndNoRunOption()
    {
        $this->input->method('getArgument')->willReturn('file');
        $this->input->method('getOption')->willReturn(false);
        $this->importFactory->method('create')->willReturn($this->import);
        $this->import->expects($this->once())->method('getConfigSettings')->willReturn([
            [new ConfigItem('', '', true)],
            [new ConfigItem('', '', false), new ConfigItem('', '', true)],
        ]);
        $this->import->expects($this->never())->method('import');
        $this->import->expects($this->once())->method('isConfigValid')->willReturn(false);
        $formatter = $this->createMock(OutputFormatterInterface::class);
        $formatter->method('isDecorated')->willReturn(false);
        $this->output->method('getFormatter')->willReturn($formatter);
        $this->assertEquals(1, $this->importCommand->run($this->input, $this->output));
    }
}
