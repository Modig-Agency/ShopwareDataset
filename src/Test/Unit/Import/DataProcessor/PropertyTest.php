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
use Modig\Dataset\Import\DataProcessor\Property;
use Modig\Dataset\Import\Locator\LocatorInterface;
use Modig\Dataset\Import\Locator\Pool;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopware\Core\System\Language\LanguageEntity;

class PropertyTest extends TestCase
{
    /**
     * @var Pool | MockObject
     */
    private Pool $locatorPool;
    /**
     * @var Property
     */
    private Property $property;
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
        $this->property = new Property($this->locatorPool);
        $this->locator = $this->createMock(LocatorInterface::class);
    }

    /**
     * @covers \Modig\Dataset\Import\DataProcessor\Property::process
     * @covers \Modig\Dataset\Import\DataProcessor\Property::__construct
     */
    public function testProcessWithMissingLanguage()
    {
        $this->locator->method('locate')->willReturn(null);
        $this->locatorPool->method('getLocator')->willReturn($this->locator);
        $this->expectException(MissingConfigValueException::class);
        $this->property->process([], []);
    }

    /**
     * @covers \Modig\Dataset\Import\DataProcessor\Property::process
     * @covers \Modig\Dataset\Import\DataProcessor\Property::__construct
     */
    public function testProcessWithLocatorException()
    {
        $this->locatorPool->method('getLocator')
            ->willThrowException($this->createMock(\InvalidArgumentException::class));
        $this->expectException(MissingConfigValueException::class);
        $this->property->process([], []);
    }

    /**
     * @covers \Modig\Dataset\Import\DataProcessor\Property::process
     * @covers \Modig\Dataset\Import\DataProcessor\Property::__construct
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
                'name' => 'name1',
                'options' => [
                    [
                        'id' => 'optionId11',
                        'name' => 'option11'
                    ],
                    [
                        'id' => 'optionId12',
                        'name' => 'option12'
                    ],
                ]
            ],
            [
                'id' => 'id2',
                'name' => 'name2',
                'options' => [
                    [
                        'id' => 'optionId21',
                        'name' => 'option21'
                    ],
                ]
            ],
        ];
        $expected = [
            [
                'id' => 'id1',
                'name' => [
                    'language_id' => 'name1'
                ],
                'sortingType' => 'alphanumeric',
                'displayType' => 'text',
                'options' => [
                    [
                        'id' => 'optionId11',
                        'name' => [
                            'language_id' => 'option11'
                        ],
                        'translations' => [
                            'language_id' => ['name' => 'option11']
                        ]
                    ],
                    [
                        'id' => 'optionId12',
                        'name' => [
                            'language_id' => 'option12'
                        ],
                        'translations' => [
                            'language_id' => ['name' => 'option12']
                        ]
                    ]
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
                'sortingType' => 'alphanumeric',
                'displayType' => 'text',
                'options' => [
                    [
                        'id' => 'optionId21',
                        'name' => [
                            'language_id' => 'option21'
                        ],
                        'translations' => [
                            'language_id' => ['name' => 'option21']
                        ]
                    ],
                ],
                'translations' => [
                    'language_id' => ['name' => 'name2']
                ]
            ]
        ];
        $this->assertEquals($expected, $this->property->process($data, []));
    }
}
