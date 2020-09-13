<?php

namespace App\Controller;

use App\Entity\Moto;
use App\Entity\Piecedetachee;
use App\Entity\User;
use App\Form\ContactType;
use App\Form\ContactVendeurType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Security\Core\User\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


class ContactController extends AbstractController
{
    const EMAILSITE = 'lesmotardsdetaregion@gmail.com';
    private $mailer;

    public function __construct(MailerInterface $mailer){
        $this->mailer = $mailer;
    }
    /**
     * @Route("/contact", name="contact")
     */
    public function index(Request $request,
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

            $this->mailer->send($email);
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

            $this->mailer->send($email);
            $this->addFlash('success', 'Le message a bien été envoyé');
            return $this->redirectToRoute('moto_index');
        }
        return $this->render('contact/contactVendeur.html.twig', [
            'contactVendeurForm' => $form->createView(),
        ]);
    }

    public function sendNewMotoConfirmation(User $user,
                                            Moto $moto)
    {
        $images = $moto->getImages();
        $email = (new TemplatedEmail())
            ->from(SELF::EMAILSITE)
            ->to($user->getEmail())
            ->subject('Creation d\'une annonce de moto')
            ->htmlTemplate('emails/newBikeOnSale.html.twig');
        foreach ($images as $image) {
            $type = pathinfo($image->getName(), PATHINFO_EXTENSION);
            $data = file_get_contents('../public/uploads/' . $image->getName());
            $imagestr[] = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }

            $email = $email
            ->context([
                'user'=>$user,
                'moto'=>$moto,
                'token' =>$moto->getConfirmationCode(),
                'imagesstr' => $imagestr,
            ]);
        $this->mailer->send($email);
        $this->addFlash('success','Un mail de confirmation de creation de votre annonce vient de vous etre envoyé');
        return $this->redirectToRoute('moto_index');
    }

    public function sendNewPiecedetacheeConfirmation(User $user,
                                                     Piecedetachee $piecedetachee)
    {
        $images = $piecedetachee->getImages();
        $email = (new TemplatedEmail())
            ->from(SELF::EMAILSITE)
            ->to($user->getEmail())
            ->subject('Creation d\'une annonce de piéce détachée')
            ->htmlTemplate('emails/newPiecedetacheeOnSale.html.twig');
        foreach ($images as $image) {
            $type = pathinfo($image->getName(), PATHINFO_EXTENSION);
            $data = file_get_contents('../public/uploads/' . $image->getName());
            $imagestr[] = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }

        $email = $email
            ->context([
                'user'=>$user,
                'piece'=>$piecedetachee,
                'token' =>$piecedetachee->getConfirmationCode(),
                'imagesstr' => $imagestr,
            ]);
        $this->mailer->send($email);
        $this->addFlash('success','Un mail de confirmation de creation de votre annonce vient de vous etre envoyé');
        return $this->redirectToRoute('piecedetachee_index');
    }

    /**
     * @IsGranted("ROLE_SUBSCRIBER")
     * @Route("/contactpiece/{piece}", name="contact_vendeur_piece")
     */
    public function sendMessageToVendeurPiece(Piecedetachee $piece,
                                              Request $request,
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

            $this->mailer->send($email);
            $this->addFlash('success', 'Le message a bien été envoyé');
            return $this->redirectToRoute('piecedetachee_index');
        }
        return $this->render('contact/contactVendeur.html.twig', [
            'contactVendeurForm' => $form->createView(),
        ]);
    }
}
