<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PasswordUpdateType extends AbstractType
{
    /**
     * Permet d'avoir un template de configuration de nos champs
     *
     * @param string $label
     * @param string $placeholder
     * @return array
     */
    private function getConfiguration($label, $placeholder){
        return [
            'label' => $label,
            'attr' => [
                'placeholder' => $placeholder
            ]
        ];
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder                                                //  Par défault le builder est vide car nous n'avons pas donnée d'Entity lors de la créattion du formulaire
            // Ajout manuellement de nos champs depuis la fausse Entity PasswordUpdate
            ->add('oldPassword', PasswordType::class, $this->getConfiguration("Ancien mot de passe", "Donnez votre mot de passe actuel"))
            ->add('newPassword', PasswordType::class, $this->getConfiguration("Nouveau mot de passe", "Tappez votre nouveau mot de passe"))
            ->add('confirmPassword', PasswordType::class, $this->getConfiguration("Confirmation du mot de passe", "Confirmer votre nouveau mot de passe"))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
