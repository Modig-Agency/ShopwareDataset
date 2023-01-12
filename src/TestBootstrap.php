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

use Shopware\Core\TestBootstrapper;

if (is_readable(__DIR__ . '/../../../vendor/shopware/platform/src/Core/TestBootstrapper.php')) {
    require __DIR__ . '/../../../vendor/shopware/platform/src/Core/TestBootstrapper.php';
} elseif (is_readable(__DIR__ . '/../../../../vendor/shopware/core/TestBootstrapper.php')) {
    require __DIR__ . '/../../../../vendor/shopware/core/TestBootstrapper.php';
} else {
    // vendored from platform, only use local TestBootstrapper if not already defined in platform
    require __DIR__ . '/TestBootstrapper.php';
}

return (new TestBootstrapper())
    ->setProjectDir($_SERVER['PROJECT_ROOT'] ?? dirname(__DIR__, 4))
    ->setLoadEnvFile(true)
    ->setForceInstallPlugins(true)
    ->addActivePlugins('ModigDataset')
    ->addCallingPlugin()
    ->bootstrap()
    ->setClassLoader(require dirname(__DIR__) . '/../../../vendor/autoload.php')
    ->getClassLoader();
