<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AbstractController {

    /**
     * @Route("/hello/{prenom}/age/{age}", name="hello")
     * @Route("/hello", name="hello_base")
     * @Route("/hello/{prenom}", name="prenom")
     * 
     * Montrer la page qui dit bonjour
     * 
     * @return void
     */


    public function hello($prenom = "anonyme", $age = 0) {
        return $this->render(                                      // $this->render() permet d'interpreterun template Twig sous la forme d'une Response
            'hello.html.twig', 
            [
                'prenom' =>  $prenom,
                'age'    =>  $age
            ]
        );
    }


    /**
     * @Route("/", name="homepage")
     */
    public function home() {

        $prenoms = ["Mohamed" => 33, "Yannis" => 30, "Dominique" => 34];
        return $this->render(
            'home.html.twig',
            [
                'title' => "Bonjour à tous",
                'age' => 15,
                'tableau' => $prenoms
            ]
        );
    }
}
?>
