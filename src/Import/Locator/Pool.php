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

namespace Modig\Dataset\Import\Locator;

use InvalidArgumentException;
use Modig\Dataset\Validator\ArrayTypeValidator;

class Pool
{
    /**
     * @var LocatorInterface[]
     */
    private array $locators;

    /**
     * @param LocatorInterface[] $locators
     */
    public function __construct(iterable $locators)
    {
        $this->locators = ArrayTypeValidator::getValid($locators, LocatorInterface::class, static::class);
    }

    /**
     * @param string $key
     * @return LocatorInterface
     */
    public function getLocator(string $key): LocatorInterface
    {
        if (isset($this->locators[$key])) {
            return $this->locators[$key];
        }
        throw new InvalidArgumentException("Missing locator with key {$key}");
    }
}
