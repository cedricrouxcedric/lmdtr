<?php

namespace App\Controller;

use App\Entity\Articles;
use App\Entity\Commentaires;
use App\Entity\Themes;
use App\Repository\CommentairesRepository;
use App\Form\ThemesType;
use App\Repository\ThemesRepository;
use App\Repository\ArticlesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/themes")
 */
class ThemesController extends AbstractController
{
    /**
     * @param ThemesRepository $themesRepository
     * @return Response
     * @Route("/", name="themes_index", methods={"GET"})
     */
    public function index(ThemesRepository $themesRepository): Response
    {
        return $this->render('themes/index.html.twig', [
            'themes' => $themesRepository->findAll(),
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     * @IsGranted("ROLE_ADMIN")
     * @Route("/new", name="themes_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $theme = new Themes();
        $form = $this->createForm(ThemesType::class, $theme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($theme);
            $entityManager->flush();

            return $this->redirectToRoute('themes_index');
        }

        return $this->render('themes/new.html.twig', [
            'theme' => $theme,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Themes $theme
     * @return Response
     * @Route("/{name}", name="themes_show", methods={"GET"})
     */
    public function show(Themes $theme): Response

    {
        $articlesRepo = $this->getDoctrine()->getRepository(Articles::class);
        $commentRepo = $this->getDoctrine()->getRepository(Commentaires::class);
        $articlesTheme = $articlesRepo->findBy(array('themes' => $theme),array('created_at' => 'DESC'));
//        foreach ( $articlesTheme as $article)
//        {
//            $lastComment = $commentRepo->findOneBy(['articles'=> $article],array('created_at' => 'DESC'));
//            $article => 'lastcomment' = $lastComment;
//            dump($article);die();
//
//
//        }

        return $this->render('themes/show.html.twig', [
            'theme' => $theme,
            'articlesTheme' => $articlesTheme,
        ]);
    }

    /**
     * @param Request $request
     * @param Themes $theme
     * @return Response
     * @IsGranted("ROLE_ADMIN")
     * @Route("/{id}/edit", name="themes_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Themes $theme): Response
    {
        $form = $this->createForm(ThemesType::class, $theme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('themes_index');
        }

        return $this->render('themes/edit.html.twig', [
            'theme' => $theme,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param Themes $theme
     * @return Response
     * @IsGranted("ROLE_ADMIN")
     * @Route("/{id}", name="themes_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Themes $theme): Response
    {
        if ($this->isCsrfTokenValid('delete'.$theme->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($theme);
            $entityManager->flush();
        }

        return $this->redirectToRoute('themes_index');
    }
}
