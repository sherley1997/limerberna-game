<?php

namespace App\Controller;

use App\Form\FaqFormType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class FaqController extends AbstractController
{
    #[Route('/faq', name: 'app_faq')]
    public function index(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(FaqFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $form = $form->getData();
            $email = $form['email'];
            $objet=  $form['objet'];
            $contenu =  $form['message'];
            $message = (new TemplatedEmail())
                ->from( new Address($email))
                ->to(new Address('sherley-games@games.fr'))
                ->subject($objet)
                ->htmlTemplate('faq/faq_form.html.twig')  
                ->context([
                    'date' => new \DateTime('now'),
                    'objet' => $objet,
                    'contenu'=>$contenu,
                ]);
            $mailer->send($message);
            $this->addFlash('faq_add_success', 'Votre message vient d\'être envoyé');
            return $this->redirectToRoute('app_faq');
        }
        return $this->render('faq/index.html.twig', [
            'FaqForm' => $form,
        ]);
    }
}
