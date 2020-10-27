<?php

namespace App\Service\Asset;

use App\Entity\Asset\AssetEquity;
use App\Entity\Resource\Asset;
use App\Entity\Resource\Resource;
use App\Enum\ProjectStatus;
use App\Service\ResourceService;
use Exception;

/**
 * AssetService.
 *
 * @author Laerte Mercier Junior <laertejjunior@gmail.com>
 */
class AssetService extends ResourceService
{
    /**
     * @param Asset $asset
     *
     * @throws Exception
     */
    public function save(Resource $asset)
    {
        if (!$asset instanceof Asset) {
            throw new Exception('The resource must be a instance of Asset');
        }

        if (!$asset->getId()) {
            $asset->setAssetStatus(ProjectStatus::TO_INVEST());
            $asset->getTotalInvested()->setAmount(0);
        }

        /** @var AssetEquity|null $lastEquity */
        $lastEquity = null;
        foreach ($asset->getAssetEquities() as $equity) {
            /* @var AssetEquity $equity */
            if (null === $lastEquity) {
                $lastEquity = $equity;
            } else {
                $last = ($lastEquity->getYearRef() * 100) + $lastEquity->getMonthRef();
                $curr = ($equity->getYearRef() * 100) + $equity->getMonthRef();
                if ($curr > $last) {
                    $lastEquity = $equity;
                }
            }
        }

        if ($lastEquity && $asset->getTotalInvested()->getAmount() > $lastEquity->getPrice()) {
            throw new Exception('Current equity cannot be less than total invested');
        }

        $asset->setLastEquity($lastEquity);

        parent::save($asset);
    }

    public function getTransactions(Asset $asset)
    {
        return [];
    }

    public function getCompanyTransactions(Asset $asset)
    {
        return [];
    }
}
