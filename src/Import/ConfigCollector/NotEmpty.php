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

class NotEmpty implements ConfigCollectorInterface
{
    private string $key;
    private string $settingName;
    private string $errorMessage;

    /**
     * @param string $key
     * @param string $settingName
     * @param string $errorMessage
     */
    public function __construct(string $key, string $settingName, string $errorMessage)
    {
        $this->key = $key;
        $this->settingName = $settingName;
        $this->errorMessage = $errorMessage;
    }

    /**
     * {@inheritDoc}
     */
    public function collect(array $config): array
    {
        $valid = !empty($config[$this->key]);
        $value = $valid
            ? $config[$this->key]
            : $this->errorMessage;
        return [new ConfigItem($this->settingName, (string)$value, $valid)];
    }
}
