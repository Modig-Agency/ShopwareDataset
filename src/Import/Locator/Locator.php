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

namespace Modig\Dataset\Import\Locator;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;

class Locator implements LocatorInterface
{
    private EntityRepositoryInterface $repository;
    private array $loadedValues = [];
    private ?Context $context = null;

    /**
     * @param EntityRepositoryInterface $repository
     */
    public function __construct(EntityRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritDoc}
     */
    public function locate(array $data): ?Entity
    {
        $key = $this->getKey($data);
        if (!array_key_exists($key, $this->loadedValues)) {
            $this->loadedValues[$key] = null;
            if (isset($data['id'])) {
                $result = $this->repository->search(
                    new Criteria([$data['id']]),
                    $this->getContext()
                )->first();
                if ($result->getId()) {
                    $this->loadedValues[$key] = $result;
                    return $result;
                }
            }
            unset($data['id']);
            if (count($data) > 0) {
                $criteria = new Criteria();
                foreach ($data as $field => $value) {
                    $criteria->addFilter(new EqualsFilter($field, $value));
                }
                $result = $this->repository->search(
                    $criteria,
                    $this->getContext()
                )->first();
                if ($result) {
                    $this->loadedValues[$key] = $result;
                    return $result;
                }
            }
        }
        return $this->loadedValues[$key];
    }

    /**
     * @param array $data
     * @return string
     */
    private function getKey(array $data): string
    {
        asort($data);
        return hash('md5', json_encode($data));
    }

    /**
     * @return Context
     */
    private function getContext(): Context
    {
        if ($this->context === null) {
            $this->context = Context::createDefaultContext();
        }
        return $this->context;
    }
}
