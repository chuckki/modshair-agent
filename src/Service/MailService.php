<?php

namespace App\Service;

use App\Entity\Purchase;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class MailService
{
    private MailerInterface      $mailer;
    private PurchaseToCsvService $purchaseToCsvService;

    public function __construct(MailerInterface $mailer, PurchaseToCsvService $purchaseToCsvService)
    {
        $this->mailer               = $mailer;
        $this->purchaseToCsvService = $purchaseToCsvService;
    }

    public function sendMailToHQ(Purchase $purchase): void
    {
        $files = $this->purchaseToCsvService->createCSVFiles($purchase);
        $email = (new TemplatedEmail())->from('hello@example.com')->to('cschneider@modshair.de')->subject(
            'Modshair Agent Bestellung'
        )->htmlTemplate('email/confirmation.html.twig')->context(
            [
                'purchase'         => $purchase,
                'purchaseForBrand' => $this->purchaseToCsvService->splitPurchaseforBrands($purchase),
            ]
        );
        foreach ($files as $key => $file) {
            $email->attachFromPath($file, $key, 'text/csv');
        }
        $this->mailer->send($email);
    }
}
