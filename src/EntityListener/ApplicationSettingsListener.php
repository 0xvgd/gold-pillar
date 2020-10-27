<?php

namespace App\EntityListener;

use App\Entity\ApplicationSettings as Entity;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * ApplicationSettingsListener.
 *
 * @author Laerte Mercier <laertejjunior@gmail.com>
 */
class ApplicationSettingsListener extends LogListener
{
    private $tokenStorage;

    public function __construct(
        TokenStorageInterface $tokenStorage
    ) {
        parent::__construct($tokenStorage);

        $this->tokenStorage = $tokenStorage;
    }

    protected function getEntityClass(): string
    {
        return Entity::class;
    }

    protected function getFields(): array
    {
        return [
            'commissionRate',
            'monthlyFee',
        ];
    }

    protected function getUser()
    {
        $token = $this->tokenStorage->getToken();
        $user = null;
        if ($token) {
            $user = $token->getUser();
        }

        return $user;
    }
}
