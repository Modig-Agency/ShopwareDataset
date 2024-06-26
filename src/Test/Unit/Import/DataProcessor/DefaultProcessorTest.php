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

use Modig\Dataset\Import\DataProcessor\DefaultProcessor;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(DefaultProcessor::class)]
class DefaultProcessorTest extends TestCase
{
    #[Test]
    public function testProcess()
    {
        $processor = new DefaultProcessor();
        $this->assertEquals([], $processor->process([], []));
        $this->assertEquals(['data'], $processor->process(['data'], []));
    }
}
