<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use App\Mailer\BucketMailer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends Controller
{
    /**
     * @Route("/connexion", name="user_login")
     */
    public function login(AuthenticationUtils $authenticationUtils, Request $request)
    {
        if ($this->getUser()){
            $this->addFlash('warning', 'Vous êtes déjà connecté !');
            return $this->redirectToRoute('home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('user/login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    /**
     * @Route("/inscription", name="user_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, BucketMailer $bucketMailer)
    {
        if ($this->getUser()){
            $this->addFlash('warning', 'Vous êtes déjà inscrit !');
            return $this->redirectToRoute('home');
        }

        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $user->setDateRegistered(new \DateTime());
            $user->setRoles(["ROLE_USER"]);

            //hash le mot de passe
            $hash = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash("success", "Votre compte a été créé !");

            //envoi de l'email
            $bucketMailer->sendRegistrationMail($user);

            return $this->redirectToRoute("home");
        }

        return $this->render('user/register.html.twig', [
            "form" => $form->createView()
        ]);
    }

    /**
     * C'est Symfony qui gère la déconnexion !
     * @Route("/deconnexion", name="user_logout")
     */
    public function logout(){}
}
