<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Form\ValidationCodeType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class SecurityController extends AbstractController
{
    const EMAILSITE = 'lesmotardsdetaregion@gmail.com';

    /**
     * @var UserRepository
     */
    private $repository;

    /**
     * SecurityController constructor.
     * @param UserRepository $repository
     */

    public function __construct(UserRepository $repository)
    {
        $this->UserRepository = $repository;

    }


    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/inscription", name="security_registration")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function registration(Request $request,
                                 UserPasswordEncoderInterface $encoder,
                                 MailerInterface $mailer): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $checkUniqueUsername = $this->UserRepository->findOneBy(['username' => $user->getUsername()]);
            $checkUniqueEmail = $this->UserRepository->findOneBy(['email' => $user->getEmail()]);
            if ($checkUniqueUsername === null && $checkUniqueEmail === null) {
                $password = $user->getPassword();
                $hash = $encoder->encodePassword($user, $password);
                $user->setPassword($hash);
                $user->setRoles(["ROLE_USER"]);
                $user->setConfirmationCode(md5(uniqid()));
                $email = $user->getEmail();
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($user);
                $manager->flush();
                $this->sendCode($email, $mailer, $user);
                $token = new UsernamePasswordToken(
                    $user,
                    $password,
                    'main',
                    $user->getRoles()
                );
                $this->get('security.token_storage')->setToken($token);
                $this->get('session')->set('_security_main',serialize($token));
            }
            $this->addFlash('success', "Votre compte utilisateur à bien été crée veuillez valider votre compte");
            return $this->redirectToRoute('moto_index');
        }
        if (isset($checkUniqueUsername)) {
            $this->addFlash('error', "Nom d'utilisateur non disponible");
        }
        if (isset($checkUniqueEmail)) {
            $this->addFlash('error', "Adresse mail non disponible");
        }


        return $this->render('security/registration.html.twig', ['form' => $form->createView()]);
    }


    /**
     * @Route("/activation/{token}", name="activation")
     */
    public function activation($token, UserRepository $userRepository) {
        // check si un user a ce token
        $user = $userRepository->findOneBy(['confirmationCode' => $token]);
        // si pas de user avec ce token
        if(!$user){
            throw $this->createNotFoundException('cet utilisateur n\'existe pas ');
        }
//        suppression du token
        $user->setConfirmationCode(null);
        // validation du compte
        $user->setValidateAccount(true);
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        $this->addFlash('success', "Votre compte vient d'etre activé");
        return $this->redirectToRoute('lmdtr_index');
    }


    private function sendCode($to,
                              MailerInterface $mailer,
                              User $user)
    {

        $email = (new TemplatedEmail())
            ->from(self::EMAILSITE)
            ->to($to)
            ->subject('Confirmation de votre adresse email')
            ->htmlTemplate('emails/confirmationAccount.html.twig')
            ->context([
                'user' => $user,
            ]);

        $mailer->send($email);
    }

    private function generateRandomString($length)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
