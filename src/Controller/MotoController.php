<?php

namespace App\Controller;

use App\Entity\Moto;
use App\Form\MotoType;
use App\Repository\MotoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/moto")
 */

class MotoController extends AbstractController
{


    /**
     * @var MotoRepository
     */
    private $repository;

    public function __construct(MotoRepository $repository)
    {
        $this->MotoRepository = $repository;
    }


    /**
     * @Route("/", name="moto_index", methods={"GET"})
     */
    public function index(): Response
    {
        $motos = $this->MotoRepository->findAll();
        return $this->render('moto/index.html.twig', [
            'motos' => $motos,
        ]);
    }

    /**
     * @Route("/disponible", name="moto_disponible", methods={"GET"})
     */
    public function stillOnSale(): Response
    {
        $motos = $this->MotoRepository->findAllStillOnSale();
        return $this->render('moto/index.html.twig', [
            'motos' => $motos,
        ]);
    }

    /**
     * @Route("/vendue", name="moto_vendue", methods={"GET"})
     */
    public function alreadySale(): Response
    {
        $motos = $this->MotoRepository->findAllAlreadySale();
        return $this->render('moto/index.html.twig', [
            'motos' => $motos,
        ]);
    }

    /**
     * @Route("/new", name="moto_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $moto = new Moto();
        $form = $this->createForm(MotoType::class, $moto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($moto);
            $entityManager->flush();

            return $this->redirectToRoute('moto_index');
        }

        return $this->render('moto/new.html.twig', [
            'moto' => $moto,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="moto_show", methods={"GET"})
     */
    public function show(Moto $moto): Response
    {
        return $this->render('moto/show.html.twig', [
            'moto' => $moto,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="moto_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Moto $moto): Response
    {
        $form = $this->createForm(MotoType::class, $moto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('moto_index');
        }

        return $this->render('moto/edit.html.twig', [
            'moto' => $moto,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="moto_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Moto $moto): Response
    {
        if ($this->isCsrfTokenValid('delete'.$moto->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($moto);
            $entityManager->flush();
        }

        return $this->redirectToRoute('moto_index');
    }
}
