<?php

namespace App\Service;

use App\Entity\Purchase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\EncoderInterface;

class PurchaseToCsvService
{
    private $csvEncoder;

    public function __construct(EncoderInterface $csvEncoder)
    {
        $this->csvEncoder = $csvEncoder;
    }

    public function createCSVFiles(Purchase $purchase): array
    {

        //die;
        $items = $purchase->getPurchaseItems();
        $brands = [];
        foreach ($items as $item){
            $product = $item->getProduct();
            if(!array_key_exists($product->getBrand(),$brands)){
                $brands[$product->getBrand()] = [];
            }
            $price = ($item->getProduct()->getPrice() / 100);
            $price = number_format($price,2);
            $price = str_replace('.',',',$price);
            $productObj = [
                'product' => $item->getProduct()->getName(),
                'id' => $item->getProduct()->getProductNumber(),
                'single' =>  $price,
                'quantity' => $item->getQuantity(),
            ];
            $brands[$product->getBrand()][] = $productObj;
        }
        dump($brands);
        die;
        $file = $this->csvEncoder->encode($brands,'csv');
        $filesystem = new Filesystem();
        $filesystem->dumpFile('../tmp/file.txt', $file);
        die("walter");

        return $brands;
    }
}
