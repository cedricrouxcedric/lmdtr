<?php

namespace App\Controller;

use App\Entity\Articles;
use App\Entity\Commentaires;
use App\Entity\Moto;
use App\Entity\MotoLike;
use App\Entity\Themes;
use App\Form\ArticlesType;
use App\Repository\ArticlesRepository;
use App\Repository\MotoLikeRepository;
use DateTime;
use Doctrine\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/articles")
 */
class ArticlesController extends AbstractController
{
    /**
     * @Route("/", name="articles_index", methods={"GET"})
     */
    public function index(ArticlesRepository $articlesRepository): Response
    {
        return $this->render('articles/index.html.twig', [
            'articles' => $articlesRepository->findAll(),
        ]);
    }

    /**
     * @IsGranted("ROLE_SUBSCRIBER")
     * @Route("/new/{theme}", name="articles_new", methods={"GET","POST"})
     */
    public function new(Request $request,
                        Themes $theme,
                        UserInterface $user): Response
    {
        $article = new Articles();
        $form = $this->createForm(ArticlesType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setThemes($theme);
            $article->setCreatedAt(new DateTime());
            $article->setAuteur($user);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('articles_index');
        }

        return $this->render('articles/new.html.twig', [
            'article' => $article,
            'theme' => $theme,
            'form' => $form->createView(),

        ]);
    }

    /**
     * @Route("/{id}", name="articles_show", methods={"GET"})
     */
    public function show(Articles $article,
                         PaginatorInterface $paginator,
                         Request $request): Response
    {
        $commentsRepo = $this->getDoctrine()->getRepository(Commentaires::class);
        $commentsArticle = $commentsRepo->findBy(['articles' => $article], ['created_at' => 'ASC']);
        $comments = $paginator->paginate(
            $commentsArticle,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('articles/show.html.twig', [
            'article' => $article,
            'comments' => $comments,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="articles_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Articles $article): Response
    {
        $form = $this->createForm(ArticlesType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('articles_index');
        }

        return $this->render('articles/edit.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="articles_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Articles $article): Response
    {
        if ($this->isCsrfTokenValid('delete' . $article->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($article);
            $entityManager->flush();
        }

        return $this->redirectToRoute('articles_index');
    }

    /**
     * @Route("/{article}", name="articles_theme", methods={"GET"})
     * @ParamConverter("article", class="App\Entity\Articles")
     */
    public function articleByTheme($article)
    {
        return $this->render('articles/show.html.twig', [
            'article' => $article,
        ]);
    }

}
