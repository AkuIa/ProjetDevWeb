<?php

namespace App\Controller;

use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
     */
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
    /**
     * @Route("/user/list", name="user.list")
     * @return Response
     */
    public function list() : Response
    {
        return $this->render('user/list.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
    /**
     * Chercher et afficher un stage.
     * @Route("/user/detail/{devise}", name="user.detail", requirements={"devise" = "\w+"})
     * @param devise $devise
     * @return Response
     */
    public function detail($devise)
    {
        return $this->render('user/detail.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
}
