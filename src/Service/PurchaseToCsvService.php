<?php

namespace App\Service;

use App\Entity\Purchase;
use DateTime;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Serializer\Encoder\EncoderInterface;

class PurchaseToCsvService
{
    private EncoderInterface $csvEncoder;

    public function __construct(EncoderInterface $csvEncoder)
    {
        $this->csvEncoder = $csvEncoder;
    }

    public function splitPurchaseforBrands(Purchase $purchase): array
    {
        $items  = $purchase->getPurchaseItems();
        $brands = [];
        foreach ($items as $item) {
            $product = $item->getProduct();
            if (!$product) {
                continue;
            }
            if (!array_key_exists($product->getBrand(), $brands)) {
                $brands[$product->getBrand()] = [];
            }
            $brands[$product->getBrand()][] = $product;
        }

        return $brands;
    }

    public function createCSVFiles(Purchase $purchase): array
    {
        $orderNumber = dechex(time());
        $items       = $purchase->getPurchaseItems();
        $brands      = [];
        $endCustomer = $purchase->getEndcustomer();
        if (!$endCustomer) {
            return [];
        }
        foreach ($items as $item) {
            $product = $item->getProduct();
            if (!$product) {
                continue;
            }
            if (!array_key_exists($product->getBrand(), $brands)) {
                $brands[$product->getBrand()] = [];
            }
            $price                               = ($product->getPrice() / 100);
            $price                               = number_format($price, 2);
            $price                               = str_replace('.', ',', $price);
            $productObj                          = $this->getCSVArray();
            $productObj['ORDER NUMBER']          = $orderNumber;
            $productObj['Article number or EAN'] = $product->getProductNumber();
            $productObj['QUANTITY']              = $item->getQuantity();
            $productObj['MANUAL UNIT PRICE']     = $price;
            $productObj['CUSTOMER NUMBER']       = $endCustomer->getCustomernumber();
            $productObj['City']                  = $endCustomer->getCity();
            $productObj['Company name']          = $endCustomer->getFirma();
            $productObj['Zip']                   = $endCustomer->getPostcode();
            $productObj['Street']                = $endCustomer->getStreet();
            $brands[$product->getBrand()][]      = $productObj;
        }
        $fileNames = [];
        foreach ($brands as $key => $brand) {
            $file                                    =
                $this->csvEncoder->encode($brand, 'csv', ['csv_delimiter' => ';',]);
            $filesystem                              = new Filesystem();
            $fileName                                = '../tmp/'.$key.'-'.$orderNumber.'.csv';
            $fileNames[$key.'-'.$orderNumber.'.csv'] = $fileName;
            $filesystem->dumpFile($fileName, $file);
        }

        return $fileNames;
    }

    private function getCSVArray(): array
    {
        return [
            "ORDER NUMBER"             => "",
            "Order date"               => (new DateTime())->format("d.m.Y"),
            "Planned shipping date"    => "",
            "Currency"                 => "EUR",
            "Order type"               => "",
            "Terms of payment"         => "",
            "Shpipment type"           => "",
            "Shipment method"          => "",
            "Payment method"           => "",
            "Commercial language"      => "",
            "Article number or EAN"    => "",
            "Article description"      => "",
            "QUANTITY"                 => "",
            "MANUAL UNIT PRICE"        => "",
            "Tax"                      => "",
            "Discount"                 => "",
            "Note"                     => "",
            "CUSTOMER NUMBER"          => "",
            "Order number at customer" => "",
            "Sales channel"            => "",
            "Commission"               => "",
            "Company name"             => "",
            "Company line 2"           => "",
            "Salutation"               => "",
            "First name"               => "",
            "Last name"                => "",
            "Email"                    => "",
            "Phone"                    => "",
            "Fax"                      => "",
            "Street"                   => "",
            "City"                     => "",
            "Zip"                      => "",
            "COUNTRY"                  => "Deutschland",
            "shippingCompany"          => "",
            "shippingCompany2"         => "",
            "shippingFirstName"        => "",
            "shippingLastName"         => "",
            "Shipping strasse"         => "",
            "Shipping zip"             => "",
            "Shipping city"            => "",
            "Shipping country"         => "",
            "Shipping cost item"       => "",
        ];
    }
}
