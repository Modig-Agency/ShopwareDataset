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

namespace Modig\Dataset\Console\Command;

use Modig\Dataset\Import\ConfigItem;
use Modig\Dataset\Import\ImportFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Import extends Command
{
    public const NAME = 'dataset:import';
    public const FILE_ARGUMENT = 'file';
    public const RUN_OPTION = 'run';
    public const RUN_SHORTCUT = 'r';
    public const CONFIG_VALID_MASK = '<info>Config file is valid. ' .
        'You can run the import with the same command and `--%s` (-%s) option</info>';
    public const CONFIG_NOT_VALID_MASK = '<error>Config file is not valid</error>';
    private ImportFactory $importFactory;

    /**
     * @param ImportFactory $importFactory
     */
    public function __construct(ImportFactory $importFactory)
    {
        $this->importFactory = $importFactory;
        parent::__construct(self::NAME);
    }

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        parent::configure();
        $this->addArgument(
            self::FILE_ARGUMENT,
            InputArgument::REQUIRED,
            'Location of the import config file'
        );
        $this->addOption(
            self::RUN_OPTION,
            self::RUN_SHORTCUT,
            null,
            'Run the import. Without this the command will only validate the config file'
        );
        $this->setDescription('Import categories, properties products and images');
    }

    /**
     * Run the command to validate config or import dataset
     *
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = $input->getArgument(self::FILE_ARGUMENT);
        $run = $input->getOption(self::RUN_OPTION);
        $import = $this->importFactory->create($file, !$output->isQuiet() ? $output : null);
        if ($run) {
            if ($import->isConfigValid()) {
                $import->import();
                return Command::SUCCESS;
            }
            $output->writeln($this->getConfigNotValidMessage());
            $this->getConfigTable($import->getConfigSettings(), $output)->render();
            return Command::FAILURE;
        }
        $this->getConfigTable($import->getConfigSettings(), $output)->render();
        $validConfig = $import->isConfigValid();
        $output->writeln(
            $validConfig
                ? $this->getConfigValidMessage()
                : $this->getConfigNotValidMessage()
        );
        return $validConfig ? Command::SUCCESS : Command::FAILURE;
    }

    /**
     * @return string
     */
    private function getConfigValidMessage(): string
    {
        return sprintf(self::CONFIG_VALID_MASK, self::RUN_OPTION, self::RUN_SHORTCUT);
    }

    /**
     * @return string
     */
    private function getConfigNotValidMessage(): string
    {
        return self::CONFIG_NOT_VALID_MASK;
    }

    /**
     * Build the configuration validation table
     *
     * @param array $data
     * @param OutputInterface $output
     * @return Table
     */
    private function getConfigTable(array $data, OutputInterface $output): Table
    {
        $table = new Table($output);
        $table->setHeaders(['Setting', 'Value', 'Is Valid']);
        foreach ($data as $key => $items) {
            $table->addRow([new TableCell('<info>' . $key . '</info>', ['colspan' => 3])]);
            foreach ($items as $item) {
                /** @var ConfigItem $item */
                $value = $item->getValue();
                if (!$item->isValid()) {
                    $value = '<error>' . $value . '</error>';
                }
                $table->addRow([
                    $item->getLabel(),
                    $value,
                    $item->isValid() ? 'Yes' : '<error>No</error>'
                ]);
            }
        }
        return $table;
    }
}
