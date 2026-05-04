<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ChangePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('oldPassword', PasswordType::class, [
                'label' => 'Ancien mot de passe',
                'mapped' => false,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => "L'ancien mot de passe est obligatoire",
                    ]),
                ],
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'first_options' => [
                    'label' => 'password',
                ],
                'second_options' => [
                    'label' => 'confirmPassword',
                ],
                'invalid_message' => 'Les mots de passes doivent correspondre',
                'constraints' => [
                    new Assert\Length([
                        'min' => 12,
                        'minMessage' => 'Le mot de passe doit avoir au moins 12 caractères',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/[[:upper:]]/',
                        'message' => 'Le mot de passe doit avoir au moins une majuscule',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/[[:lower:]]/',
                        'message' => 'Le mot de passe doit avoir au moins une minuscule',
                    ]),
                    new Assert\Regex([
                        'pattern' => "/\d/",
                        'message' => 'Le mot de passe doit avoir au moins un chiffre',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/[[:^alnum:]]/',
                        'message' => 'Le mot de passe doit avoir au moins un caractère special',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
