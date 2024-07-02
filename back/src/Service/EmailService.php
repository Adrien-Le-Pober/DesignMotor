<?php

namespace App\Service;

use App\Entity\Order;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\File;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\FilePart;
use Symfony\Component\Mailer\MailerInterface;

class EmailService
{
    private $mailer;
    private $pdfGeneratorService;
    private $mailerFrom;

    public function __construct(
        MailerInterface $mailer,
        PdfGeneratorService $pdfGeneratorService,
        string $mailerFrom
    ) {
        $this->mailer = $mailer;
        $this->pdfGeneratorService = $pdfGeneratorService;
        $this->mailerFrom = $mailerFrom;
    }

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
}