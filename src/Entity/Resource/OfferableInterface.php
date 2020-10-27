<?php

namespace App\Entity\Resource;

use App\Entity\Money;

interface OfferableInterface extends ResourceInterface
{
    public function getOfferPrice(): Money;
}
