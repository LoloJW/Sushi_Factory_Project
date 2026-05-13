<?php

namespace App\Form;

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
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'options' => [
                    'attr' => [
                        'autocomplete' => 'new-password',
                    ],
                ],
                'first_options' => [
                    'constraints' => [
                        // new Assert\NotBlank(
                        //     message: 'Please enter a password', Autre syntaxe possible
                        // ),
                        new Assert\NotBlank([
                            "message"=> 'Les champs doivent être remplies',
                        ]),
                        new Assert\Length([
                            'min' => 12,
                            'max' => 255,
                            'minMessage' => 'Le mot de passe doit avoir au moins {{ limit }} caractères',
                            'maxMessage' => 'Le mot de passe doit avoir au plus {{ limit }} caractères',
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
                    'label' => 'New password',
                ],
                'second_options' => [
                    'label' => 'Repeat Password',
                ],
                'invalid_message' => 'Les mots de passes doivent correspondre.',
                // Instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
