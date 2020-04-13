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
            'website' => 'Les motards de ta région' ,
        ]);
    }

    /**
     * @Route("/lmdtr/test/{slug<^[a-z0-9-]+$>}", defaults={"slug" = null}, name="lmdtr_test_slug")
     * @return Response
     */
    public function testSlug(string $slug) :Response
    {
        if (!$slug) {
        throw $this
            ->createNotFoundException('No slug has been sent to find a program in program\'s table.');
        }


            return $this->render('lmdtr/testslug.html.twig', [
                'slug' => $slug,
        ]);
    }
}
