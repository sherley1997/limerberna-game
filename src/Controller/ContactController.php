<?php

namespace App\Controller;

use App\Form\ContactFormType;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(ContactFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $form = $form->getData();
            $email = $form['nom'];
            $email = $form['prenom'];
            $email = $form['email'];
            $objet=  $form['objet'];
            $contenu =  $form['message'];
            $message = (new TemplatedEmail())
                ->from( new Address($email))
                ->to(new Address('sherley-games@games.fr'))
                ->subject($objet)
                ->htmlTemplate('contact/contact_form.html.twig')  
                ->context([
                    'date' => new \DateTime('now'),
                    'objet' => $objet,
                    'contenu'=>$contenu,
                ]);
            $mailer->send($message);
            $this->addFlash('contact_add_success', 'Votre message a été envoyé avec succès !');
            return $this->redirectToRoute('app_contact');
        }
        return $this->render('contact/index.html.twig', [
            'contactForm' => $form->createView(),
        ]);
    }
}
