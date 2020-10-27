<?php

namespace App\Controller\Traits;

use Symfony\Component\Form\Form;

trait FormErrorsTrait
{
    private function getErrorMessages(Form $form)
    {
        $errors = [];

        foreach ($form->getErrors(true) as $error) {
            $data = [
                'propertyPath' => $error->getOrigin()->getName(),
                'message' => $error->getMessage(),
            ];
            $errors[] = $data;
        }

        return $errors;
    }
}
