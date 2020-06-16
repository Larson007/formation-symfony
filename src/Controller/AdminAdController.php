<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Repository\AdRepository;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminAdController extends AbstractController
{
    /**
     * @Route("/admin/ads", name="admin_ads_index")         // Mise a jour du name de la Route
     */
    public function index(AdRepository $repo)               //  On a besoin du Repository des annonces
    {
        return $this->render('admin/ad/index.html.twig', [  // Mise a jour du chemin vers le template
            'ads' => $repo->findAll()                       // On créer une variable ads qui contiendra toutes les données des annonces via AdRepository findAll()
        ]);
    }

    /**
     * Edition des annonces Admin
     *
     * @Route("/admin/ads/{id}/edit", name="admin_ads_edit")
     * 
     * @param Ad $ad
     * @return Response
     */
    public function edit(Ad $ad, Request $request, ObjectManager $manager)
    {
        $form = $this->createForm(AdType::class, $ad,);     // On créee un formulaire avec createForm base sur le from AdType et on le relie a notre Entity Ad = $ad
    
        $form -> handleRequest($request);                   // Le request pour interoger la BDD

        if ($form->isSubmitted() && $form->isValid()) {     // Condition qui vérifier ques les chammps remplie repondes au champs de la BDD
            $manager->persist($ad);                         // Sauvegarder des modifs
            $manager->flush();                              // Envoi des modifs en BDD

            $this->addFlash(                                // Message flash si flush Ok
                'success',
                "L'annonce <strong>{$ad->getTitle()}</strong> a bien été enregistrer"
            );
        }

        return $this->render('admin/ad/edit.html.twig', [
            'ad'    => $ad,                                 // On lui passe les annonces en paramettre dans le template
            'form'  => $form->createView()
        ]);
    }

    /**
     * Permet a l'admin de supprimer une annonce
     * 
     * @Route("/admin/ads/{id}/delete", name="admin_ads_delete")
     *
     * @param Ad $ad
     * @param ObjectManager $manager
     * @return Response
     */
    public function delete(Ad $ad, ObjectManager $manager)
    {
        $manager->remove($ad);                              // On dde au manager de supprimer l'entry stocker dans $ad
        $manager->flush();

        $this->addFlash(
            'success',
            "L'annonce <strong>{$ad->getTitle()}</strong> a bien été supprimer"
        );

        return $this->redirectToRoute('admin_ads_index');   // La redirection apres delete
    }
}
