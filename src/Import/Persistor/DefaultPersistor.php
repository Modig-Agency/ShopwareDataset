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

namespace Modig\Dataset\Import\Persistor;

use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Write\EntityWriterInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Write\WriteContext;

class DefaultPersistor implements PersistorInterface
{
    private EntityWriterInterface $writer;

    /**
     * @param EntityWriterInterface $writer
     */
    public function __construct(EntityWriterInterface $writer)
    {
        $this->writer = $writer;
    }

    /**
     * {@inheritDoc}
     */
    public function persist(EntityDefinition $definition, array $data, WriteContext $context): int
    {
        $result = $this->writer->upsert(
            $definition,
            $data,
            $context
        );
        return count(array_values($result)[0] ?? []);
    }
}
