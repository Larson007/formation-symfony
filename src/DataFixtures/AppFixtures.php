<?php
namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
use App\Entity\Image;                                                  // La class Image importer fais bien référence à l'Entity Image
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr-FR');

        for($i = 1; $i <= 30; $i++){
            $ad = new Ad();

            $title          = $faker->sentence();
            $coverImage     = $faker->imageUrl(1000, 350);
            $introduction   = $faker->paragraph(2);
            $content        = $faker->paragraph(5);

            $ad ->setTitle($title)
                ->setCoverImage($coverImage)
                ->setIntroduction($introduction)
                ->setContent($content)
                ->setPrice(mt_rand(40, 200))
                ->setRooms(mt_rand(1, 5));

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