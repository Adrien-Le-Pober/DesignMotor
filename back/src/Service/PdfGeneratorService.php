<?php

namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\Order;
use App\Repository\VehicleRepository;
use Twig\Environment;

class PdfGeneratorService
{
    public function __construct(
        private Environment $twig,
        private VehicleRepository $vehicleRepository
    ) {}

    public function saveInvoiceToFile(Order $order, string $filePath): void
    {
        $pdfContent = $this->generateInvoice($order);
        file_put_contents($filePath, $pdfContent);
    }

    private function generateInvoice(Order $order): string
    {
        $html = $this->twig->render('invoice/invoice.html.twig', [
            'order' => $order
        ]);

        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }
}