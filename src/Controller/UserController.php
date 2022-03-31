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
           $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(array('email' => $this->getUser()->getUsername()));
           $commentaires = $this->getDoctrine()->getRepository(Commentaire::class)->findBy(array('user' => $user));
           return $this->render('user/compte.html.twig', [
               'user' => $user,
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
           $laCrypto = $entityManager->getRepository(Crypto::class)->find($crypto);
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
         * @param EntityManagerInterface $em
         */
       public function ajouterFavoris(Crypto $crypto, EntityManagerInterface $em) : Response
       {
            $em = $this->getDoctrine()->getManager();
            $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(array('id' => $this->getUser()->getId()));
            $maCrypto = $this->getDoctrine()->getRepository(Crypto::class)->findOneBy(array('id' => $crypto->getId()));
            $user->addFavori($maCrypto);
            $em->persist($user);
            $em->persist($maCrypto);
            $em->flush();
           return $this->redirectToRoute('user.compte');
       }

        /**
         * @Route("/user/favoris/retirer/{devise}", name="user.retirer-favoris", requirements={"devise" = "\w+"})
         * @param Crypto $crypto
         */
        public function retirerFavoris(Crypto $crypto) : Response
        {
            $entityManager = $this->getDoctrine()->getManager();
            $user = $this->getDoctrine()->getRepository(User::class)->find($this->getUser()->getId());
            $crypto->removeUser($user);
            $entityManager->flush();
            return $this->redirectToRoute('user.compte');
        }


}
