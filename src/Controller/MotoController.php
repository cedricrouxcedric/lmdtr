<?php

namespace App\Controller;

use App\Entity\Images;
use App\Entity\Moto;
use App\Form\MotoType;
use App\Repository\MotoRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class MotoController extends AbstractController
{


    /**
     * @var MotoRepository
     */
    private $repository;

    /**
     * MotoController constructor.
     * @param MotoRepository $repository
     */

    public function __construct(MotoRepository $repository)
    {
        $this->MotoRepository = $repository;

    }


    /**
     * @Route("/moto", name="moto_index", methods={"GET"})
     */
    public function index(): Response
    {
        $motos = $this->MotoRepository->findAll();
        return $this->render('moto/index.html.twig', [
            'motos' => $motos,
        ]);
    }

    /**
     * @Route("/moto/disponible", name="moto_disponible", methods={"GET"})
     */
    public function stillOnSale(): Response
    {
        $motos = $this->MotoRepository->findAllStillOnSale();
        return $this->render('moto/index.html.twig', [
            'motos' => $motos,
        ]);
    }

    /**
     * @Route("/moto/vendue", name="moto_vendue", methods={"GET"})
     */
    public function alreadySale(): Response
    {
        $motos = $this->MotoRepository->findAllAlreadySale();
        return $this->render('moto/index.html.twig', [
            'motos' => $motos,
        ]);
    }
 //TODO utiliser le em du constructeur
    /**
     * @Route("/moto/new", name="moto_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $moto = new Moto();
        $form = $this->createForm(MotoType::class, $moto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Récuperation des images transmises
            $images = $form->get('images')->getData();
            // On boucle sur les images
            foreach ($images as $image) {
                // Generation d'un new nom de fichier
                $fichier = md5(uniqid()).'.'.$image->guessExtension();
                // Copie du fichier dans le dossier Uploads
                $image->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );
                // Stockage de l'image dans la base de données (son nom)
                $img = new Images();
                $img->setName($fichier);
                $moto->addImage($img);
            }

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
     * @Route("/moto/{id}", name="moto_show", methods={"GET"})
     */
    public function show(Moto $moto): Response
    {
        return $this->render('moto/show.html.twig', [
            'moto' => $moto,
        ]);
    }

    /**
     * @Route("/moto/{id}/edit", name="moto_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Moto $moto): Response
    {
        $form = $this->createForm(MotoType::class, $moto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
                // Récuperation des images transmises
                $images = $form->get('images')->getData();
                // On boucle sur les images
                foreach ($images as $image) {
                    // Generation d'un new nom de fichier
                    $fichier = md5(uniqid()).'.'.$image->guessExtension();
                    // Copie du fichier dans le dossier Uploads
                    $image->move(
                        $this->getParameter('images_directory'),
                        $fichier
                    );
                    // Stockage de l'image dans la base de données (son nom)
                    $img = new Images();
                    $img->setName($fichier);
                    $moto->addImage($img);
                }
            $this->getDoctrine()->getManager()->flush();
        }

        return $this->render('moto/edit.html.twig', [
            'moto' => $moto,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/moto/{id}", name="moto_delete", methods={"DELETE"})
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

    /**
     * @Route("/supprime/image/{id}", name="moto_delete_image", methods={"DELETE"})
     */
    public function deleteImage(Images $image,
                                Request $request,
                                EntityManagerInterface $em) {
        $data = json_decode($request->getContent(), true);

        // Verification si le token est valide
        if($this->isCsrfTokenValid('delete'.$image->getId(), $data['_token'])){
            $nom = $image->getName();
            unlink($this->getParameter('images_directory').'/'.$nom);

            $em = $this->getDoctrine()->getManager();
            $em->remove($image);
            $em->flush();

            // Recuperation de la reponse en JSON
            return new JsonResponse(['success'=> true]);
        } else {
            return new JsonResponse(['error'=> 'Token Invalide'], 400);
        }
    }
}
