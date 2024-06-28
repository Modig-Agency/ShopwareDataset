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

namespace Modig\Dataset\Test\Unit\Validator;

use Modig\Dataset\Exception\TypeValidationException;
use Modig\Dataset\Validator\ArrayTypeValidator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use stdClass;

#[CoversClass(ArrayTypeValidator::class)]
class ArrayTypeValidatorTest extends TestCase
{
    #[Test]
    public function testGetValid()
    {
        $elements = [new stdClass(), new stdClass()];
        $this->assertEquals($elements, ArrayTypeValidator::getValid($elements, stdClass::class, 'caller'));
    }

    #[Test]
    public function testGetValidWithWrongData()
    {
        $elements = [new stdClass(), []];
        $this->expectException(TypeValidationException::class);
        ArrayTypeValidator::getValid($elements, stdClass::class, 'caller');
    }
}
