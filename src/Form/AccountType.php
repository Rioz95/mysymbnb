<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class AccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName')
            ->add('lastName')
            ->add('email')
            ->add('picture', HiddenType::class)
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
                        'maxSize' => '40048k',
                        'maxSizeMessage'  => "La taille maximum doit être égal à 2048k",
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png'
                        ]
                    ])
                ]
            ])
            ->add('introduction')
            ->add('description');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
