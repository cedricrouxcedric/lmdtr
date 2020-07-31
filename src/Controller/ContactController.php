<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class ContactController extends AbstractController
{
    const EMAILSITE = 'lesmotardsdetaregion@gmail.com';

    /**
     * @Route("/contact", name="contact")
     */
    public function index(Request $request, MailerInterface $mailer)
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contact = $form->getData();

            // on envoie le mail

                $email = (new TemplatedEmail())
                    ->from($contact['email'])
                    ->to(self::EMAILSITE)
                    // TODO rajouter une condition si l'utilisateur est identifié metttre le mettre en copie
                    //->cc($contact['email'])
                    ->subject('Message de contact envoyé depuis lmdtr : ' .$contact['email'])
                   ->htmlTemplate('emails/contact.html.twig')
                    ->context([
                        'contact' => $contact,
                    ]);

                $mailer->send($email);
            $this->addFlash('success', 'Le message a bien été envoyé');
            return $this->redirectToRoute('contact');
        }
        return $this->render('contact/index.html.twig', [
            'contactForm' => $form->createView(),
        ]);
    }
}
