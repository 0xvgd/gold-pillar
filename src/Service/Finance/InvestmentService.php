<?php

namespace App\Service\Finance;

use App\Entity\Finance\Investment;
use Doctrine\ORM\EntityManagerInterface;

/**
 * InvestmentService.
 *
 * @author Laerte Mercier Junior <laertejjunior@gmail.com>
 */
class InvestmentService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function generateReferenceCode()
    {
        $referenceCode = null;
        $codeExists = true;

        while ($codeExists) {
            $referenceCode = 'INV-'.substr(strtoupper(sha1(random_bytes(4))), 0, 8);
            $reserve = $this->em->getRepository(Investment::class)->findOneBy(
                [
                    'referenceCode' => $referenceCode,
                ]
            );

            if (!$reserve) {
                $codeExists = false;
            }
        }

        return $referenceCode;
    }
}
