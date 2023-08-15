<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Order;
use App\Entity\OrderDetail;
use App\Entity\User;
use App\Form\UserFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $user =$this->getUser();
        $orders= $entityManager->getRepository(Order::class)->findBy(['user'=>$user, 'isPaid'=>true]);
        $ordersDetails =[];
        foreach ($orders as $value) {
            $ordersDetails[] = $entityManager->getRepository(OrderDetail::class)->findBy(['relatedOrder'=>$value->getId()]);
        }
        $articles= $entityManager->getRepository(Article::class)->findAll();
        $utilisateurs= $entityManager->getRepository(User::class)->findAll();
        return $this->render('admin/index.html.twig', [
            'articles' => $articles,
            'utilisateurs'=>$utilisateurs,
            'user'=>$user,
            'orders'=>$orders,
            'details'=>$ordersDetails
        ]);
    }

    #[Route('admin/users/supprimer/{id}', name: 'app_user_supprimer', methods: ['GET', 'DELETE'])]
    public function supprimer($id, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->find(User::class, $id);
        $entityManager->remove($user);
        $entityManager->flush();
        $this->addFlash('notice', 'l\'utilisateur vient d\'être supprimée');
        return $this->redirectToRoute('app_admin');
    }

    #[Route('/admin/users/modifier/{id}', name: 'app_user_modifier', methods: ['GET', 'POST'])]
    public function modifier($id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->find(User::class, $id);
        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('app_admin');
        }
        return $this->render('user/modifier-user.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
