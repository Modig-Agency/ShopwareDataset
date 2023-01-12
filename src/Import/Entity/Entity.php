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

namespace Modig\Dataset\Import\Entity;

use Modig\Dataset\Exception\MissingConfigValueException;
use Modig\Dataset\Import\ConfigCollector\ConfigCollectorInterface;
use Modig\Dataset\Import\ConfigItem;
use Modig\Dataset\Import\DataProcessor\DataProcessorInterface;
use Modig\Dataset\Import\FileLoader;
use Modig\Dataset\Import\ImportInterface;
use Modig\Dataset\Import\OutputHandler;
use Modig\Dataset\IMport\Persistor\PersistorInterface;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Write\WriteContext;
use Symfony\Component\Console\Output\OutputInterface;

class Entity implements ImportInterface
{
    private FileLoader $fileLoader;
    private DataProcessorInterface $processor;
    private OutputHandler $outputHandler;
    private PersistorInterface $persistor;
    private EntityDefinition $definition;
    private ConfigCollectorInterface $collector;
    private string $title;

    /**
     * @param FileLoader $fileLoader
     * @param ConfigCollectorInterface $collector
     * @param DataProcessorInterface $processor
     * @param OutputHandler $outputHandler
     * @param PersistorInterface $persistor
     * @param EntityDefinition $definition
     * @param string $title
     */
    public function __construct(
        FileLoader $fileLoader,
        ConfigCollectorInterface $collector,
        DataProcessorInterface $processor,
        OutputHandler $outputHandler,
        PersistorInterface $persistor,
        EntityDefinition $definition,
        string $title
    ) {
        $this->fileLoader = $fileLoader;
        $this->collector = $collector;
        $this->processor = $processor;
        $this->outputHandler = $outputHandler;
        $this->persistor = $persistor;
        $this->definition = $definition;
        $this->title = $title;
    }

    /**
     * {@inheritDoc}
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * {@inheritDoc}
     */
    public function import(array $config, WriteContext $context, ?OutputInterface $output = null): void
    {
        $title = $this->getTitle();
        $source = $this->fileLoader->getSource($config['source'] ?? "");
        if (!$source) {
            throw new MissingConfigValueException("Data source", $title);
        }
        $files = $this->fileLoader->getFiles($source);
        $progressBar = $this->outputHandler->createProgressBar($output, count($files), $title);
        $progressBar && $progressBar->display();
        $total = 0;
        foreach ($files as $file) {
            $this->outputHandler->setProgressBarMessage($progressBar, "Importing {$title} file {$file}");
            $data = $this->processor->process($this->fileLoader->readFile($file), $config);
            $imported = $this->persistor->persist($this->definition, $data, $context);
            $total += $imported;
            $this->outputHandler->advanceProgressBar($progressBar);
        }
        $this->outputHandler->finishProgressBar($progressBar, $title . ' ' . $total);
    }

    /**
     * {@inheritDoc}
     */
    public function getConfigValues(array $config): array
    {
        return $this->collector->collect($config);
    }
}
