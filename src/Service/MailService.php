<?php

namespace App\Service;

use App\Entity\Purchase;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailService
{
    private MailerInterface      $mailer;
    private PurchaseToCsvService $purchaseToCsvService;

    public function __construct(MailerInterface $mailer, PurchaseToCsvService $purchaseToCsvService)
    {
        $this->mailer = $mailer;
        $this->purchaseToCsvService = $purchaseToCsvService;
    }

    public function sendMailToHQ(Purchase $purchase){

        $files = $this->purchaseToCsvService->createCSVFiles($purchase);

        $files =

            $email = (new Email())
            ->from('hello@example.com')
            ->to('you@example.com')
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Time for Symfony Mailer!')
            ->text('Sending emails is fun again!')
            ->html('<p>See Twig integration for better HTML integration!</p>')
            ->attachFromPath('/path/to/documents/contract.doc', 'Contract', 'application/msword')
;

        $this->mailer->send($email);
    }
}
