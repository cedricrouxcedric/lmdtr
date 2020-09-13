<?php

namespace App\Controller;

use App\Entity\Images;
use App\Entity\Piecedetachee;
use App\Form\PiecedetacheeType;
use App\Repository\PiecedetacheeRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/piecedetachee")
 */
class PiecedetacheeController extends AbstractController
{

    /**
     * @var PiecedetacheeRepository
     */
    private $repository;

    /**
     * @var PiecedetacheeRepository
     */
    private $PiecedetacheeRepository;

    /**
     * PiecedetacheeController constructor.
     * @param PiecedetacheeRepository $repository
     */

    public function __construct(PiecedetacheeRepository $repository)
    {
        $this->PiecedetacheeRepository = $repository;

    }

    private function checkExt($repo)
    {
        $allowedExt = ["jpeg", "jpg", "png"];
        $result = true;
        foreach ($repo as $file) {
            if (!in_array($file->guessExtension(), $allowedExt)) {
                $result = false;
            }
        }
        return $result;
    }

    private function uploadImg($image, $piece = null)
    {
        if (!isset($piece)) {
            $piece = new Piecedetachee();
        }
        $fichier = md5(uniqid()) . '.' . $image->guessExtension();
        // Copie du fichier dans le dossier Uploads
        $image->move(
            $this->getParameter('images_directory'),
            $fichier
        );
        // Stockage de l'image dans la base de données (son nom)
        $img = new Images();
        $img->setName($fichier);
        $piece->addImage($img);
    }

    /**
     * @Route("/", name="piecedetachee_index", methods={"GET"})
     */
    public function index(PiecedetacheeRepository $piecedetacheeRepository,
                          PaginatorInterface $paginator,
                          Request $request): Response
    {
        $pieceAllValidate = $this->PiecedetacheeRepository->findBy(array('validate'=>1), array('created_at' => 'DESC'));
        $piecedetachees = $paginator->paginate(
            $pieceAllValidate,
            $request->query->getInt('page', 1),
            6
        );
        return $this->render('piecedetachee/index.html.twig', [
            'piecedetachees' => $piecedetachees,
        ]);
    }

    /**
     * @Route("/new", name="piecedetachee_new", methods={"GET","POST"})
     */
    public function new(Request $request,
                        UserInterface $user,
                        ContactController $contactController): Response
    {
        $piecedetachee = new Piecedetachee();
        $form = $this->createForm(PiecedetacheeType::class, $piecedetachee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Récuperation des images transmises
            $images = $form->get('images')->getData();
            if ($this->checkExt($images)) {
                // On boucle sur les images
                foreach ($images as $image) {
                    $this->uploadImg($image, $piecedetachee);
                }
                $createDate = new DateTime();
                $piecedetachee->setVendeur($user);
                $piecedetachee->setConfirmationCode(md5(uniqid()));
                $piecedetachee->setValidate(false);
                $piecedetachee->setCreatedAt($createDate);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($piecedetachee);
                $entityManager->flush();
                $contactController->sendNewPiecedetacheeConfirmation($user, $piecedetachee);
                return $this->redirectToRoute('piecedetachee_index');
            }
            $this->addFlash('error', "Les photos ne respectent pas le format requis");
        }

        return $this->render('piecedetachee/new.html.twig', [
            'piecedetachee' => $piecedetachee,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="piecedetachee_show", methods={"GET"})
     */
    public function show(Piecedetachee $piecedetachee): Response
    {
        return $this->render('piecedetachee/show.html.twig', [
            'piecedetachee' => $piecedetachee,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="piecedetachee_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Piecedetachee $piecedetachee): Response
    {
        $form = $this->createForm(PiecedetacheeType::class, $piecedetachee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récuperation des images transmises
            $images = $form->get('images')->getData();
            if ($this->checkExt($images)) {
                // On boucle sur les images
                foreach ($images as $image) {
                    $this->uploadImg($image, $piecedetachee);
                }
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($piecedetachee);
                $entityManager->flush();
                $this->addFlash('success', 'Annonce modifiée');
                return $this->redirectToRoute('piecedetachee_show', array('id' => $piecedetachee->getId()));
            } else {
                $this->addFlash('error', "Les photos ne respectent pas le format requis");
            }}

        return $this->render('piecedetachee/edit.html.twig', [
            'piecedetachee' => $piecedetachee,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="piecedetachee_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Piecedetachee $piecedetachee): Response
    {
        if ($this->isCsrfTokenValid('delete' . $piecedetachee->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($piecedetachee);
            $entityManager->flush();
        }

        return $this->redirectToRoute('piecedetachee_index');
    }

    /**
     * @Route("/supprime/imagepiece/{id}", name="piecedetachee_delete_image", methods={"DELETE"})
     */
    public function deleteImage(Images $image,
                                Request $request,
                                EntityManagerInterface $em)
    {
        $data = json_decode($request->getContent(), true);
        // Verification si le token est valide
        if ($this->isCsrfTokenValid('delete' . $image->getId(), $data['_token'])) {
            $nom = $image->getName();
            unlink($this->getParameter('images_directory') . '/' . $nom);
            $em = $this->getDoctrine()->getManager();
            $em->remove($image);
            $em->flush();
            // Recuperation de la reponse en JSON
            return new JsonResponse(['success' => true]);
        } else {
            return new JsonResponse(['error' => 'Token Invalide'], 400);
        }
    }
}
