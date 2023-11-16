<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Plateforme;

use App\Form\ArticleFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ArticleController extends AbstractController
{
    #[Route('/articles', name: 'app_articles', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $articles= $entityManager->getRepository(Article::class)->findAll();
        return $this->render('article/index.html.twig', [
            'articles' => $articles,
        ]);
    }

    #[Route('/articles/plateforme/{filter}', name: 'app_articles_plateforme', methods: ['GET'])]
    public function plateformeJeux($filter,EntityManagerInterface $entityManager): Response
    {
        $articles= $entityManager->getRepository(Article::class)->findAll();
        $articlesPlateforme =[];
        foreach ($articles as $article) {
            foreach ($article->getPlateforme() as  $value) {
                if ($value == $filter) {
                    $articlesPlateforme[] = $article;
                }
            }
        }
        return $this->render('article/jeux-par-plateforme.html.twig', [
            'articlesPlateforme' => $articlesPlateforme,
            'filter' => $filter
        ]);
    }

    /**
     * Création d'un nouveau article
     */
    #[Route('/article/admin/creer', name: 'app_article_creation', methods: ['GET', 'POST'])]
    public function creer(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $id_user = $this->getUser()->getUserIdentifier();
        $article = new Article;
        $articleForm = $this->createForm(ArticleFormType::class, $article);
        $articleForm->handleRequest($request);
        if ($articleForm->isSubmitted() && $articleForm->isValid()) {
            $articlePhoto = $articleForm->get('photo')->getData();
            if ($articlePhoto) {
                $originalFilename = pathinfo($articlePhoto->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $articlePhoto->guessExtension();
                try {
                    $articlePhoto->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads',
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $article->setPhoto('/uploads/' .$newFilename);
            }
            $entityManager->persist($article);
            $entityManager->flush();
            //code.....
            $this->addFlash("articles_add_success", "L'article à bien été enregistré");
            return $this->redirectToRoute('app_articles');
        }
        return $this->render('article/creer-article.html.twig', [
            'articleForm' => $articleForm->createView(),
            // 'plateformes'=> $plateformes
        ]);
    }

    /**
     * Modifier un article
     */
    #[Route('/article/admin/modifier/{id}', name: 'app_article_modifier', methods: ['GET', 'POST'])]
    public function modifier($id, Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $article = $entityManager->find(Article::class, $id);
        $articleForm = $this->createForm(ArticleFormType::class, $article);
        $articleForm->handleRequest($request);
        if ($articleForm->isSubmitted() && $articleForm->isValid()) {
            $articlePhoto = $articleForm->get('photo')->getData();
            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($articlePhoto) {
                $originalFilename = pathinfo($articlePhoto->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $articlePhoto->guessExtension();
                // Move the file to the directory where brochures are stored
                try {
                    $articlePhoto->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads',
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $article->setPhoto('/uploads/' .$newFilename);
            }
            $entityManager->persist($article);
            $entityManager->flush();  
            return $this->redirectToRoute('app_admin');
        }
        return $this->render('article/editer-article.html.twig', [
            'articleForm' => $articleForm->createView(),

        ]);
    }

    #[Route('/article/admin/supprimer/{id}', name: 'app_article_supprimer', methods: ['GET', 'DELETE'])]
    public function supprimer($id, EntityManagerInterface $entityManager): Response
    {
        $article = $entityManager->find(Article::class, $id);
        $entityManager->remove($article);
        $entityManager->flush();
        $this->addFlash('notice', 'Votre produit vient d\'être supprimée');
        return $this->redirectToRoute('app_admin');
    }

    #[Route(path: '/article/afficher/{id}', name: 'app_article_afficher', methods: ['GET'])]
    public function afficher($id, EntityManagerInterface $entityManager): Response
    {
        $article = $entityManager->getRepository(Article::class)->findBy(['id' => $id]);
        return $this->render('article/afficher-article.html.twig', [
            'article'=>$article,
            'id' => $id
        ]);
    }

    #[Route('/article/ajouter-au-panier/{id}', name: 'app_article_ajouter_panier', methods: ['GET'])]
    public function ajouterAuPanier($id, EntityManagerInterface $entityManager): Response
    {
        $article = $entityManager->getRepository(Article::class)->find($id);
        if (!$article) {
            throw $this->createNotFoundException('Article not found');
        }
        // Récupérez le panier actuel de l'utilisateur (à partir de la session ou de l'identifiant de l'utilisateur connecté)

        // Ajoutez l'article au panier
        // Exemple : $panier->addArticle($article);

        // Enregistrez les modifications (si nécessaire)

        // Redirigez l'utilisateur vers la page du panier
        return $this->redirectToRoute('app_afficher_panier');
    }
}