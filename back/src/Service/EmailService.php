<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Order;
use App\Security\EmailVerifier;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordToken;

class EmailService
{
    public function __construct(
        private MailerInterface $mailer,
        private PdfGeneratorService $pdfGeneratorService,
        private EmailVerifier $emailVerifier,
        private string $mailerFrom,
    ) {}

    public function sendInvoiceEmail(Order $order)
    {        
        $pdfFilePath = tempnam(sys_get_temp_dir(), 'invoice_') . '.pdf';
        $this->pdfGeneratorService->saveInvoiceToFile($order, $pdfFilePath);

        $email = (new Email())
            ->from($this->mailerFrom)
            ->to($order->getUser()->getEmail())
            ->subject('Votre commande')
            ->html('<p>Merci pour votre commande, vous trouverez votre facture dans ce mail</p>')
            ->attachFromPath($pdfFilePath, 'invoice.pdf', 'application/pdf');

        $this->mailer->send($email);

        unlink($pdfFilePath);
    }

    public function sendEmailConfirmation(User $user): void
    {
        $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
            (new TemplatedEmail())
                ->from(new Address($this->mailerFrom, 'Design Motor'))
                ->to($user->getEmail())
                ->subject('Confirmation de votre adresse email')
                ->htmlTemplate('registration/confirmation_email.html.twig')
        );
    }

    public function sendPasswordResetEmail(User $user, ResetPasswordToken $resetToken, string $frontendBaseUrl): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address($this->mailerFrom, 'DesignMotor'))
            ->to($user->getEmail())
            ->subject('Réinitialiser un mot de passe')
            ->htmlTemplate('reset_password/email.html.twig')
            ->context(
                [
                    'resetToken' => $resetToken,
                    'redirectPath' => $frontendBaseUrl
                ]
            );

        $this->mailer->send($email);
    }
}