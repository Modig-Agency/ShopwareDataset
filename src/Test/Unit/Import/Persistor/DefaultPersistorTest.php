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

use Modig\Dataset\Import\Persistor\DefaultPersistor;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Write\EntityWriterInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Write\WriteContext;

#[CoversClass(DefaultPersistor::class)]
class DefaultPersistorTest extends TestCase
{
    #[Test]
    public function testPersist()
    {
        $writer = $this->createMock(EntityWriterInterface::class);
        $definition = $this->createMock(EntityDefinition::class);
        $context = $this->createMock(WriteContext::class);
        $defaultPersistor = new DefaultPersistor($writer);
        $writer->expects($this->once())->method('upsert')->willReturn(['entity' => [[], []]]);
        $this->assertEquals(2, $defaultPersistor->persist($definition, [], $context));
    }
}
