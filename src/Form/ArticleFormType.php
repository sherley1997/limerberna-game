<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre',TextType::class)
            ->add('description',TextareaType::class)
            ->add('createur',TextType::class)
            ->add('prix',NumberType::class, [              
                'scale'    => 2,
                'attr'     => array(
                    'min'  => 0,
                    'max'  => 9999.99,
                    'step' => 0.01,
                )
            ])
            ->add('photo',FileType::class, [
                'label' => 'Votre image',
                // unmapped means that this field is not associated to any entity property
                'mapped' => false,
                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,
            ])
            ->add('dateDeSortie',null,[
                'widget' => 'single_text',
            ])
            ->add('plateforme', CollectionType::class,[
                'label' => false,
                'entry_type' => PlateformeFormType::class,
                'entry_options' => ['label' => false ],
                'allow_add' => true,
                'by_reference' => false,
                'allow_delete' => true,
                
            ])
             ->add("submit", SubmitType::class, [
                'label' => 'Enregister',
                'attr' => [
                    'class'=>'btn btn-primary m-2'
                ]
            ]);    
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
