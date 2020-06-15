<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Repository\AdRepository;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdController extends AbstractController
{
    /**
     * @Route("/ads", name="ads_index")
     */
    public function index(AdRepository $repo)
    {
        $ads = $repo->findAll();

        return $this->render('ad/index.html.twig', [
            'ads' => $ads
        ]);
    }

    /**
     * permet de créer une annonce
     *
     * @Route("/ads/new", name="ads_create")
     * 
     * @IsGranted("ROLE_USER")
     * 
     * @return Response
     */
    public function create (Request $request, ObjectManager $manager)
    {
        $ad = new Ad();
        $form = $this->createForm(AdType::class, $ad);
        $form ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        
            // On passe sur chaque images
            foreach ($ad->getImages() as $image) {

                // On précise que l'image est lié à l'annonce
                $image  ->setAd($ad);

                // On dde au manager de faire persiste l'image 
                $manager->persist($image);
            }

            $ad->setAuthor($this->getUser()); // Ajoute la personne connecter comme autheur de l'annonce créée dont getUser() fait référence

            $manager ->persist($ad);
            $manager ->flush();

            $this->addFlash(                                                                // test de msg flash 'success' avec son 1er msg
                'success', 
                "l'annonce <strong>{$ad->getTitle()}<strong> a bien été enregistrée !"
            );
        
            return $this->redirectToRoute('ads_show', [
                'slug' => $ad->getSlug()
            ]);
        }                                              

        return $this->render('ad/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet d'afficher le formulaire d'édition
     *
     * @Route("/ads/{slug}/edit", name="ads_edit")
     * 
     * @Security("is_granted('ROLE_USER') and user === ad.getAuthor()", message="Cette annonce ne vous appartient pas, vous ne pouvez pas la modifier")
     * 
     * @return Response
     */
        public function edit(Ad $ad, Request $request, ObjectManager $manager)  // ParamController=(Ad $ad), Request et ObjectManager nécéssaire pour les variables $request et $manager du Form
        {
            $form = $this->createForm(AdType::class, $ad);                      // copier/coller du formulaire create Ad
            $form ->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                foreach ($ad->getImages() as $image) {
                    $image  ->setAd($ad);
                    $manager->persist($image);
                }
                $manager ->persist($ad);
                $manager ->flush();

                $this->addFlash(
                    'success', 
                    "les modifictation de l'annonce <strong>{$ad->getTitle()}<strong> ont bien été enregistrées !"
                );
            
                return $this->redirectToRoute('ads_show', [
                    'slug' => $ad->getSlug()
                ]);
            }   

            return $this->render('ad/edit.html.twig',[                          // On passe a Twig un tableau de variables []
                'form'  => $form->createView(),
                'ad'    => $ad                                                  // On passe a Twig la variable $ad qui contient les données de notre Entity
            ]);
        }

    /**
     * Permet d'afficher une seule annonce
     *
     * @Route("/ads/{slug}", name="ads_show")
     * 
     * @return Response
     */
    public function show(Ad $ad)                                                //  Import de l'Entity Ad stocker dans la variable $ad
    {
        return $this->render('ad/show.html.twig', [
            'ad' => $ad                                                         // on envoi les données de notre Entity Ad $ad vers le template pour pouvoir avoir accèes au donnée de celle-ci
        ]);
    }

    /**
     * Permet de supprimer une annonces
     *
     * @Route("/ads/{slug}/delete", name="ads_delete")
     * 
     * @Security("is_granted('ROLE_USER') and user == ad.getAuthor()", message="Vous n'avez pas le droit d'acceder à cette ressource")
     * 
     * @param Ad $ad                            // Elle va recevoir une Ad
     * @param ObjectManager $manager            // va sauvegarder la supression en BDD
     * @return Response
     */
    public function delete(Ad $ad, ObjectManager $manager)
    {   
        // On dde au manager de supprimer l'annonce de notre BDD
        $manager->remove($ad);
        // On confirme l'envoi de la Requete de suppression
        $manager -> flush();

        // Si ok message de confirmation
        $this->addFlash(
            'success',
            "L'annonce <strong>{$ad->getTitle()}</strong> a bien été supprimeée"
        );

        // On redirige vers la page d'index des annonces
        return $this->redirectToRoute("ads_index");
    }
}

