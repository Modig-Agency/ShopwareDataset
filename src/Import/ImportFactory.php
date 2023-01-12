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

namespace Modig\Dataset\Import;

use Modig\Dataset\Validator\ArrayTypeValidator;
use Symfony\Component\Console\Output\OutputInterface;

class ImportFactory
{
    /**
     * @var ImportInterface[]
     */
    private array $entities;

    /**
     * @param iterable $entities
     */
    public function __construct(iterable $entities)
    {
        $this->entities = ArrayTypeValidator::getValid($entities, ImportInterface::class, static::class);
    }

    /**
     * @param string $configFile
     * @param OutputInterface|null $output
     * @return Import
     */
    public function create(string $configFile, ?OutputInterface $output = null): Import
    {
        return new Import($configFile, $this->entities, $output);
    }
}
