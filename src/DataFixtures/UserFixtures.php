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
        $user1->setEmail("admin@gmail.com")
        ->setNom("Admin")
        ->setPrenom("Admin")
        ->setRoles(["ROLE_ADMIN"])
        ->setPassword($this->passwordEncoder->encodePassword(
            $user1, 'admin'
        ));
        $manager->persist($user1);

        $user2 = new User();
        $user2->setEmail("test@gmail.com")
            ->setNom("test")
            ->setPrenom("test")
            ->setRoles(["ROLE_USER"])
            ->setPassword($this->passwordEncoder->encodePassword(
                $user2, 'test'
            ));
        $manager->persist($user2);

        $manager->flush();

        $this->addReference('user-admin', $user1);
    }
}