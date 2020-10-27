<?php

namespace App\Service\HousePriceIndex;

use App\Entity\Helper\PricePaidIndex;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;

final class DatabaseClient implements ClientInterface
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getAverageSoldPrice(string $location): ?PricePaidIndex
    {
        $ppi = $this->search($location);

        return $ppi;
    }

    public function getAverageRentPrice(string $location): ?PricePaidIndex
    {
        $ppi = $this->search($location);
        if ($ppi) {
            $ppi->setPrice(.0069 * $ppi->getPrice());
        }

        return $ppi;
    }

    private function search(string $location)
    {
        $rsm = new ResultSetMapping();
        $rsm
            ->addEntityResult(PricePaidIndex::class, 'p')
            ->addFieldResult('p', 'id', 'id')
            ->addFieldResult('p', 'postcode', 'postcode')
            ->addFieldResult('p', 'area', 'area')
            ->addFieldResult('p', 'region', 'region')
            ->addFieldResult('p', 'county', 'county')
            ->addFieldResult('p', 'address', 'address')
            ->addFieldResult('p', 'date', 'date')
            ->addFieldResult('p', 'town', 'town')
            ->addFieldResult('p', 'price', 'price')
        ;

        // While dont change the search rules, searching only for postcodes
        $where = "
            ppi.postcode LIKE CONCAT(:location, '%')
        ";

        $sql = "
            SELECT
                ppi.id,
                ppi.postcode,
                ppi.area,
                ppi.region,
                ppi.county,
                ppi.address,
                ppi.date,
                ppi.town,
                (
                    SELECT AVG(ppi.price) FROM price_paid_index ppi WHERE {$where}
                ) as price
            FROM
                price_paid_index ppi
            WHERE
                {$where}
            ORDER BY
                ppi.postcode
            LIMIT 1;
        ";

        $query = $this->em->createNativeQuery($sql, $rsm);
        $ppi = null;

        $location = str_replace(' ', '', $location);

        do {
            $query->setParameter('location', $location, \PDO::PARAM_STR);
            $ppi = $query->getOneOrNullResult();
            $location = substr($location, 0, strlen($location) - 1);
        } while (!$ppi && strlen($location) > 1);

        return $ppi;
    }
}
