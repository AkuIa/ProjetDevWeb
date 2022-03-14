<?php
namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    /**
     * @param ObjectManager $manager
     *
     * @return void
     */

    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager) : void
    {
        $user1 = new User();
        $user1->setEmail("alexandre.ruaux@sfr.fr")
        ->setNom("Ruaux")
        ->setPrenom("Alexandre")
        ->setFavoris(["BTC"])
        ->setRoles(["ROLE_USER,ROLE_ADMIN"])
        ->setPassword($this->passwordEncoder->encodePassword(
            $user1, 'test'
        ));
        $manager->persist($user1);

        $manager->flush();

        $this->addReference('user-admin', $user1);
    }
}