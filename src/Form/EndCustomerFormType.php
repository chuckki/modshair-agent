<?php

namespace App\Form;

use App\Entity\EndCustomer;
use App\Entity\Purchase;
use App\Repository\EndCustomerRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class EndCustomerFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('endCustomer', EntityType::class, [
            'class'        => EndCustomer::class,
            'label'        => 'Kunde',
            'choice_label' => function ($choice, $key, $value) {
                /** $choice EndCustomer  */
                return $choice->getCustomernumber()." - ".$choice->getFirma();
            },
            'data' => null,
            'placeholder' => 'Choose an option',
            'constraints'  => [
                new NotBlank([
                    'message' => 'Bitte geben Sie einen Kunden an.',
                ]),
            ],
        ])->add('note', TextareaType::class, [
            'label'    => 'Bemerkung',
            'required' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'compound'        => true,
            'invalid_message' => 'Kunde nicht gefunden!',
            'placeholder' => 'Choose an option',
            'data' => null,
            'finder_callback' => function (EndCustomerRepository $userRepository, string $email) {
                return $userRepository->findOneBy(['customernumber' => $email]);
            },
        ]);
        //$resolver->setRequired(['endCustomer']);
    }

}
