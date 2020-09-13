<?php

namespace App\Controller;

use App\Entity\Images;
use App\Entity\Moto;
use App\Form\MotoType;
use App\Repository\MotoLikeRepository;
use App\Repository\MotoRepository;
use App\Entity\MotoLike;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use phpDocumentor\Reflection\Types\Null_;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class MotoController extends AbstractController
{


    /**
     * @var MotoRepository
     */
    private $repository;

    /**
     * @var MotoRepository
     */
    private $MotoRepository;

    /**
     * MotoController constructor.
     * @param MotoRepository $repository
     */

    public function __construct(MotoRepository $repository)
    {
        $this->MotoRepository = $repository;

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

    private function uploadImg($image, $moto = NULL)
    {
        if (!isset($moto)) {
            $moto = new Moto();
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
        $moto->addImage($img);
    }

    /**
     * @Route("/moto", name="moto_index", methods={"GET"})
     */
    public function index(Request $request,
                          PaginatorInterface $paginator): Response
    {
        $motosAll = $this->MotoRepository->findAll();
        $motos = $paginator->paginate(
            $motosAll,
            $request->query->getInt('page', 1),
            6
        );
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

    /**
     * @IsGranted("ROLE_SUBSCRIBER")
     * @Route("/moto/new", name="moto_new", methods={"GET","POST"})
     */
    public function new(Request $request,
                        UserInterface $user,
                        ContactController $contactController): Response
    {
        $moto = new Moto();
        $form = $this->createForm(MotoType::class, $moto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récuperation des images transmises
            $images = $form->get('images')->getData();
            if ($this->checkExt($images)) {
                // On boucle sur les images
                foreach ($images as $image) {
                    $this->uploadImg($image, $moto);
                }
                $moto->setVendeur($user);
                $moto->setConfirmationCode(md5(uniqid()));
                $moto->setValidate(false);
                $createdDate = new DateTime();
                $moto->setCreatedAt($createdDate);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($moto);
                $entityManager->flush();
                $contactController->sendNewMotoConfirmation($user,$moto);
                return $this->redirectToRoute('moto_index');
            }
            $this->addFlash('error', "Les photos ne respectent pas le format requis");
        }
        return $this->render('moto/new.html.twig', [
            'moto' => $moto,
            'form' => $form->createView(),
            'user' => $user,
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
     * @IsGranted ("ROLE_SUBSCRIBER")
     * @Route("/moto/{id}/edit", name="moto_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Moto $moto): Response
    {
        $form = $this->createForm(MotoType::class, $moto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récuperation des images transmises
            $images = $form->get('images')->getData();
            if ($this->checkExt($images)) {
                // On boucle sur les images
                foreach ($images as $image) {
                    $this->uploadImg($image, $moto);
                }
                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('success', 'Annonce modifiée');
                return $this->redirectToRoute('moto_show', array('id' => $moto->getId()));
            } else {
                $this->addFlash('error', "Les photos ne respectent pas le format requis");
            }
        }

        return $this->render('moto/edit.html.twig', [
            'moto' => $moto,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @IsGranted("ROLE_SUBSCRIBER")
     * @Route("/moto/{id}", name="moto_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Moto $moto): Response
    {
        if ($this->isCsrfTokenValid('delete' . $moto->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $likes = $moto->getLikes();
            $motoLikeRepo = $entityManager->getRepository(MotoLike::class);
            foreach ($likes as $like){
                $motoLike = $motoLikeRepo->findOneBy(['moto'=> $like->getMoto()]);
                $moto->removeLike($like);
                $entityManager->remove($motoLike);
            }
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

    /**
     *
     * @IsGranted("ROLE_SUBSCRIBER")
     * @Route("/moto/{id}/like", name="moto_like", options={"expose"=true}, methods={"GET","POST"})
     *
     * @param Moto $moto
     * @param EntityManagerInterface $manager
     * @param MotoLikeRepository $likeRepo
     * @return Response
     */
    public function like(Moto $moto,
                         EntityManagerInterface $manager,
                         MotoLikeRepository $likeRepo): Response
    {
        $user = $this->getUser();
        if ($user) {
            if (!($moto->isLikedByUser($user))) {
                $like = new MotoLike();
                $like->setMoto($moto);
                $like->setUser($user);

                $manager->persist($like);

                $message = "Moto ajoutée à vos favoris";
            } else {
                $like = $likeRepo->findOneBy([
                    'moto' => $moto,
                    'user' => $user
                ]);

                $manager->remove($like);
                $message = "Moto retirée de vos favoris";

            }
            $manager->flush();
            return $this->json([
                'code' => 200,
                'isliked' => $moto->isLikedByUser($user),
                'message' => $message,
                'likes' => $likeRepo->count(['moto' => $moto])
            ], 200);

        } else {
            return $this->json([
                'code'=> 403,
                'message'=> "Unauthorized"
            ],403);
        }
    }


}
