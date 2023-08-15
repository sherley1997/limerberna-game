<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderDetail;
use App\Form\UserFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {

        $user =$this->getUser();
        $orders= $entityManager->getRepository(Order::class)->findBy(['user'=>$user, 'isPaid'=>true]);
        $ordersDetails =[];
        foreach ($orders as $value) {
            $ordersDetails[] = $entityManager->getRepository(OrderDetail::class)->findBy(['relatedOrder'=>$value->getId()]);
        }
        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $entityManager->persist($user);
            $entityManager->flush(); 
            return $this->redirectToRoute('app_user');
        }
        return $this->render('user/index.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'orders'=>$orders,
            'details'=>$ordersDetails
        ]);
    }
}
