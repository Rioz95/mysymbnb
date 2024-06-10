<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class RegistrationType extends ApplicationType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, $this->getConfiguration("Prenom", "Doe.."))
            ->add('lastName', TextType::class, $this->getConfiguration("Nom", "JHON.."))
            ->add('slug', TextType::class,  $this->getConfiguration('Slug', "jhon-doe"), [
                'required' => false
            ])
            ->add('email', EmailType::class, $this->getConfiguration("Email", "jhondoe@example.com"))
            ->add('picture', HiddenType::class, $this->getConfiguration("Photo de profil", "Votre photo de profil"))
            ->add('imageFile', FileType::class, [
                'mapped' => false,
                'label' => 'Ajouter une image qui donner vraiment envie',
                'attr' => [
                    'accept' => ".jpg, .png"
                ],
                'row_attr' => [
                    'class' => 'd-none',
                ],
                'constraints' => [
                    new Image([
                        'maxSize' => '2048k',
                        'maxSizeMessage'  => "La taille maximum doit être égal à 2048k",
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png'
                        ]
                    ])
                ]
            ])
            ->add('hash', PasswordType::class, $this->getConfiguration("Mot de passe", "Choisissez un bon mot de passe"))
            ->add('passwordConfirm', PasswordType::class, $this->getConfiguration("Confirmation de mot de passe", "Veuillez confimer votre mots de passe "))
            ->add('introduction', TextType::class, $this->getConfiguration("Introduction", "Présentez-vous en quelque mots.."))
            ->add('description', TextareaType::class, $this->getConfiguration("Description détaillée ", "C'est le moment de vous présentez en détails !"));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
