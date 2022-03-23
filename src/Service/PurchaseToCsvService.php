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
        $orderSuffix = 0;
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
            $price                                = ($product->getPrice() / 100);
            $price                                = number_format($price, 2);
            $price                                = str_replace('.', ',', $price);
            $productObj                           = $this->getCSVArray();
            $productObj['AUFTRAGSNUMMER']         = $orderNumber.'.'.$orderSuffix++;
            $productObj['Artikelnummer oder EAN'] = $product->getProductNumber();
            $productObj['MENGE']                  = $item->getQuantity();
            $productObj['PREIS']                  = $price;
            $productObj['KUNDENNUMMER']           = $endCustomer->getCustomernumber();
            $productObj['Stadt']                  = $endCustomer->getCity();
            $productObj['Firma']                  = $endCustomer->getFirma();
            $productObj['Postleitzahl']           = $endCustomer->getPostcode();
            $productObj['Straße']                 = $endCustomer->getStreet();
            $brands[$product->getBrand()][]       = $productObj;
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
            "AUFTRAGSNUMMER"                         => "",
            "Auftragsdatum"                          => (new DateTime())->format("d.m.Y"),
            "Geplantes Lieferdatum"                  => "",
            "Währung"                                => "EUR",
            "Auftragsart"                            => "",
            "Zahlungsbedingungen"                    => "",
            "Versandart"                             => "",
            "Lieferbedingungen"                      => "",
            "Zahlungsart"                            => "",
            "Handelssprache"                         => "",
            "Artikelnummer oder EAN"                 => "",
            "Artikelbeschreibung"                    => "",
            "MENGE"                                  => "",
            "PREIS"                                  => "",
            "Steuersatz"                             => "",
            "Rabatt (%)"                             => "",
            "Notiz"                                  => "",
            "KUNDENNUMMER"                           => "",
            "Kunden Bestellnummer"                   => "",
            "Vertriebsweg"                           => "",
            "Kommission"                             => "",
            "Firma"                                  => "",
            "Firmenzusatz"                           => "",
            "Anrede"                                 => "",
            "Vorname"                                => "",
            "Nachname"                               => "",
            "E-Mail"                                 => "",
            "Telefonnummer"                          => "",
            "Straße"                                 => "",
            "Stadt"                                  => "",
            "Postleitzahl"                           => "",
            "LAND"                                   => "Deutschland",
            "Abweichende Lieferadresse Firma"        => "",
            "Abweichende Lieferadresse Firma-Zusatz" => "",
            "Abweichende Lieferadresse Vorname"      => "",
            "Abweichende Lieferadresse Nachname"     => "",
            "Abweichende Lieferadresse Straße"       => "",
            "Abweichende Lieferadresse Postleitzahl" => "",
            "Abweichende Lieferadresse Stadt"        => "",
            "Abweichende Lieferadresse Land"         => "",
            "Versandkostenartikel"                   => "",
        ];
    }
}
