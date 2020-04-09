<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MotoDeTaRegionController extends AbstractController
{
    /**
     * @return Response
     * @Route("/lmdtr", name ="lmdtr_index")
     */
    public function index() :Response
    {
        return $this->render('lmdtr/index.html.twig', [
            'website' => 'LMDTR' ,
        ]);
    }
}
