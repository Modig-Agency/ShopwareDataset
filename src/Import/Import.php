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

use InvalidArgumentException;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Write\WriteContext;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class Import
{
    private ?array $globalConfig = null;
    /**
     * @var ImportInterface[]
     */
    private array $entities;

    private ?OutputInterface $output;
    /**
     * @var WriteContext|null
     */
    private ?WriteContext $context = null;
    /**
     * @var ImportInterface[]|null
     */
    private ?array $toRun = null;
    private ?array $processedConfigs = null;
    private ?array $toSkip = null;
    private ?array $configSettings = null;
    private ?array $configCache = null;
    private string $configFile;

    /**
     * @param string $configFile
     * @param ImportInterface[] $entities
     * @param OutputInterface|null $output
     */
    public function __construct(string $configFile, array $entities, ?OutputInterface $output = null)
    {
        $this->configFile = $configFile;
        $this->entities = $entities;
        $this->output = $output;
    }

    /**
     * @return void
     */
    public function import(): void
    {
        $this->preProcess();
        if ($this->isConfigValid()) {
            foreach ($this->toRun as $key => $entity) {
                $entity->import($this->processedConfigs[$key], $this->getContext(), $this->output);
            }
        }
    }

    public function getConfigSettings(): array
    {
        if ($this->configSettings === null) {
            $this->preProcess();
            $this->configSettings = [];
            foreach ($this->toRun as $key => $entity) {
                $this->configSettings[$entity->getTitle()] = $entity->getConfigValues($this->processedConfigs[$key]);
            }
            foreach ($this->toSkip as $entity) {
                $this->configSettings['<info>' . $entity->getTitle() . ' will be skipped</info>'] = [];
            }
        }
        return $this->configSettings;
    }

    /**
     * check which imports should run and which are skipped
     */
    private function preProcess()
    {
        if ($this->toRun === null) {
            $this->toRun = [];
            $this->processedConfigs = [];
            $this->toSkip = [];
            foreach ($this->entities as $key => $entity) {
                $this->processedConfigs[$key] = $this->processConfig($key);
                if (!isset($this->processedConfigs[$key]['skip']) || !$this->processedConfigs[$key]['skip']) {
                    $this->toRun[$key] = $entity;
                    continue;
                }
                $this->toSkip[$key] = $entity;
            }
        }
    }

    /**
     * @return WriteContext
     */
    private function getContext(): WriteContext
    {
        if ($this->context === null) {
            $context = Context::createDefaultContext();
            $context->scope(Context::SYSTEM_SCOPE, function () {
            });
            $this->context = WriteContext::createFromContext($context);
        }
        return $this->context;
    }

    /**
     * @return bool
     */
    public function isConfigValid(): bool
    {
        $valid = true;
        foreach ($this->getConfigSettings() as $group) {
            $valid = array_reduce(
                $group,
                function ($valid, ConfigItem $item) {
                    return $valid && $item->isValid();
                },
                $valid
            );
        }
        return $valid;
    }

    /**
     * @return array
     */
    private function readConfig(): array
    {
        if ($this->configCache === null) {
            try {
                $this->configCache = Yaml::parseFile($this->configFile);
            } catch (\Exception $e) {
                throw new InvalidArgumentException(
                    "Config File {$this->configFile} could not be loaded: " . $e->getMessage()
                );
            }
        }
        return $this->configCache;
    }

    /**
     * @param $entity
     * @return array
     */
    private function processConfig($entity): array
    {
        $config = $this->readConfig();
        return array_merge($this->getGlobalConfig(), $config[$entity] ?? []);
    }

    /**
     * @param array $config
     * @return array
     */
    private function getGlobalConfig(): array
    {
        if ($this->globalConfig === null) {
            $config = $this->readConfig();
            $this->globalConfig = $config['global'] ?? [];
        }
        return $this->globalConfig;
    }
}
