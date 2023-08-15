<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ContactFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('Nom', TextType::class, [
            'label' => 'Nom',
            'attr' => ['class'=>'form-control m-2']
        ])
        ->add('Prenom', TextType::class, [
            'label' => 'Prénom',
            'attr' => ['class'=>'form-control m-2']
        ])
        ->add('email', EmailType::class, [
            'label' => 'Adresse mail',
            'attr' => ['class'=>'form-control m-2'],
            'constraints' => [
                new NotBlank(['message' => 'Veuiller entrer une adresse mail']),
                new Email(['message' => 'Veuiller entrer une adresse mail valide']),
            ]
        ])
        ->add('objet', TextType::class, [
            'label' => 'Objet',
            'attr' => ['class'=>'form-control m-2']
        ])
        ->add('message', TextareaType::class, [
            'label' => 'Message',
            'attr' => ['class'=>'form-control m-2']
        ])
        ->add("envoyer", SubmitType::class, [
            'label' => 'Envoyer',
            'attr' => ['class'=>'btn btn-danger m-2 mx-auto']
        ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
