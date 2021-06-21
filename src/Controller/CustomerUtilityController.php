<?php

namespace App\Controller;

use App\Repository\EndCustomerRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CustomerUtilityController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    /**
     * @Route("/api/customer/", methods="GET", name="endcustomer_api")
     * @IsGranted("ROLE_USER")
     */
    public function getEndCustomerApi(EndCustomerRepository $endCustomerRepository, Request $request){

        $users = $endCustomerRepository->findAllMatching($request->query->get('query'));

        return $this->json([
            'users' => $users
        ], 200, [], ['groups' => ['main']]);

    }
}
