<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            /*@link https://symfony.com/doc/current/reference/forms/types.html */
            ->add('Nom', TextType::class,[
                'label' => 'Nom',
                'attr' => [
                    'placeholder' => 'Limery'
                ],
                'constraints' =>[
                    new NotBlank(['message' => 'Veuiller entrer votre Nom']),
                ],  
            ])
            ->add('Prenom',TextType::class, [
                'label' => 'Prenom',
                'attr' => [
                    'placeholder' => 'Sherley',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre Prenom']),
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adress mail',
                'attr' => [
                    'placeholder' => 'adresse@', 
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuiller entrer une adresse mail']),
                    new Email(['message' => 'Veuiller entrer une adresse mail valide']),
                ]
            ])
            ->add('Telephone',TextType::class, [
                'label' => 'Numéro de telephone',
                'attr' => [
                    'placeholder' => '0712310452',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuiller entre un numéro']),
                ]
            ])
            ->add('Conditions', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter nos conditions.',
                    ]),
                ],
            ])
            ->add('Mot_de_passe', PasswordType::class, [
                // au lieu d'être posé directement sur l'objet,
                // ceci est lu et encodé dans le contrôleur
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit comporter au moins {{ limit }} caractères',
                        // longueur maximale autorisée par Symfony pour des raisons de sécurité
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add("inscrire",SubmitType::class,[ 'label'=> 'S\'inscrire'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
