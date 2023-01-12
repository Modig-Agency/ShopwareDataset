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

use Shopware\Core\Framework\DataAbstractionLayer\Write\WriteContext;
use Symfony\Component\Console\Output\OutputInterface;

interface ImportInterface
{
    /**
     * @return string
     */
    public function getTitle(): string;

    /**
     * @param array $config
     * @param WriteContext $context
     * @param OutputInterface|null $output
     * @return void
     */
    public function import(array $config, WriteContext $context, ?OutputInterface $output = null): void;

    /**
     * @param array $config
     * @return array
     */
    public function getConfigValues(array $config): array;
}
