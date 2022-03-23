<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Crypto;
use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Cookie;
use function Composer\Autoload\includeFile;


class CryptoController extends AbstractController
{

    /**
     * @Route("/crypto", name="crypto")
     */
    public function index(): Response
    {
        if (empty($_COOKIE['theme'])) {
            $res = new Response();
            $cookie = new Cookie('theme',
                'light',
                strtotime('tomorrow'),
                '/',
                'localhost',
                true,
                true);
            $res->headers->setCookie($cookie);
            $res->send();
        }
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
        if (!empty($this->getUser())) {
            return $this->render('crypto/detail.html.twig', [
                'controller_name' => 'CryptoController',
                'crypto' => $crypto,
                'commentaires' => $commentaires,
                'user' => $this->getUser(),
            ]);
        }else{
            return $this->render('crypto/detail.html.twig', [
                'controller_name' => 'CryptoController',
                'crypto' => $crypto,
                'commentaires' => $commentaires,
            ]);
        }

    }

    /**
     * Créer un nouveau stage.
     * @Route("/inscription", name="crypto.inscription")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return RedirectResponse|Response
     */

    public function create(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder) : Response
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
                return $this->redirectToRoute('user.login');
            } else {
                return $this->render('user/create.html.twig', [
                    'form' => $form->createView(),
                    'error' => "Email déja utilisé.",
                ]);
            }
        }else{
            return $this->render('user/create.html.twig', [
                'form' => $form->createView(),
            ]);
        }
    }

    /**
     * @Route("/change_locale/{locale}", name="change_locale")
     */
    public function changeLocale($locale, Request $request)
    {
        // On stocke la langue dans la session
        $request->getSession()->set('_locale', $locale);

        // On revient sur la page précédente
        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/change_theme/{theme}", name="change_theme")
     */
    public function changeTheme($theme, Request $request)
    {
        if ($theme == 'light') {
            $theme = 'dark';
        }else{
            $theme = 'light';
        }
        setcookie('theme', $theme, 'tomorrow', '/', 'localhost', true);
        // On revient sur la page précédente
        return $this->redirect($request->headers->get('referer'));
    }
}
