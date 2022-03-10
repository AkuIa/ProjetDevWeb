<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Crypto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CryptoController extends AbstractController
{
    /**
     * @Route("/crypto", name="crypto")
     */
    public function index(): Response
    {
        return $this->render('crypto/index.html.twig', [
            'controller_name' => 'CryptoController',
        ]);
    }
    /**
     * @Route("/crypto/list", name="crypto.list")
     * @return Response
     */
    public function list() : Response
    {
        $cryptos = $this->getDoctrine()->getRepository(Crypto::class)->findAll();
        return $this->render('crypto/list.html.twig', [
            'controller_name' => 'CryptoController',
            'cryptos' => $cryptos,
        ]);
    }
    /**
     * Chercher et afficher un stage.
     * @Route("/crypto/detail/{devise}", name="crypto.detail", requirements={"devise" = "\w+"})
     * @param Crypto $crypto
     * @return Response
     */
    public function detail(Crypto $crypto) : Response
    {
        $commentaires = $this->getDoctrine()->getRepository(Commentaire::class)->findBy(array('crypto' => $crypto->getId()));
        return $this->render('crypto/detail.html.twig', [
            'controller_name' => 'CryptoController',
            'crypto' => $crypto,
            'commentaires' => $commentaires,
        ]);
    }
}
