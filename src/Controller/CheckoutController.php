<?php

namespace App\Controller;

use App\Entity\EndCustomer;
use App\Entity\Purchase;
use App\Form\CheckoutFormType;
use App\Form\EndCustomerFormType;
use App\Repository\PurchaseRepository;
use App\Service\CartStorage;
use App\Service\MailService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_USER")
 */
class CheckoutController extends AbstractController
{
    /**
     * @Route("/checkout", name="app_checkout")
     */
    public function checkout(
        Request $request,
        MailService $mailService,
        CartStorage $cartStorage,
        EntityManagerInterface $entityManager,
        SessionInterface $session
    ): Response {
        //$checkoutForm = $this->createForm(CheckoutFormType::class);
        $checkoutForm = $this->createForm(EndCustomerFormType::class);
        $checkoutForm->handleRequest($request);
        if ($checkoutForm->isSubmitted() && $checkoutForm->isValid()) {

            /** @var EndCustomer $endcustomer */
            $endcustomer = $checkoutForm->getData()['endCustomer'];
            $note        = $checkoutForm->getData()['note'];
            $purchase = new Purchase();
            $purchase->setEndcustomer($endcustomer);
            $purchase->setCustomerAddress($endcustomer->getStreet());
            $purchase->setCustomerCity($endcustomer->getCity());
            $purchase->setCustomerZip($endcustomer->getPostcode());
            $purchase->setCustomerName($endcustomer->getFirma());
            $purchase->setCustomerEmail('nomail@email.com');
            $purchase->setNote($note);
            $purchase->addItemsFromCart($cartStorage->getCart());
            $mailService->sendMailToHQ($purchase);
            $entityManager->persist($purchase);
            $entityManager->flush();
            $session->set('purchase_id', $purchase->getId());
            $cartStorage->clearCart();

            return $this->redirectToRoute('app_confirmation');
        }

        return $this->renderForm('checkout/checkout.html.twig', [
            'checkoutForm' => $checkoutForm,
        ]);
    }

    /**
     * @Route("/checkout/_element", name="_app_checkout_element")
     */
    public function _shoppingCartList(): Response
    {
        $checkoutForm = $this->createForm(EndCustomerFormType::class);

        return $this->renderForm('checkout/_checkout.html.twig', [
            'checkoutForm' => $checkoutForm,
        ]);
    }

    /**
     * @Route("/confirmation", name="app_confirmation")
     */
    public function confirmation(SessionInterface $session, PurchaseRepository $purchaseRepository): Response
    {
        $purchaseId = $session->get('purchase_id');
        $purchase   = $purchaseRepository->find($purchaseId);
        if (!$purchase) {
            throw $this->createNotFoundException('No purchase found');
        }

        return $this->render('checkout/confirmation.html.twig', [
            'purchase' => $purchase,
        ]);
    }
}
