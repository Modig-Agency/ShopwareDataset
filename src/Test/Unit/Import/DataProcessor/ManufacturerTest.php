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
use Modig\Dataset\Import\DataProcessor\Manufacturer;
use Modig\Dataset\Import\Locator\LocatorInterface;
use Modig\Dataset\Import\Locator\Pool;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopware\Core\System\Language\LanguageEntity;

class ManufacturerTest extends TestCase
{
    /**
     * @var Pool | MockObject
     */
    private Pool $locatorPool;
    /**
     * @var Manufacturer
     */
    private Manufacturer $manufacturer;
    /**
     * @var LocatorInterface  | MockObject
     */
    private LocatorInterface $locator;

    /**
     * Setup tests
     */
    protected function setUp(): void
    {
        $this->locatorPool = $this->createMock(Pool::class);
        $this->manufacturer = new Manufacturer($this->locatorPool);
        $this->locator = $this->createMock(LocatorInterface::class);
    }

    /**
     * @covers \Modig\Dataset\Import\DataProcessor\Manufacturer::process
     * @covers \Modig\Dataset\Import\DataProcessor\Manufacturer::__construct
     */
    public function testProcessWithMissingLanguage()
    {
        $this->locator->method('locate')->willReturn(null);
        $this->locatorPool->method('getLocator')->willReturn($this->locator);
        $this->expectException(MissingConfigValueException::class);
        $this->manufacturer->process([], []);
    }

    /**
     * @covers \Modig\Dataset\Import\DataProcessor\Manufacturer::process
     * @covers \Modig\Dataset\Import\DataProcessor\Manufacturer::__construct
     */
    public function testProcessWithLocatorException()
    {
        $this->locatorPool->method('getLocator')
            ->willThrowException($this->createMock(\InvalidArgumentException::class));
        $this->expectException(MissingConfigValueException::class);
        $this->manufacturer->process([], []);
    }

    /**
     * @covers \Modig\Dataset\Import\DataProcessor\Manufacturer::process
     * @covers \Modig\Dataset\Import\DataProcessor\Manufacturer::__construct
     */
    public function testProcess()
    {
        $language = $this->createMock(LanguageEntity::class);
        $language->method('getId')->willReturn('language_id');
        $this->locator->method('locate')->willReturn($language);
        $this->locatorPool->method('getLocator')->willReturn($this->locator);
        $data = [
            [
                'id' => 'id1',
                'name' => 'name1'
            ],
            [
                'id' => 'id2',
                'name' => 'name2'
            ],
        ];
        $expected = [
            [
                'id' => 'id1',
                'name' => [
                    'language_id' => 'name1'
                ],
                'translations' => [
                    'language_id' => ['name' => 'name1']
                ]
            ],
            [
                'id' => 'id2',
                'name' => [
                    'language_id' => 'name2'
                ],
                'translations' => [
                    'language_id' => ['name' => 'name2']
                ]
            ]
        ];
        $this->assertEquals($expected, $this->manufacturer->process($data, []));
    }
}
