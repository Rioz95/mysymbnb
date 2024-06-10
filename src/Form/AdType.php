<?php

namespace App\Form;

use App\Entity\Ad;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class AdType extends ApplicationType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, $this->getConfiguration('Titre', 'Taper un super titre pour votre annonce'))
            ->add('slug', TextType::class,  $this->getConfiguration('Adresse web', "Taper l'adresse web (automatique)"), [
                'required' => false
            ])
            ->add('coverImage', HiddenType::class,  $this->getConfiguration("L'image principal", "Ajouter une image qui donner vraiment envie"))
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
            ->add('introduction', TextType::class,  $this->getConfiguration('Introduction', "Donner une description globale de l'annonce"))
            ->add('content', TextareaType::class,  $this->getConfiguration('Description dataillée', 'Taper une description qui donne vraiment envie de venir chez vous !'))
            ->add('rooms', IntegerType::class, $this->getConfiguration("Nombre de chambre", "Le nombre de chambre disponibles"))
            ->add('price', MoneyType::class,  $this->getConfiguration('Prix par nuit', 'Indiquer le prix que vous voulez pour une nuit'))
            ->add('images', CollectionType::class, [
                'entry_type' => ImageType::class,
                'allow_add' => true,
                'allow_delete' => true
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
        ]);
    }
}
