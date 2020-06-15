<?php

namespace App\Controller;

use App\Form\AccountType;
use App\Entity\PasswordUpdate;
use App\Form\PasswordUpdateType;
use Symfony\Component\Form\FormError;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }


    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * permet d'editer un profile User
     * 
     * @Route("account/profile", name="app_profile")
     *
     * @IsGranted("ROLE_USER")
     * 
     * @return Response
     */
    public function profile(Request $request, ObjectManager $manager)
    {
        $user = $this->getUser();                               // Permet de recuprer l'utilisateur actuelement connecté

        $form = $this->createForm(AccountType::class, $user);   // Import du formualaire générer d'edition

        $form -> handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "Les modifications ont bien été enregistrés"
            );
        }

        return $this->render('security/profile.html.twig', [    // Chemin vers le render twig
            'form' => $form->createView()                       // On passe le formluaire au template Twig pour l'afficher
        ]);
    }

    /**
     * Permet de modifier le MDP
     * 
     * @Route("/account/password-update", name="app_password-update")
     * 
     * @IsGranted("ROLE_USER")
     *
     * @return Response
     */
    public function updatePassword(Request $request, UserPasswordEncoderInterface $encoder, ObjectManager $manager)
    {   //  Instancie la variable passwordUpdate a partir de la fausse Entity PasswordUpdate
        $passwordUpdate = new PasswordUpdate;

        // On importe l'utilisateur qui est actuellement connecté
        $user = $this->getUser();

        //  On dde a symfony de créée un formulaire a partir fichier FORM/PasswordUpdateType avec les donnée de l'instantiation de PasswordUpdate
        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);

        //  On instancie une Requête pour communiquer avec la BDD importer depuis Request et stocker dans $request 
        $form->handleRequest($request);

        // On vérifie que l'utilisateur a bien respecter les @Assert de la fausse Entity PasswordUpdate
        if($form->isSubmitted() && $form->isValid()){

            // Vérifier que l'ancien MDP soit le même que celui de l'utilisateur actuellement connecté via la fonction PHP  qui compare le MDP Non Hasher et Hasher : password_verify(NoHash, Hash)
            if(!password_verify($passwordUpdate->getOldPassword(), $user->getPassword())){ // le ! permet d'inverser la question (si le MDP no HASH N'EST PAS = MDP HASH)
                
                //  GESTION DES ERREURS :
                //  $form->get('#') permet de cibler un champs précis de notre formualaire
                //  addError permet de faire passer un msg personnaliser en cas d'erreur 
                $form->get('oldPassword')->addError(new FormError("Le mot de passe que vous avez tapper n'est pas votre mot de passe actuel"));

                // SI OK : MDP noHASH = MDP HASH
            }   else {
                $newPassword = $passwordUpdate->getNewPassword();

                //  On lui dit d'encoder le new MDP dans le MDP actuel
                $password = $encoder->encodePassword($user, $newPassword);

                // On envoi a l'Entity User le New MDP a la place de l'ancien 
                $user->setPassword($password);

                //  On sauvegarde les changements
                $manager->persist($user);

                //  On envoi les nvlle donnée ds la BDD
                $manager->flush();

                // On affiche un msg si tous a bien fonctionner
                $this->addFlash(
                    'success',
                    "Votre mot de passe a bien été modifié"
                );

                // On fini par rediriger vers la page d'accueil
                return $this->redirectToRoute('homepage');
            } 
        }

        return $this->render('security/password.html.twig', [
            'form' => $form->createView()                       // On oublie pas d'envoyer le formulaire stocker dans $form vers le template ! 
        ]);
    }

    /**
     * Affiche la page de profil de l'utilisateur connecté
     *
     * @Route("/account", name="account_index")
     *
     * @IsGranted("ROLE_USER")
     * 
     * @return Response
     */
    public function MyAccount()
    {
        return $this->render('user/index.html.twig', [
            'user' => $this->getUser()                      // on dde a récupérer l'utilisateur connecté
        ]);
    }
}
