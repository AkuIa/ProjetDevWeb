<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Crypto;
use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;



class UserController extends AbstractController
{

         /**
         * Créer un nouveau stage.
         * @Route("/user/compte", name="user.compte")
          */
       public function monCompte() : Response
       {
           $user = new User();
           $commentaires = new Commentaire();
           $user = $this->getDoctrine()->getRepository(User::class)->findBy(array('email' => $this->getUser()->getUsername()));
           $commentaires = $this->getDoctrine()->getRepository(Commentaire::class)->findBy(array('user' => $user[0]));
           return $this->render('user/compte.html.twig', [
               'user' => $user[0],
               'commentaires' => $commentaires,
           ]);
       }

       /**
        * @Route("/user/modifier/{commentaire}", name="user.modifier-commentaire", requirements={"commentaire" = "\w+"})
        * @param Commentaire $commentaire
        */
       public function modifierCommentaire(Commentaire $commentaire) : Response
       {
           $entityManager = $this->getDoctrine()->getManager();
           $product = $entityManager->getRepository(Commentaire::class)->find($commentaire);
           $product->setDescription($_POST['text-user-commentaire']);
           $entityManager->flush();
           return $this->redirectToRoute('user.compte');

       }

        /**
         * @Route("/user/supprimer/{commentaire}", name="user.supprimer-commentaire", requirements={"commentaire" = "\w+"})
         * @param Commentaire $commentaire
         */
       public function supprimerCommentaire(Commentaire  $commentaire) : Response
       {
           $entityManager = $this->getDoctrine()->getManager();
           $monCommentaire = $entityManager->getRepository(Commentaire::class)->find($commentaire);
           $entityManager->remove($monCommentaire);
           $entityManager->flush();
           return $this->redirectToRoute('user.compte');
       }

        /**
         * @Route("/user/ajouter/{crypto}", name="user.ajouter-commentaire", requirements={"crypto" = "\w+"})
         * @param Crypto $crypto
         */
       public  function  ajouterCommentaire(Crypto $crypto) : Response
       {
           $entityManager = $this->getDoctrine()->getManager();
           $laCrypto = new Crypto();
           $laCrypto = $entityManager->getRepository(Crypto::class)->find($crypto);
           $user = new User();
           $user = $this->getDoctrine()->getRepository(User::class)->findBy(array('email' => $this->getUser()->getUsername()));
           $commentaire = new Commentaire();
           $commentaire->setDescription($_POST['nouveau-commentaire']);
           $commentaire->setCrypto($laCrypto);
           $commentaire->setUser($user[0]);
           $date = new \DateTime();
           $date->setDate(date("Y"),date("m"), date("d"));
           $commentaire->setDatePublication($date);
           $entityManager->persist($commentaire);
           $entityManager->flush();
           return $this->redirectToRoute('crypto.detail', array('devise' => $laCrypto->getDevise()));
       }

        /**
         * @Route("/user/favoris/ajouter/{devise}", name="user.ajouter-favoris", requirements={"devise" = "\w+"})
         * @param Crypto $crypto
         */
       public function ajouterFavoris(Crypto $crypto) : Response
       {
            $entityManager = $this->getDoctrine()->getManager();
            $user = new User();
            $user = $user = $this->getDoctrine()->getRepository(User::class)->find($this->getUser()->getId());
            $favoris = $user->getFavoris();
            array_push($favoris, $crypto->getDevise());
            $user->setFavoris($favoris);
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('user.compte');
       }

    /**
     * @Route("/user/favoris/retirer/{devise}", name="user.retirer-favoris", requirements={"devise" = "\w+"})
     * @param Crypto $crypto
     */
    public function retirerFavoris(Crypto $crypto) : Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = new User();
        $user = $user = $this->getDoctrine()->getRepository(User::class)->find($this->getUser()->getId());
        $favoris = $user->getFavoris();
        $val = array($crypto->getDevise());
        $user->setFavoris(array_diff($favoris, $val));
        $entityManager->persist($user);
        $entityManager->flush();
        return $this->redirectToRoute('user.compte');
    }


}
