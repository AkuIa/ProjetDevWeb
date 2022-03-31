<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Crypto;
use App\Entity\User;
use App\Form\CryptoType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Require ROLE_USER for *every* controller method in this class.
 *
 * @IsGranted("ROLE_ADMIN")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/admin/index", name="admin.index")
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/admin/cryptos", name="admin.cryptos")
     * @return Response
     */
    public function listCryptos(): Response
    {
        $cryptos = $this->getDoctrine()->getRepository(Crypto::class)->findAll();
        return $this->render('admin/cryptos.html.twig', [
            'controller_name' => 'AdminController',
            'cryptos' => $cryptos,
        ]);
    }
    /**
     * @Route("/admin/cryptos/edit/{id}", name="admin.edit-crypto", requirements={"id" = "\w+"})
     * @return Response
     * @param Crypto $crypto
     */
    public function editCrypto(Crypto $crypto) : Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $maCrypto = $entityManager->getRepository(Crypto::class)->find($crypto);
        $maCrypto->setNom($_POST['nom']);
        $maCrypto->setDevise($_POST['devise']);
        $maCrypto->setPrix($_POST['prix']);
        $maCrypto->setCapitalisation($_POST['capitalisation']);
        $maCrypto->setQuantite($_POST['quantite']);
        $entityManager->flush();
        return $this->redirectToRoute('admin.cryptos');
    }
    /**
     * @Route("/admin/cryptos/delete/{id}", name="admin.delete-crypto", requirements={"id" = "\w+"})
     * @return Response
     * @param Crypto $crypto
     */
    public function deleteCrypto(Crypto $crypto) : Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $users = $entityManager->getRepository(User::class)->findAll();
        foreach ($users as $user) {
            $favoris = $user->getFavoris();
            foreach ($crypto->getUsers() as $user) {
                $crypto->removeUser($user);
                $entityManager->flush();
            }
        }
        $commentaires = $entityManager->getRepository(Commentaire::class)->findBy(array('crypto' => $crypto->getId()));
        foreach ($commentaires as $commentaire) {
            $entityManager->remove($commentaire);
            $entityManager->flush();
        }
        $entityManager->flush();
        $maCrypto = $entityManager->getRepository(Crypto::class)->find($crypto);
        $entityManager->remove($maCrypto);
        $entityManager->flush();
        return $this->redirectToRoute('admin.cryptos');
    }

    /**
     * @Route("/admin/cryptos/add", name="admin.add-crypto")
     * @return Response
     */
    public function addCrypto() : Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $devise = $_POST['devise'];
        $test = $entityManager->getRepository(Crypto::class)->findBy(array('devise' => $devise));
        if (empty($test)) {
            $crypto = new Crypto();
            $crypto->setNom($_POST['nom'])
                ->setDevise($devise)
                ->setQuantite($_POST['quantite'])
                ->setPrix($_POST['prix'])
                ->setCapitalisation($_POST['capitalisation']);
            $entityManager->persist($crypto);
            $entityManager->flush();
        }
        return $this->redirectToRoute('admin.cryptos');
    }

    /**
     * @Route("/admin/users", name="admin.users")
     * @return Response
     */
    public function listUsers(): Response
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        return $this->render('admin/users.html.twig', [
            'controller_name' => 'AdminController',
            'users' => $users,
        ]);
    }

    /**
     * @Route("/admin/users/edit/{id}", name="admin.edit-user", requirements={"id" = "\w+"})
     * @return Response
     * @param User $user
     */
    public function editUser(User $user) : Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $monUser = $entityManager->getRepository(User::class)->find($user);
        $monUser->setNom($_POST['nom']);
        $monUser->setPrenom($_POST['prenom']);
        $monUser->setEmail($_POST['email']);
        $role = [$_POST['role']];
        $monUser->setRoles($role);
        $entityManager->flush();
        return $this->redirectToRoute('admin.users');
    }

    /**
     * @Route("/admin/users/delete/{id}", name="admin.delete-user", requirements={"id" = "\w+"})
     * @return Response
     * @param User $user
     */
    public function deleteUser(User $user) : Response
    {

        $entityManager = $this->getDoctrine()->getManager();
        $monUser = $entityManager->getRepository(User::class)->find($user->getId());
        $commentaires = $entityManager->getRepository(Commentaire::class)->findBy(array('user' => $user->getId()));
        foreach ($commentaires as $commentaire) {
            $entityManager->remove($commentaire);
            $entityManager->flush();
        }
        $entityManager->flush();
        $entityManager->remove($monUser);
        $entityManager->flush();
        return $this->redirectToRoute('admin.users');
    }

    /**
     * @Route("/admin/users/add", name="admin.add-user")
     * @return Response
     */
    public function addUser(UserPasswordEncoderInterface $passwordEncoder) : Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $email = $_POST['email'];
        $test = $entityManager->getRepository(User::class)->findBy(array('email' => $email));
        if (empty($test)) {
            $user = new User();
            $this->passwordEncoder = $passwordEncoder;
            $role = [$_POST['role']];
            $user->setNom($_POST['nom'])
                ->setPrenom($_POST['prenom'])
                ->setRoles($role)
                ->setEmail($email)
                ->setPassword($this->passwordEncoder->encodePassword(
                    $user, $_POST['password']
                ));
            $entityManager->persist($user);
            $entityManager->flush();
        }
        return $this->redirectToRoute('admin.users');
    }

    /**
     * @Route("/admin/commentaires", name="admin.commentaires")
     * @return Response
     */
    public function listCommentaires(): Response
    {
        $commentaires = $this->getDoctrine()->getRepository(Commentaire::class)->findAll();
        return $this->render('admin/commentaires.html.twig', [
            'controller_name' => 'AdminController',
            'commentaires' => $commentaires,
        ]);
    }

    /**
     * @Route("/admin/commentaires/delete/{id}", name="admin.delete-commentaire", requirements={"id" = "\w+"})
     * @return Response
     * @param Commentaire $commentaire
     */
    public function deleteCommentaire(Commentaire $commentaire) : Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $monCommentaire = $entityManager->getRepository(Commentaire::class)->find($commentaire->getId());
        $entityManager->remove($monCommentaire);
        $entityManager->flush();
        return $this->redirectToRoute('admin.commentaires');
    }
}
