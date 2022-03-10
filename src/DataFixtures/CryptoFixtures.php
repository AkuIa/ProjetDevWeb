<?php
namespace App\DataFixtures;

use App\Entity\Crypto;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CryptoFixtures extends Fixture
{
    /**
     * @param ObjectManager $manager
     *
     * @return void
     */
    public function load(ObjectManager $manager) : void
    {
        $crypto1 = new Crypto();
        $crypto1->setNom("BitCoin")
        ->setDevise('BTC')
        ->setQuantite(19000000)
        ->setPrix('38000')
        ->setCapitalisation(865378264);
        $manager->persist($crypto1);

        $crypto2 = new Crypto();
        $crypto2->setNom("Ethereum")
        ->setDevise('ETH')
        ->setPrix(2400)
        ->setQuantite('25000000')
        ->setCapitalisation(36748730);

        $manager->persist($crypto2);
        $manager->flush();

        $this->addReference('crypto-btc', $crypto1);
        $this->addReference('crypto-eth', $crypto2);
    }
}