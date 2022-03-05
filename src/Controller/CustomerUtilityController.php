<?php

namespace App\Controller;

use App\Entity\EndCustomer;
use App\Repository\EndCustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_USER")
 */
class CustomerUtilityController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    /**
     * @Route("/api/customer/", methods="GET", name="endcustomer_api")
     */
    public function getEndCustomerApi(EndCustomerRepository $endCustomerRepository, Request $request)
    {
        $users = $endCustomerRepository->findAllMatching($request->query->get('query'));

        return $this->json([
            'users' => $users,
        ], 200, [], ['groups' => ['main']]);

    }

    /**
     * @Route("/customer/new", methods="POST", name="app_add_new_customer")
     */
    public function addCustomer(EntityManagerInterface $entityManager, Request $request)
    {

        $customer = new EndCustomer();
        $customer->setCity($request->request->get('city'));
        $customer->setFirma($request->request->get('firma'));
        $customer->setCountry($request->request->get('country'));
        $customer->setPostcode($request->request->get('postcode'));
        $customer->setStreet($request->request->get('street'));
        $customer->setCustomernumber('000'.random_int(1, 9999));
        $entityManager->persist($customer);
        $entityManager->flush();

        return new Response(
            '<div style="padding:5px; background: #157347; color: white; border-radius: 5%;"><i class="fa fa-check"></i> '.$customer->getFirma().' wurde angelegt</div>'
        );
    }

}
