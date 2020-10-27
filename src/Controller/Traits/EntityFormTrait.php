<?php

namespace App\Controller\Traits;

use Exception;
use Symfony\Component\Form\Form;

trait EntityFormTrait
{
    /**
     * @param mixed $entity
     */
    public function handleForm(Form $form, $entity): bool
    {
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                try {
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($entity);
                    $em->flush();

                    $this->addFlash('success', 'Registro salvo com sucesso');

                    return true;
                } catch (Exception $ex) {
                    $this->addFlash('error', 'Erro ao tentar salvar o registro: '.$ex->getMessage());
                }
            } else {
                $this->addFlash('error', 'O formulário possui valores inválidos, favor verificar antes de reenviar.');
            }
        }

        return false;
    }
}
