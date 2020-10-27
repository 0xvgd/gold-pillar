<?php

namespace App\Service\HousePriceIndex;

use App\Entity\Helper\PricePaidIndex;

interface ClientInterface
{
    public function getAverageSoldPrice(string $location): ?PricePaidIndex;

    public function getAverageRentPrice(string $location): ?PricePaidIndex;
}
