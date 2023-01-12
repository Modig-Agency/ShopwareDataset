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

use Shopware\Core\Content\Media\File\FileSaver;
use Shopware\Core\Content\Media\File\MediaFile;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Write\EntityWriterInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Write\WriteContext;

class Media implements PersistorInterface
{
    private ?Context $context = null;
    private EntityWriterInterface $writer;
    private FileSaver $fileSaver;

    /**
     * @param EntityWriterInterface $writer
     * @param FileSaver $fileSaver
     */
    public function __construct(EntityWriterInterface $writer, FileSaver $fileSaver)
    {
        $this->writer = $writer;
        $this->fileSaver = $fileSaver;
    }

    /**
     * {@inheritDoc}
     */
    public function persist(EntityDefinition $definition, array $data, WriteContext $context): int
    {
        $dataToSave = $data['to_save'] ?? [];
        $filesToCopy = $data['to_copy'] ?? [];
        $this->writer->upsert($definition, $dataToSave, $context);
        foreach ($filesToCopy as $image) {
            $mediaObject = new MediaFile(
                $image['file'],
                \mime_content_type($image['file']) ?: 'application/octet-stream',
                \pathinfo($image['file'], PATHINFO_EXTENSION),
                \filesize($image['file']) ?: 0
            );
            $this->fileSaver->persistFileToMedia(
                $mediaObject,
                \pathinfo($image['file'], PATHINFO_FILENAME),
                $image['id'],
                $this->getImageSaveContext()
            );
            unset($mediaObject);
        }
        return count($filesToCopy);
    }

    /**
     * @return Context
     */
    private function getImageSaveContext(): Context
    {
        if ($this->context === null) {
            $this->context = Context::createDefaultContext();
            $this->context->scope(Context::SYSTEM_SCOPE, function () {
            });
        }
        return $this->context;
    }
}
