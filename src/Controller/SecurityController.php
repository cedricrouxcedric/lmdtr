<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Towns;
use App\Form\ChangePassType;
use App\Form\RegistrationType;
use App\Form\ResetPassType;
use App\Form\ValidationCodeType;
use App\Repository\MotoRepository;
use App\Repository\TownsRepository;
use App\Repository\PiecedetacheeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use LogicException;
use MongoDB\Driver\Manager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
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
         if ($this->getUser()) {
             return $this->redirectToRoute('target_path');
         }

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
     * @param UserPasswordEncoderInterface $encoder
     * @param MailerInterface $mailer
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function registration(Request $request,
                                 UserPasswordEncoderInterface $encoder,
                                 MailerInterface $mailer,
                                 EntityManagerInterface $manager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $checkUniqueUsername = $this->UserRepository->findOneBy(['username' => $user->getUsername()]);
            $checkUniqueEmail = $this->UserRepository->findOneBy(['email' => $user->getEmail()]);
            $townsRepo = $manager->getRepository(Towns::class);
            $town = $townsRepo->find(35854);
            if ($checkUniqueUsername === null && $checkUniqueEmail === null) {
                $password = $user->getPassword();
                $hash = $encoder->encodePassword($user, $password);
                $user->setPassword($hash);
                $user->setRoles(["ROLE_USER"]);
                $user->setConfirmationCode(md5(uniqid()));
                $user->setTown($town);
                $email = $user->getEmail();
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($user);
                $manager->flush();
                $this->sendMail($email,
                    $mailer,
                    $user,
                    'emails/confirmationAccount.html.twig',
                    'Confirmation de votre adresse email');
                $token = new UsernamePasswordToken(
                    $user,
                    $password,
                    'main',
                    $user->getRoles()
                );
                $this->get('security.token_storage')->setToken($token);
                $this->get('session')->set('_security_main', serialize($token));
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
    public function activation($token,
                               UserRepository $userRepository)
    {
        // check si un user a ce token
        $user = $userRepository->findOneBy(['confirmationCode' => $token]);
        // si pas de user avec ce token
        if (!$user) {
            throw $this->createNotFoundException('cet utilisateur n\'existe pas ');
        }
//        suppression du token
        $user->setConfirmationCode(null);
        // validation du compte
        $user->setValidateAccount(true);
        // attribution du role subscriber lors de la validation du compte
        $user->setRoles(["ROLE_SUBSCRIBER"]);
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        $this->addFlash('success', "Votre compte vient d'etre activé");
        return $this->redirectToRoute('lmdtr_index');
    }

    /**
     * @Route("/activationmoto/{token}",name="activation_moto")
     */

     public function activationMoto($token,
                                    MotoRepository $motoRepository)
     {
         $moto = $motoRepository->findOneBy(['confirmation_code'=> $token]);
         if (!$moto) {
             throw $this->createNotFoundException('Cette annonce n\'existe pas ');
         }
         $moto->setConfirmationCode(NULL);
         $moto->setValidate(true);
         $em = $this->getDoctrine()->getManager();
         $em->persist($moto);
         $em->flush();

         $this->addFlash('success', "Votre annonce vient d'etre validée");
         return $this->redirectToRoute('moto_index');
     }

    /**
     * @Route("/activationpiece/{token}", name="activation_piece")
     */

    public function activationPiece($token,
                                    PiecedetacheeRepository $piecedetacheeRepository)
    {
        $piece = $piecedetacheeRepository->findOneBy(['confirmation_code'=> $token]);
        if(!$piece) {
            throw $this->createNotFoundException('Cette annonce n\'existe pas ');
        }
        $piece->setConfirmationCode(NULL);
        $piece->setValidate(true);
        $em = $this->getDoctrine()->getManager();
        $em->persist($piece);
        $em->flush();

        $this->addFlash('success', "Votre annonce vient d'etre validée");
        return $this->redirectToRoute('piecedetachee_index');
    }

    /**
     * @Route ("/oubli-mdp", name="app_forgot_password")
     * @param Request $request
     * @param UserRepository $userRepository
     * @param TokenGeneratorInterface $tokenGenerator
     * @param MailerInterface $mailer
     * @return RedirectResponse|Response
     */
    public function forgottenPassword(Request $request,
                                      UserRepository $userRepository,
                                      TokenGeneratorInterface $tokenGenerator,
                                      MailerInterface $mailer)
    {

        $form = $this->createForm(ResetPassType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $user = $userRepository->findOneBy(['email' => $data['email']]);
            if (is_null($user)) {
                $this->addFlash('error', 'Cette adresse n\'existe pas');
                return $this->redirectToRoute('app_reset_password');
            }
            $token = $tokenGenerator->generateToken();
            try {
                $user->setResetToken($token);
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
            } catch (Exception $e) {
                $this->addFlash('danger', "Une erreur est survenue : " . $e->getMessage());
                return $this->redirectToRoute('app_login');
            }
            $this->sendMail($user->getEmail(),
                $mailer,
                $user,
                'emails/resetPassword.html.twig',
                'Réinitialisation de votre mot de passe');
            $this->addFlash('success', 'Un email de reinitialisation de votre mot de passe vient de vous être envoyé');
            return $this->redirectToRoute('app_login');
        }
        return $this->render('security/forgottenPassword.html.twig', ['emailForm' => $form->createView()]);
    }

    /**
     * @Route("/reset-mdp/{token}" , name="app_reset_password")
     */
    public function resetPassword(Request $request,
                                  $token,
                                  UserRepository $userRepository,
                                  UserPasswordEncoderInterface $encoder)
    {
        $user = $userRepository->findOneBy(['resetToken' => $token]);
        if (!$user) {
            $this->addFlash('error', 'Token inconnu');
            return $this->redirectToRoute('app_login');
        }
        $form = $this->createForm(ChangePassType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $code = $data->getpassword();
            $user->setPassword($encoder->encodePassword($user, $code));
            $user->setResetToken(null);
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'Votre mot de passe vient d\'etre modifié avec succés');
            return $this->redirectToRoute('app_login');
        } else {
            return $this->render('security/passwordChange.html.twig', ['changePassForm' => $form->createView()]);
        }
    }
    private function sendMail($to,
                              MailerInterface $mailer,
                              User $user,
                              $template,
                              $subject)
    {

        $email = (new TemplatedEmail())
            ->from(self::EMAILSITE)
            ->to($to)
            ->subject($subject)
            ->htmlTemplate($template)
            ->context([
                'user' => $user,
                'token' => $user->getResetToken(),
            ]);

        $mailer->send($email);
    }
}
