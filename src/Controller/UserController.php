<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
       /**
        * Créer un nouveau stage.
        * @Route("/inscription", name="user.create")
        * @param Request $request
        * @param EntityManagerInterface $em
        * @return RedirectResponse|Response
        */

       public function create(HttpFoundationRequest $request, EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder) : Response
       {
           $this->passwordEncoder = $passwordEncoder;
           $user = new User();
           $form = $this->createForm(UserType::class, $user);
           $form->handleRequest($request);
           if ($form->isSubmitted() && $form->isValid()) {
               $test = $this->getDoctrine()->getRepository(User::class)->findBy(array('email' => $user->getEmail()));
               $nb = sizeof($test);
               if ($nb == 0) {
                   $user->setRoles(['ROLE_USER']);
                   $user->setPassword($this->passwordEncoder->encodePassword(
                       $user, $user->getPassword()
                   ));
                   $em->persist($user);
                   $em->flush();
                   return $this->redirectToRoute('crypto');
               } else {
                   return $this->render('user/create.html.twig', [
                       'form' => $form->createView(),
                   ]);
               }
               return $this->render('user/create.html.twig', [
                   'form' => $form->createView(),
               ]);
           }
       }
}
