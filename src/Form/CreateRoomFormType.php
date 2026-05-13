<?php

namespace App\Form;

use App\Entity\Rooms;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateRoomFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('roomNumber', IntegerType::class)
            ->add('capacity', IntegerType::class)
            ->add('projector', ChoiceType::class, [
                'choices' => [
                    'Oui' => true,
                    'Non' => false,
                    ]
            ])
            ->add('whiteboard', ChoiceType::class, [
                'choices' => [
                    'Oui' => true,
                    'Non' => false,
                    ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Rooms::class,
        ]);
    }
}
