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

namespace Modig\Dataset\Exception;

use InvalidArgumentException;
use Throwable;

class TypeValidationException extends InvalidArgumentException
{
    public const PATTERN = 'Invalid type supplied in %s. Expected %s, got %s';

    /**
     * TypeValidationException constructor.
     * @param string $class
     * @param string $expectedType
     * @param $actual
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(
        string $class,
        string $expectedType,
        $actual,
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct(
            sprintf(
                self::PATTERN,
                $class,
                $expectedType,
                is_object($actual) ? get_class($actual) : gettype($actual)
            ),
            $code,
            $previous
        );
    }
}
