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

use Symfony\Component\HttpKernel\KernelInterface;

class FileLoader
{
    private KernelInterface $kernel;

    /**
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @param string $source
     * @return array
     */
    public function getFiles(string $source): array
    {
        return glob($source);
    }

    /**
     * @param string $file
     * @return array
     */
    public function readFile(string $file): array
    {
        return json_decode(file_get_contents($file), true);
    }

    /**
     * @param string|null $value
     * @return string|null
     */
    public function getSource(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }
        if ($value[0] === '@') {
            $parts = explode('/', $value);
            $bundle = substr($parts[0], 1);
            unset($parts[0]);
            return $this->kernel->getBundle($bundle)->getPath() . '/' . implode('/', $parts);
        }
        return $value;
    }
}
