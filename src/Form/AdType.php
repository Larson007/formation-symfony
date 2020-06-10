<?php

namespace App\Form;

use App\Entity\Ad;
use App\Form\ImageType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class AdType extends AbstractType
{
    /**
     * Permet d'avoir la conf de base d'un champ
     *
     * @param string $label
     * @param string $placeholder
     * @param array $options
     * @return array
     */
    private function getConfiguration($label, $placeholder, $options = []){     // ajout du param $options qui sera par defaukt un tableau vide
        return array_merge([                                                    // array_merge va fusionner les tableau qui contient label et attr avec letableau $options
            'label' => $label,
            'attr' => [
                'placeholder' => $placeholder
            ]
            ], $options);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'attr' => [
                    'placeholder' => 'Un superbe titre'
                ]
            ])
            ->add('slug', TextType::class, $this->getConfiguration("Adresse Web", "Tapper l'adresse web (automatique)", [   // ouverture du tableau $options
                'required' => false                                                                                         // ajout de l'option de notre choix, ici required
            ]))
            ->add('coverImage', UrlType::class, $this->getConfiguration("Url de l'image principale", "Donnez l'adresse d'une image de votre bien"))
            ->add('introduction', TextType::class, $this->getConfiguration("Introduction", "Donnez une déscription global de l'annonce") )
            ->add('content', TextareaType::class, $this->getConfiguration("Déscription détaillée", "Tapper une description qui donneenvie de venir chez vous !"))
            ->add('price', MoneyType::class, $this->getConfiguration("Prix par nuit", "Indiquer le prix que vous voulez pour une nuit"))
            ->add('rooms', IntegerType::class, $this->getConfiguration("Nombre de chambres", "Le nombre de chambres disponibles"))
            ->add('images', CollectionType::class,[             // On dde au CollectionType de répéter le formulaire ImageType
                'entry_type'    =>  ImageType::class,           // Type de champs/Formulaire que l'ont doit répéter
                'allow_add'     => true,                         // 'allow_add' permet de préciser si l'on peut ajouter de nouveau éléménts
                'allow_delete'  =>true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
        ]);
    }
}