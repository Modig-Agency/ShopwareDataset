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

namespace Modig\Dataset\Import\DataProcessor;

use InvalidArgumentException;
use Modig\Dataset\Exception\MissingConfigValueException;
use Modig\Dataset\Import\Locator\Pool;

class Media implements DataProcessorInterface
{
    private Pool $locatorPool;

    /**
     * @param Pool $locatorPool
     */
    public function __construct(Pool $locatorPool)
    {
        $this->locatorPool = $locatorPool;
    }

    /**
     * {@inheritDoc}
     */
    public function process(array $data, array $config): array
    {
        try {
            $productFolder = $this->locatorPool->getLocator('folder')->locate($config['folder'] ?? []);
        } catch (InvalidArgumentException $exception) {
            $productFolder = null;
        }
        if (!$productFolder) {
            throw new MissingConfigValueException("Media Folder", "Media");
        }
        if (!isset($config['images'])) {
            throw new MissingConfigValueException("Image Source", "Media");
        }
        $folder = $productFolder->getFolder()->getId();
        $mediaImagesSource = $config['images'];
        $dataToSave = [];
        $filesToCopy = [];
        foreach ($data as $item) {
            $dataToSave[] = [
                'id' => $item['id'],
                'mediaFolderId' => $folder,
            ];
            $filesToCopy[] = [
                'file' => $mediaImagesSource . $item['file'],
                'id' => $item['id']
            ];
        }
        return [
            'to_save' => $dataToSave,
            'to_copy' => $filesToCopy
        ];
    }
}
