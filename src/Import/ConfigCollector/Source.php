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

namespace Modig\Dataset\Import\ConfigCollector;

use Modig\Dataset\Import\ConfigItem;
use Modig\Dataset\Import\FileLoader;

class Source implements ConfigCollectorInterface
{
    private FileLoader $fileLoader;
    private string $key;
    private string $errorMessage;
    private string $settingName;

    /**
     * @param FileLoader $fileLoader
     * @param string $key
     * @param string $errorMessage
     * @param string $settingName
     */
    public function __construct(FileLoader $fileLoader, string $key, string $errorMessage, string $settingName)
    {
        $this->fileLoader = $fileLoader;
        $this->key = $key;
        $this->errorMessage = $errorMessage;
        $this->settingName = $settingName;
    }

    /**
     * {@inheritDoc}
     */
    public function collect(array $config): array
    {
        $source = $this->fileLoader->getSource($config[$this->key] ?? '');
        $globSource = is_dir($source) ? rtrim($source, '/') . '/*' : $source;
        $files = glob($globSource);
        $total = count($files);
        $valid = !!$total;
        $value = $valid
            ? $source . ' (' . $total . ' file(s) available)'
            : $this->errorMessage . ' ' . ($source ?? '');
        return [new ConfigItem($this->settingName, $value, $valid)];
    }
}
