<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ArticlesRepository;
use App\Repository\CommentairesRepository;
use App\Repository\PiecedetacheeRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Form\UserType;
use App\Repository\MotoLikeRepository;
use App\Repository\MotoRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_SUBSCRIBER")
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

//    /**
//     * @Route("/new", name="user_new", methods={"GET","POST"})
//     */
//    public function new(Request $request): Response
//    {
//        $user = new User();
//        $form = $this->createForm(UserType::class, $user);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $entityManager = $this->getDoctrine()->getManager();
//            $entityManager->persist($user);
//            $entityManager->flush();
//
//            return $this->redirectToRoute('user_index');
//        }
//
//        return $this->render('user/new.html.twig', [
//            'user' => $user,
//            'form' => $form->createView(),
//        ]);
//    }

    /**
     * @IsGranted("ROLE_SUBSCRIBER")
     * @Route("/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user,
                         MotoRepository $motoRepository,
                         ArticlesRepository $articlesRepository,
                         CommentairesRepository $commentairesRepository,
                         PiecedetacheeRepository $piecedetacheeRepository): Response
    {
        $favorites = $motoRepository->getFavoritesByUser($user);
        $motoOnSale = $motoRepository->getForSaleByUser($user);
        $articleCreated = $articlesRepository->createdByOrderByDate($user);
        $commentaires = $commentairesRepository->findComAndDataByUser($user);
        $pieces = $piecedetacheeRepository->getForSaleByUser($user);
        dump($pieces);
        return $this->render('user/show.html.twig', [
            'user' => $user,
            'favorites' => $favorites,
            'forSale' => $motoOnSale,
            'articles' => $articleCreated,
            'commentaires' => $commentaires,
            'pieces'=> $pieces,
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request,
                         User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @IsGranted("ROLE_SUPERADMIN")
     * @Route("/{id}/delete", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request,
                           User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index');
    }
}
