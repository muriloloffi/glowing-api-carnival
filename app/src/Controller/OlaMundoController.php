<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OlaMundoController 
{
    /**
     * @Route("/ola")
     */
    public function OlaMundoAction(Request $request): Response
    {
        $pathInfo = $request->getPathInfo();
        $parametro = $request->query->get('parametro');
        return new JsonResponse([
            'mensagem' => 'Olá mundo!', 
            'pathInfo' => $pathInfo,
            'parametro' => $parametro
            ]);
    }
}