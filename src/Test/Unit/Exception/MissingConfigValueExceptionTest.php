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

use Modig\Dataset\Exception\MissingConfigValueException;
use PHPUnit\Framework\TestCase;

class MissingConfigValueExceptionTest extends TestCase
{
    /**
     * @covers \Modig\Dataset\Exception\MissingConfigValueException::__construct()
     */
    public function testConstruct()
    {
        $exception = new MissingConfigValueException("setting", 'import');
        $expected = 'Missing "setting" for "import" import';
        $this->assertEquals($expected, $exception->getMessage());
    }
}
