<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

class ErrorController extends AbstractController
{
    /**
     * @Route("/error", name="error")
     */
    public function show(
        Request $request,
        Throwable $exception,
        DebugLoggerInterface $logger
    ) {
        $statusCode = $exception->getStatusCode();

        switch ($statusCode) {
            case 403:
                if ($exception->getPrevious()) {
                    $role = $exception->getPrevious()->getAttributes()[0];
                    dd($role);
                }
                break;
            case 404:
                // code...
                break;
            default:
                // code...
                break;
        }

        dd($exception);

        return $this->render('error/index.html.twig', [
            'controller_name' => 'ErrorController',
        ]);
    }
}
