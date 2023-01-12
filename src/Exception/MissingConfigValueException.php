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

class MissingConfigValueException extends InvalidArgumentException
{
    public const ERROR_MESSAGE_MASK = 'Missing "%s" for "%s" import';

    /**
     * @param string $setting
     * @param string $import
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(string $setting, string $import, int $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf(self::ERROR_MESSAGE_MASK, $setting, $import), $code, $previous);
    }
}
