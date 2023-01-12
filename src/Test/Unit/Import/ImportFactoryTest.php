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

use Modig\Dataset\Import\Import;
use Modig\Dataset\Import\ImportFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\OutputInterface;

class ImportFactoryTest extends TestCase
{
    /**
     * @covers \Modig\Dataset\Import\ImportFactory::create
     * @covers \Modig\Dataset\Import\ImportFactory::__construct
     */
    public function testCreate()
    {
        $output = $this->createMock(OutputInterface::class);
        $importFactory = new ImportFactory([]);
        $this->assertInstanceOf(Import::class, $importFactory->create("file", $output));
    }
}
