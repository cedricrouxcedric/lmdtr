<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\LocationType;
use App\Repository\ArticlesRepository;
use App\Repository\CommentairesRepository;
use App\Repository\PiecedetacheeRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Form\UserType;
use App\Repository\MotoLikeRepository;
use App\Repository\MotoRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
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

    /**
     * @Route("/new", name="user_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @IsGranted("ROLE_SUBSCRIBER")
     * @Route("/{id}", name="user_show", methods={"GET"})
     * @param User $user
     * @param MotoRepository $motoRepository
     * @param ArticlesRepository $articlesRepository
     * @param CommentairesRepository $commentairesRepository
     * @param PiecedetacheeRepository $piecedetacheeRepository
     * @return Response
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
        return $this->render('user/show.html.twig', [
            'user' => $user,
            'favorites' => $favorites,
            'forSale' => $motoOnSale,
            'articles' => $articleCreated,
            'commentaires' => $commentaires,
            'pieces' => $pieces,
        ]);
    }

    /**
     * @IsGranted("ROLE_SUBSCRIBER")
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function edit(Request $request,
                         User $user): Response
    {

        $form = $this->createForm(UserType::class, $user);

        if ($this->isGranted('ROLE_ADMIN')) {
            $form->add('roles', ChoiceType::class, [
                'label' => 'Role ',
                'expanded' => true,
                'multiple' => true,
                'choices' => array(
                    'Sac de sable' => 'ROLE_USER',
                    'Motard' => 'ROLE_SUBSCRIBER',
                    'Pilote' => 'ROLE_ADMIN',
                )
            ]);
        }
        if ($this->isGranted('ROLE_SUPERADMIN')) {
            $form->add('roles', ChoiceType::class, [
                'label' => 'Role ',
                'expanded' => true,
                'multiple' => true,
                'choices' => array(
                    'Sac de sable' => 'ROLE_USER',
                    'Motard' => 'ROLE_SUBSCRIBER',
                    'Pilote' => 'ROLE_ADMIN',
                    'Force de l\'ordre' => 'ROLE_SUPERADMIN',
                )
            ]);
        }

        $form->handleRequest($request);
        $formLocation = $this->createForm(LocationType::class, $user);
        $formLocation->handleRequest($request);
        if (($form->isSubmitted() && $form->isValid()) || ($formLocation->isSubmitted() && $formLocation->isValid())) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('user_index');
        }
        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'locationform' => $formLocation->createView(),
        ]);
        $this->addFlash('error', 'Vous ne pouvez pas faire ça ');
        return $this->redirectToRoute('user_index');
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/{id}/delete", name="user_delete",methods={"DELETE"})
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function delete(Request $request,
                           User $user): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }
        return $this->redirectToRoute('user_index');
    }
}
