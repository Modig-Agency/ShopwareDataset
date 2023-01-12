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

namespace Modig\Dataset\Test\Unit\Exception;

use Modig\Dataset\Exception\TypeValidationException;
use PHPUnit\Framework\TestCase;
use stdClass;

class TypeValidationExceptionTest extends TestCase
{
    /**
     * @covers \Modig\Dataset\Exception\TypeValidationException::__construct()
     */
    public function testConstruct()
    {
        $exception = new TypeValidationException("classname", 'expected', []);
        $expected = 'Invalid type supplied in classname. Expected expected, got array';
        $this->assertEquals($expected, $exception->getMessage());
    }

    /**
     * @covers \Modig\Dataset\Exception\TypeValidationException::__construct()
     */
    public function testConstructWithObject()
    {
        $exception = new TypeValidationException("classname", 'expected', new stdClass());
        $expected = 'Invalid type supplied in classname. Expected expected, got stdClass';
        $this->assertEquals($expected, $exception->getMessage());
    }
}
