<?php
namespace App\DataFixtures;

use Faker\Factory;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;   // Import de UserPasswordEncoderInterface
use App\Entity\Ad;                                                          // Import de l'Entity Ad
use App\Entity\User;                                                        // Import de l'Entity User
use App\Entity\Image;                                                       // Import de l'Entity Image
use App\Entity\Role;

class AppFixtures extends Fixture
{
    // On créer une propriété $encoder en private
    private $encoder;                                               

    public function __construct(UserPasswordEncoderInterface $encoder )
    {
         // On stock la propriété private $encoder dans la fonction public $encoder pour l'utiliser dans tt les function du fichier Fixture AppFixtures
        $this->encoder = $encoder;                                 
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr-FR');

        /**** Creation d'un Role Admin ****/
        // Import de l'Entity Role
        $adminRole = new Role();
        // On défnit une valeur dans le champs title de l'Entity Role
        $adminRole->setTitle('ROLE_ADMIN');
        // On sauvegarde les données
        $manager -> persist($adminRole);

        // Création d'un user avec le Role Admin
        $adminUser = new User();
        // Les différents info du futur Admin
        $adminUser  ->setFirstName('Mohamed')
                    ->setLastName('Ben Allal')
                    ->setEmail('mohamed@msn.com')
                    ->setPassword($this->encoder->encodePassword($adminUser, 'password'))   // Pour le MDP, on encode avec encodePassword qui attend 2 paramettres (L'entity cible, le MDP en BRUT)
                    ->setPicture('https://randomuser.me/api/portraits/lego/8.jpg')
                    ->setIntroduction($faker->sentence())
                    ->setDescription($faker->paragraph(3))
                    ->addUserRole($adminRole)   //Appel de la methode addUserRole qui contient le ROLE ADMIN stocker dans $adminRole
                ;
        // On sauvegarde les données
        $manager->persist($adminUser);
        


        /**** Gestion des utilisateurs ****/
        // On déclare un tableau vide $users qui contiendra les x10 $user créer
        $users = [];                                                
        // On déclare un tableau $genres pour 'Hommes' ou 'Femmes' en Anglais pour utiliser $faker
        $genres = ['male', 'female'];                               

        // Gener 10 utilisateurs
        for ($i=1; $i < 10; $i++) { 
            // importation de la class Entity User
            $user = new User();
            // $faker va récuprer un élémént aléatoire du tableau $genres
            $genre = $faker->randomElement($genres);                
            // On utilise le site randomuser.me en prenant l'adresse qui contient les avatars ( https://randomuser.me/api/portraits/men/68.jpg )
            $picture = 'https://randomuser.me/api/portraits/';
            // le site randomuser.me propose 99 avatar Hommes et Femmes    
            $pictureId = $faker->numberBetween(1, 99) . '.jpg' ;
            // Concatenation de l'url "randomuser" en fonctin du genre avec Condition ternaire
            $picture .= ($genre == 'male' ? 'men/' : 'women/') . $pictureId;

            // la méthode encodePassword viens de la class UserPasswordEncoderInterface stocker dans 'encoder' et encode le MDP avec l'encodeur choisir dans security.yaml
            // encodePassword prend 2 paramettres (l'Entity cible , le MDP à encoder ) et va retouner le 2ieme paramettre hasher dans $hash
            $password = $this->encoder->encodePassword($user, 'password');

            $user   -> setFirstName($faker->firstname($genre))  // $faker va donner un nom/prenom en fonction du genre
                    -> setLastname($faker->lastName($genre))
                    -> setEmail($faker->email)
                    -> setIntroduction($faker->sentence())
                    -> setDescription($faker->paragraph(3))
                    -> setPassword($password)   // On définit le champs MDP via la variable $hash construite plus tôt 
                    -> setPicture($picture);    // On appel notre condition $picture qui gére les avatar par genre via randomuser.me

            // On dde a sauvegarder x10 nos $user générer via $faker
            $manager->persist($user);
            // On place les $user dans le tableau vide $users créer précédement                               
            $users [] = $user;                                      
        }

        // Nous gérons les annonces
        for($i = 1; $i <= 30; $i++){
            $ad = new Ad();

            $title          = $faker->sentence();
            $coverImage     = $faker->imageUrl(1000, 350);
            $introduction   = $faker->paragraph(2);
            $content        = $faker->paragraph(5);

            // On donne à nos annonces un auteur générer de 1 aux max-1 au cas ou on a + de 10 user par la suite du tableau $users
            $user = $users [mt_rand(0, count($users) -1)];           

            $ad ->setTitle($title)
                ->setCoverImage($coverImage)
                ->setIntroduction($introduction)
                ->setContent($content)
                ->setPrice(mt_rand(40, 200))
                ->setRooms(mt_rand(1, 5))
                ->setAuthor($user); // On previens l'annonce qu'elle appartient a un $user

            // On va boucler a nouveau pour les images
            // $j = 1 pour min 1 image | $j < mt_rand(2, 5) pour un max entre 2 et 5 image | $j++ on incrémente jusu'au max
            for ($j=1; $j < mt_rand(2, 5); $j++) {                  
                $image = new Image();

                $image  ->setUrl($faker->imageUrl())    // On utilise Faker pour générer des url d'images
                        ->setCaption($faker->sentence())    // Faker génere une légende (alt de la balise <img>)
                        ->setAd($ad);   // Pour la lier l'image à l'annonce qui lui Ad qui lui est lié

                // on persite l'image (la faire perdurer dans le tps dans la BDD)
                $manager->persist($image);                          
            }
            $manager->persist($ad);
        }
        $manager->flush();
    }
}