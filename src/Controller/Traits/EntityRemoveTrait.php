<?php

namespace App\Controller\Traits;

use Doctrine\DBAL\Exception\ConstraintViolationException;
use Exception;

trait EntityRemoveTrait
{
    /**
     * @param mixed $entity
     */
    public function doRemove($entity): bool
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $em->remove($entity);
            $em->flush();

            $this->addFlash('success', 'The record has been successfully removed.');

            return true;
        } catch (ConstraintViolationException $e) {
            $this->addFlash('error', 'O registro não pode ser removido porque está sendo '
                    .'usado em outro cadastro.');
        } catch (Exception $e) {
            $this->addFlash('error', 'Erro ao tentar remover o registro. Favor contactar o responsável '
                    .'pelo o sistema.');
        }

        return false;
    }
}
