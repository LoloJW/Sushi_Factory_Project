<?php

namespace App\Form;

use App\Entity\ReservationRoom;
use App\Entity\User;
use App\Enum\ReservationType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditReservationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', EnumType::class, [
                "class" => ReservationType::class,
                "label" => "Type de réservation"
            ])
            ->add('reservedFor',DateType::class, [
                'widget' => 'single_text',
                'label' => 'Jour'
            ])
            ->add('timeStart',TimeType::class, [
                'widget' => 'single_text',
                'label' => 'Heure de debut',
                'hours' => range(8, 22),
            ])
            ->add('timeEnd', TimeType::class, [
                'widget' => 'single_text',
                'label' => 'Heure de fin',
                'hours' => range(9, 23),
            ])
            ->add('name',TextType::class, [
                'label' => 'Nom de la réunion'
            ])
            ->add('userInvites', EntityType::class, [
                'class' => User::class,
                'choice_label' => fn(User $user) => $user->getFirstName() . ' ' . $user->getLastName(),
                'multiple' => true,
                'expanded' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ReservationRoom::class,
        ]);
    }
}
