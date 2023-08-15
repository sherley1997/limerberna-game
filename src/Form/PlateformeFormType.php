<?php

namespace App\Form;

use App\Entity\Plateforme;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlateformeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom',ChoiceType::class,[
                'label'=>'selectionnez une plateforme :',
            'choices' => [
                "PlayStation"=>"PlayStation",
                "Xbox"=>"Xbox",
                "Nintendo Switch"=>"Nintendo Switch",
                "PC"=>"PC",
            ],])
            // ->add('articles')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Plateforme::class,
        ]);
    }
}
