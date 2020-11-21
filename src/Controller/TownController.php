<?php

namespace App\Controller;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Towns;

class TownController extends AbstractController
{
    /**
     * @Route("/town", name="town")
     */
    public function index()
    {
        return $this->render('town/index.html.twig', [
            'controller_name' => 'TownController',
        ]);
    }

    /**
     * @Route("/departement/towns", name="get_Towns", options={"expose"=true}, methods={"GET", "POST"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function getTownsByDepartement(Request $request,
                                          EntityManagerInterface $entityManager):Response
    {
        $id = $request->query->get('departement');
        $townRepo = $entityManager->getRepository(Towns::class);
        $towns = $townRepo->getTownsForm($id);

        $data = [];
            foreach ($towns as $town) {
                $data[] = [
                'name' => $town->getName(),
                'value' => $town->getId(),
                ];
            }
        return new JsonResponse($data);
    }
}
