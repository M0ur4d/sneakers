<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends AbstractController
{
    /**
     * @Route("/inscription", name="register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $manager = $this->getDoctrine()->getManager();
        $user = new User;
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $user->setRole('ROLE_USER');
            $user->setRegisterDate(new \DateTime('now'));

            $password = $user->getPassword();
            $password_hash = $encoder->encodePassword($user, $password);
            $user->setPassword($password_hash);

            $manager->persist($user);
            $manager->flush();

            $e = ($user ->getSexe() == 'm')? '' : 'e';

            $this->addFlash('success','Felicitations, Vous êtes bien inscrit' . $e);
            return $this->redirectToRoute('login');
        }

        return $this->render('user/register.html.twig', [
            'userForm' => $form -> createView()
        ]);
    }

    /**
     * @Route("/connexion", name="login")
     */
    public function login(AuthenticationUtils $auth)
    {
        $lastUsername = $auth ->getLastUsername();
        $error = $auth->getLastAuthenticationError();

        if ($error){
            $this->addFlash('error', 'Problème d\'identifiant');
        }

        return $this->render('user/login.html.twig', [
            'lastUsername' => $lastUsername,
        ]);
    }

    /**
     * @Route("/connexion_check", name="login_check")
     */
    public function loginCheck()
    {
        return $this->render('user/login.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    /**
     * @Route("/deconnexion", name="logout")
     */
    public function logout()
    {
        return $this->render('user/login.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }


}
