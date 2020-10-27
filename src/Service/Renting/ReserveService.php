<?php

namespace App\Service\Renting;

use App\Entity\Reserve\Reserve;
use Doctrine\ORM\EntityManagerInterface;

/**
 * ReserveService.
 *
 * @author Laerte Mercier Junior <laertejjunior@gmail.com>
 */
class ReserveService
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
            $referenceCode = 'RES-'.substr(strtoupper(sha1(random_bytes(4))), 0, 8);
            $reserve = $this->em->getRepository(Reserve::class)->findOneBy(
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
