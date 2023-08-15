<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Order;
use App\Entity\OrderDetail;
use App\Repository\ArticleRepository;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Stripe\Stripe;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class OrderController extends AbstractController
{
    public function __construct(private UrlGeneratorInterface $generatorUrl)
    {
      
      $this->generatorUrl = $generatorUrl;
  
    }
    #[Route('user/order', name: 'app_order')]
    public function index(EntityManagerInterface $em, SessionInterface $session,ArticleRepository $articleRepo,OrderRepository $orderRepo): Response
    {
        
        if(!$this->getUser()){
            $this->addFlash('notice', 'Merci de vous connecter pour pouvoir garder votre panier et procéder au paiement de celle-ci!');
            return $this->redirectToRoute('app_login');
        }


        $cart = $session->get('cart',[]);
      
        $cartWithData = [];
        foreach ($cart as $id => $quantite) {
            $cartWithData[] = [
                'article' => $articleRepo->find($id),
                'platform'=> substr($id, strpos($id, "_") + 1),
                'quantite' => $quantite,
                'id'=>$id
            ];
        }
        $user =$this->getUser();
        $order= new Order();
        $order->setNumero(uniqid());
        $order->setCreatedAt(new \DateTimeImmutable());
        $order->setIsPaid(0);
        $order->setUser($this->getUser());
        $em->persist($order);

        foreach ($cartWithData as $data) {
            $orderDetail = new OrderDetail();
            $orderDetail->setRelatedOrder($order);
            $orderDetail->setImage($data['article']->getPhoto());
            $orderDetail->setJeux($data['article']->getTitre());
            $orderDetail->setPlateform($data['platform']);
            $orderDetail->setPrix($data['article']->getPrix());
            $orderDetail->setQuantite($data['quantite']);
            $orderDetail->setIdArticlePlatform($data['id']);
            $em->persist($orderDetail);

        }
        $em->flush();
        return $this->render('order/index.html.twig', [
            'items'=>$cartWithData,
            'user'=>$user,
            'ref'=>$order->getNumero()
        ]);
    }

    #[Route('user/order/{ref}', name: 'app_stripe')]
    public function paiementStripe($ref,EntityManagerInterface $em, SessionInterface $session):  RedirectResponse
    {
        $articleStripe = [];
        $order = $em->getRepository(Order::class)->findOneBy(['numero' => $ref]);
    
        if (!$order) {
            return $this->redirectToRoute('app_cart');
          }
          foreach ($order->getDetailsOrder()->getValues() as $article) {
            $articleData = $em->getRepository(Article::class)->findOneBy(['titre' => $article->getJeux()]);
            // dd($articleData);
            $articleStripe[] = [
              'price_data' => [
                'currency' => 'eur',
                'unit_amount' => $article->getPrix()*100,
                'product_data' => [
                  'name' => $article->getJeux()
      
                ],
              ],
                'quantity' => $article->getQuantite(),
            ];
          }
          Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
          $checkout_session = \Stripe\Checkout\Session::create([
            'customer_email' =>$this->getUser()->getEmail(),
            'payment_method_types'=>['card'],
            'line_items' => [
              [
            
                $articleStripe
              ]
            ],
            'mode' => 'payment',
            'success_url' => $this->generatorUrl->generate('payment_success', ['reference' =>$order->getNumero()], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' =>  $this->generatorUrl->generate('payment_error',['reference' => $order->getNumero()], UrlGeneratorInterface::ABSOLUTE_URL),
          ]);
      
          $order->setStripSessionId( $checkout_session->id);
          $em->flush();
          return new RedirectResponse($checkout_session->url );
        }
    
      
        #[Route('user/order/success/{reference}', name: 'payment_success')]
        public function stripeSuccess($reference, SessionInterface $session, EntityManagerInterface $em): Response
        {
          $session->set('cart',[]);
          $order = $em->getRepository(Order::class)->findOneBy(['numero' => $reference]);
          $order->setIsPaid(true);
          $em->persist($order);
          $em->flush();
          return $this->render('order/success.html.twig');
        }
      
        #[Route('user/order/echec/{reference}', name: 'payment_error')]
        public function stripeError($reference): Response
        {
          return $this->render('order/error.html.twig');
        }
    }


