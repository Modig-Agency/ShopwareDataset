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

namespace Modig\Dataset\Validator;

use Modig\Dataset\Exception\TypeValidationException;

class ArrayTypeValidator
{
    /**
     * @param array $types
     * @param string $expected
     * @param string $caller
     * @return array
     */
    public static function getValid(iterable $types, string $expected, string $caller): array
    {
        $arrayTypes = [];
        foreach ($types as $key => $type) {
            if (!$type instanceof $expected) {
                throw new TypeValidationException($caller, $expected, $type);
            }
            $arrayTypes[$key] = $type;
        }
        return $arrayTypes;
    }
}
