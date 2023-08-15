<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
    public function index(SessionInterface $session ,ArticleRepository $articleRepo, OrderRepository $orderRepo): Response
    {
        $cartWithData = [];
        
    //     if ($this->getUser()) {
    //     $order=$orderRepo->findOneBy(['user'=>$this->getUser(), 'isPaid'=>false ],['createdAt'=>'DESC']);
    //    foreach ($order->getDetailsOrder() as $value) {
    //     $cartWithData[]= [
    //         'article' =>$articleRepo->findOneBy(['titre'=>$value->getJeux()]),
    //         'platform'=> $value->getPlateform(),
    //         'quantite' =>$value->getQuantite(),
    //         'id'=>$value->getIdArticlePlatform()
    //     ];
    //    }
    // }
   
    //     // dd($cartWithData);
    // }
        # code...
   
        $cart = $session->get('cart',[]);
      
 
      foreach ($cart as $id => $quantite) {
        
            $cartWithData[] = [
                'article' => $articleRepo->find($id),
                'platform'=> substr($id, strpos($id, "_") + 1),
                'quantite' => $quantite,
                'id'=>$id
            ];
        }
    // dd($cartWithData);
        return $this->render('cart/index.html.twig', [
            'cart'=>$cartWithData
        ]);
    }

    #[Route('/cart/add/{id}', name: 'app_cart_add')]
    public function addToCart($id,SessionInterface $session , Request $request): Response
    {
      
        $selectedPlatforme = $request->request ;
 
        if(!((in_array("Nintendo",$selectedPlatforme->all(),true))||(in_array("PlayStation",$selectedPlatforme->all(),true))||(in_array("Xbox",$selectedPlatforme->all(),true))||(in_array("PC",$selectedPlatforme->all(),true)))) {
            $this->addFlash('notice', 'Veuillez cocher au moins une platefome !');
            return $this->redirectToRoute('app_article_afficher',['id' => $id]);
        }
        $ids =[];
        foreach ($selectedPlatforme->all() as $p) {
            $ids[] = $id.'_'.$p;
        }
   
        $cart = $session->get('cart',[]);
        foreach ($selectedPlatforme->all() as $p){
        if (!empty($cart[$id.'_'.$p])) {
                $cart[$id.'_'.$p]++;
            } else {
                $cart[$id.'_'.$p] = 1;
            }
        }
            $session->set('cart', $cart);

            return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/remove/{id}', name: 'app_cart_remove')]
    public function removeFromCart($id, SessionInterface $session)

    {
        $cart = $session->get('cart',[]);
      
        if (!empty($cart[$id])) {
            unset($cart[$id]);
        }
        $session->set('cart', $cart);

        return $this->redirectToRoute('app_cart');
}
      
}
