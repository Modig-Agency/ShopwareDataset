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

use InvalidArgumentException;
use Modig\Dataset\Import\ConfigItem;
use Modig\Dataset\Import\Import;
use Modig\Dataset\Import\ImportInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\OutputInterface;

#[CoversClass(Import::class)]
class ImportTest extends TestCase
{
    private OutputInterface|MockObject $output;

    /**
     * Setup tests
     */
    protected function setUp(): void
    {
        $this->output = $this->createMock(OutputInterface::class);
    }

    #[Test]
    public function testImportWithWrongConfigFile()
    {
        $import = new Import('missing-file', [$this->getImportEntityMock()], $this->output);
        $this->expectException(InvalidArgumentException::class);
        $import->import();
    }

    #[Test]
    public function testImportWithValidConfig()
    {
        $entityOne = $this->getImportEntityMock();
        $entityTwo = $this->getImportEntityMock();
        $entityThree = $this->getImportEntityMock();

        $entityOne->expects($this->once())->method('import');
        $entityOne->expects($this->once())->method('getConfigValues')->willReturn([]);

        $entityTwo->expects($this->once())->method('import');
        $entityTwo->expects($this->once())->method('getConfigValues')->willReturn([new ConfigItem('', '', true)]);

        $entityThree->expects($this->never())->method('import');
        $entityThree->expects($this->never())->method('getConfigValues');

        $import = new Import(
            __DIR__ . '/../_fixtures/import/config1.yml',
            [
                'entity1' => $entityOne,
                'entity2' => $entityTwo,
                'entity3' => $entityThree
            ],
            $this->output
        );
        $import->import();
    }

    #[Test]
    public function testImportWithNotValidValidConfig()
    {
        $entityOne = $this->getImportEntityMock();
        $entityTwo = $this->getImportEntityMock();
        $entityThree = $this->getImportEntityMock();

        $entityOne->expects($this->never())->method('import');
        $entityOne->expects($this->once())->method('getConfigValues')->willReturn([[new ConfigItem('', '', true)]]);

        $entityTwo->expects($this->never())->method('import');
        $entityTwo->expects($this->once())->method('getConfigValues')->willReturn([new ConfigItem('', '', false)]);

        $entityThree->expects($this->never())->method('import');
        $entityThree->expects($this->never())->method('getConfigValues');

        $import = new Import(
            __DIR__ . '/../_fixtures/import/config1.yml',
            [
                'entity1' => $entityOne,
                'entity2' => $entityTwo,
                'entity3' => $entityThree
            ],
            $this->output
        );
        $import->import();
    }

    /**
     * @return ImportInterface| MockObject
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    private function getImportEntityMock(): ImportInterface|MockObject
    {
        return $this->createMock(ImportInterface::class);
    }
}
