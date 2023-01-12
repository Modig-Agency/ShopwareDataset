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

use Shopware\Core\Framework\DataAbstractionLayer\Entity;

class Category extends Locator implements LocatorInterface
{
    /**
     * {@inheritDoc}
     */
    public function locate(array $data): ?Entity
    {
        $searchData = [];
        if (isset($data['id'])) {
            $searchData['id'] = $data['id'];
        }
        if (isset($data['auto']) && $data['auto']) {
            $searchData['parentId'] = null;
        }
        return parent::locate($searchData);
    }
}
