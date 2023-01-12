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

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @codeCoverageIgnore
 */
class OutputHandler
{
    /**
     * @param OutputInterface|null $output
     * @param int $max
     * @param string $message
     * @return ProgressBar|null
     */
    public function createProgressBar(?OutputInterface $output, int $max, string $message): ?ProgressBar
    {
        if (!$output) {
            return null;
        }
        $progressBar = new ProgressBar($output, $max);
        $progressBar->setMessage($message);
        $progressBar->setFormat(
            ' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s% %message% ' . "\n"
        );
        return $progressBar;
    }

    /**
     * @param ProgressBar|null $progressBar
     * @param string $message
     */
    public function setProgressBarMessage(?ProgressBar $progressBar, string $message)
    {
        if ($progressBar) {
            $progressBar->setMessage($message);
            $progressBar->display();
        }
    }

    /**
     * @param ProgressBar|null $progressBar
     * @param int $step
     */
    public function advanceProgressBar(?ProgressBar $progressBar, int $step = 1)
    {
        $progressBar && $progressBar->advance($step);
    }

    /**
     * @param ProgressBar|null $progressBar
     * @param string $message
     */
    public function finishProgressBar(?ProgressBar $progressBar, string $message)
    {
        if ($progressBar) {
            $progressBar->setMessage($message);
            $progressBar->finish();
        }
    }
}
