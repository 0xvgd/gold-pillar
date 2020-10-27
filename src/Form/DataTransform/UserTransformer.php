<?php

namespace App\Form\DataTransform;

use App\Entity\Security\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\DataTransformerInterface;

class UserTransformer implements DataTransformerInterface
{
    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function reverseTransform($userId)
    {
        return $this->em->find(User::class, $userId);
    }

    public function transform($user)
    {
        return $user ? $user->getId() : '';
    }
}
