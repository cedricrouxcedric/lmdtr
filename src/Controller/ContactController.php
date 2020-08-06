<?php

namespace App\Controller;

use App\Entity\Moto;
use App\Entity\Piecedetachee;
use App\Form\ContactType;
use App\Form\ContactVendeurType;
use App\Repository\MotoRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Security\Core\User\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


class ContactController extends AbstractController
{
    const EMAILSITE = 'lesmotardsdetaregion@gmail.com';

    /**
     * @Route("/contact", name="contact")
     */
    public function index(Request $request,
                          MailerInterface $mailer,
                          ?UserInterface $user)
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $contact = $form->getData();
            $copy = is_null($user) ? $contact['email'] : $user->getemail();
            // on envoie le mail
            $email = (new TemplatedEmail())
                ->from($user ? $copy : $contact['email'])
                ->to(self::EMAILSITE)
                ->replyTo($copy)
                ->cc($copy)
                ->subject('Message de contact envoyé depuis lmdtr : ' . $copy)
                ->htmlTemplate('emails/contact.html.twig')
                ->context([
                    'contact' => $contact,
                    'user' => $user,
                ]);

            $mailer->send($email);
            $this->addFlash('success', 'Le message a bien été envoyé');
            return $this->redirectToRoute('contact');
        }
        return $this->render('contact/index.html.twig', [
            'contactForm' => $form->createView(),
        ]);
    }

    /**
     * @IsGranted("ROLE_SUBSCRIBER")
     * @Route("/contact/{moto}", name="contactmoto_vendeur")
     */
    public function sendMessageToVendeur(Moto $moto,
                                         Request $request,
                                         MailerInterface $mailer,
                                         UserInterface $user)
    {
        $form = $this->createForm(ContactVendeurType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $copy = $user->getemail();
            $data = $form->getData();
            $email = (new TemplatedEmail())
                ->from($copy)
                ->to($moto->getVendeur()->getEmail())
                ->replyTo($copy)
                ->cc($copy)
                ->subject('Votre annonce sur Lesmotardsdetaregion')
                ->htmlTemplate('emails/contactVendeur.html.twig')
                ->context([
                    'message' => $data,
                    'moto' => $moto,
                    'user' => $user,
                ]);

            $mailer->send($email);
            $this->addFlash('success', 'Le message a bien été envoyé');
            return $this->redirectToRoute('moto_index');
        }
        return $this->render('contact/contactVendeur.html.twig', [
            'contactVendeurForm' => $form->createView(),
        ]);
    }
    /**
     * @IsGranted("ROLE_SUBSCRIBER")
     * @Route("/contact/{piece}", name="contact_vendeur")
     */
    public function sendMessageToVendeurPiece(Piecedetachee $piece,
                                         Request $request,
                                         MailerInterface $mailer,
                                         UserInterface $user)
    {
        $form = $this->createForm(ContactVendeurType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $copy = $user->getemail();
            $data = $form->getData();
            $email = (new TemplatedEmail())
                ->from($copy)
                ->to($piece->getVendeur()->getEmail())
                ->replyTo($copy)
                ->cc($copy)
                ->subject('Votre annonce sur Lesmotardsdetaregion')
                ->htmlTemplate('emails/contactVendeurPieceDetachee.html.twig')
                ->context([
                    'message' => $data,
                    'piecedetachee' => $piece,
                    'user' => $user,
                ]);

            $mailer->send($email);
            $this->addFlash('success', 'Le message a bien été envoyé');
            return $this->redirectToRoute('piecedetachee_index');
        }
        return $this->render('contact/contactVendeur.html.twig', [
            'contactVendeurForm' => $form->createView(),
        ]);
    }
}
