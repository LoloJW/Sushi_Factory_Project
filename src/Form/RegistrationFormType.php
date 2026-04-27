<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email',EmailType::class)
            ->add('password', RepeatedType::class,[
                "type" => PasswordType::class,
                "first_options" => [
                    "label" => "password"
                ],
                "second_options" => [
                    "label" => "confirmPassword",
                ],
                "invalid_message" => "Les mots de passes doivent correspondre",
                "mapped" => false, // A pour but d'éviter d'associer directement le mot de passe au user, le temps de hasher le mot de passe
                "constraints" => [ 
                    new Assert\Length([
                        "min" => 12,
                        "minMessage" => "Le mot de passe doit avoir au moins 12 caractères"
                    ]),
                    new Assert\Regex([
                        "pattern" => "/[[:upper:]]/",
                        "message" => "Le mot de passe doit avoir au moins une majuscule"
                    ]),
                    new Assert\Regex([
                        "pattern" => "/[[:lower:]]/",
                        "message" => "Le mot de passe doit avoir au moins une minuscule"
                    ]),
                    new Assert\Regex([
                        "pattern" => "/\d/",
                        "message" => "Le mot de passe doit avoir au moins un chiffre"
                    ]),
                    new Assert\Regex([
                        "pattern" => "/[[:^alnum:]]/",
                        "message" => "Le mot de passe doit avoir au moins un caractère special"
                    ])
                ]
            ])
            ->add('firstName', TextType::class)
            ->add('lastName', TextType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
