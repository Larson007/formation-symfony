<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    /**
     * @Route("/user/{slug}", name="user_show")                  //  On modifie la route avec le slug de l'Entity User
     */
    public function index(User $user)                       // On import la class User 
    {


        return $this->render('user/index.html.twig', [
            'user' => $user                                 // on place une variable 'user' qui recevra les valeurs de Entity User $user
        ]);
    }
}
