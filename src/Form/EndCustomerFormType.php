<?php

namespace App\Form;

use App\Entity\EndCustomer;
use App\Entity\Purchase;
use App\Repository\EndCustomerRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EndCustomerFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'endCustomer',
            EntityType::class,
            [
                'class'        => EndCustomer::class,
                'choice_label' => function ($choice, $key, $value) {
                    /** $choice EndCustomer  */
                    return $choice->getCustomernumber(). " - " .$choice->getFirma();
                },
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                //'data_class' => EndCustomer::class,
                'compound'        => true,
                'invalid_message' => 'Kunde nicht gefunden!',
                'finder_callback' => function (EndCustomerRepository $userRepository, string $email) {
                    return $userRepository->findOneBy(['customernumber' => $email]);
                },
            ]
        );
    }

}
