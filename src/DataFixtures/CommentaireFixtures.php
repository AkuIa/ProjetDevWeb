<?php

namespace App\DataFixtures;

use App\Entity\Commentaire;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CommentaireFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @param ObjectManager $manager
     *
     * @return void
     */
    
    public function load(ObjectManager $manager) : void
    {
        $com1 = new Commentaire();
        $com1->setDescription("Trop bien.")
        ->setCrypto($manager->merge($this->getReference('crypto-btc')))
        ->setUser($manager->merge($this->getReference('user-admin')));

        $manager->persist($com1);
        $manager->flush();

    }

    /**
     * @return array
     */
    public function getDependencies(): array
    {
        return [
            CryptoFixtures::class,
            UserFixtures::class,
        ];
    }
}