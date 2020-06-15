<?php
namespace App\DataFixtures;

use Faker\Factory;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;   // Import de UserPasswordEncoderInterface
use App\Entity\Ad;                                                          // Import de l'Entity Ad
use App\Entity\User;                                                        // Import de l'Entity User
use App\Entity\Image;                                                       // Import de l'Entity Image

class AppFixtures extends Fixture
{
    private $encoder;                                               // On créer une propriété $encoder en private

    public function __construct(UserPasswordEncoderInterface $encoder )
    {
        $this->encoder = $encoder;                                  // On stock la propriété private $encoder dans la fonction public $encoder pour l'utiliser dans tt les function du fichier Fixture AppFixtures
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr-FR');

        // Nous gérons les utilisateur
        $users = [];                                                // On déclare un tableau vide $users qui contiendra les x10 $user créer

        $genres = ['male', 'female'];                               // On déclare un tableau $genres pour 'Hommes' ou 'Femmes' en Anglais pour utiliser $faker

        for ($i=1; $i < 10; $i++) { 
            $user = new User();                                     // Ne pas oublier d'importer la class Entity User

            $genre = $faker->randomElement($genres);                // $faker va récuprer un élémént aléatoire du tableau $genres
            
            $picture = 'https://randomuser.me/api/portraits/';      // On utilise le site randomuser.me en prenant l'adresse qui contient les avatars ( https://randomuser.me/api/portraits/men/68.jpg )
            $pictureId = $faker->numberBetween(1, 99) . '.jpg' ;    // le site randomuser.me propose 99 avatar Hommes et Femmes

            // Sans condition ternaire :
//            if ($genre == "male") $picture = $picture . 'men/' . $pictureId;    // On mets une condition si le genre est 'male' on concatenant une Url vers l'avatar d'un Homme.
//            else $picture = $picture . 'women/' . $pictureId;                   // Idem mais pour une Femme.

            // Avec Condition ternaire :
            $picture .= ($genre == 'male' ? 'men/' : 'women/') . $pictureId;

            // la méthode encodePassword viens de la class UserPasswordEncoderInterface stocker dans 'encoder' et encode le MDP avec l'encodeur choisir dans security.yaml
            $password = $this->encoder->encodePassword($user, 'password');  // encodePassword prend 2 paramettres (l'Entity cible , le MDP à encoder ) et va retouner le 2ieme paramettre hasher dans $hash

            $user   -> setFirstName($faker->firstname($genre))      // $faker va donner un nom/prenom en fonction du genre
                    -> setLastname($faker->lastName($genre))
                    -> setEmail($faker->email)
                    -> setIntroduction($faker->sentence())
                    -> setDescription($faker->paragraph(3))
                    -> setPassword($password)                               // On définit le champs MDP via la variable $hash construite plus tôt 
                    -> setPicture($picture);                        // On appel notre condition $picture qui gére les avatar par genre via randomuser.me

            $manager->persist($user);                               // On dde a sauvegarder x10 nos $user générer via $faker
            $users [] = $user;                                      // On place les $user dans le tableau vide $users créer précédement
        }

        // Nous gérons les annonces
        for($i = 1; $i <= 30; $i++){
            $ad = new Ad();

            $title          = $faker->sentence();
            $coverImage     = $faker->imageUrl(1000, 350);
            $introduction   = $faker->paragraph(2);
            $content        = $faker->paragraph(5);

            $user = $users [mt_rand(0, count($users) -1)];           // On donne à nos annonces un auteur générer de 1 aux max-1 au cas ou on a + de 10 user par la suite du tableau $users

            $ad ->setTitle($title)
                ->setCoverImage($coverImage)
                ->setIntroduction($introduction)
                ->setContent($content)
                ->setPrice(mt_rand(40, 200))
                ->setRooms(mt_rand(1, 5))
                ->setAuthor($user);                                 // On previens l'annonce qu'elle appartient a un $user

                                                                    // On va boucler a nouveau pour les images
            for ($j=1; $j < mt_rand(2, 5); $j++) {                  // $j = 1 pour min 1 image | $j < mt_rand(2, 5) pour un max entre 2 et 5 image | $j++ on incrémente jusu'au max
                $image = new Image();

                $image  ->setUrl($faker->imageUrl())                // On utilise Faker pour générer des url d'images
                        ->setCaption($faker->sentence())            // Faker génere une légende (alt de la balise <img>)
                        ->setAd($ad);                               // Pour la lier l'image à l'annonce qui lui Ad qui lui est lié

                $manager->persist($image);                          // on persite l'image (la faire perdurer dans le tps dans la BDD)
            }
            $manager->persist($ad);
        }
        $manager->flush();
    }
}