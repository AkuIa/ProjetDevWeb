<?php
namespace App\DataFixtures;

use App\Entity\Crypto;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CryptoFixtures extends Fixture
{
    /**
     * @param ObjectManager $manager
     *
     * @return void
     */
    public function load(ObjectManager $manager) : void
    {
        $data = $this->get_json();
        $data = $data->data;

        foreach ($data as $key => $value) {
            $nom = $value->slug;
            $devise = $value->symbol;
            $prix = round($value->metrics->market_data->price_usd, 8);
            $market_cap = round($value->metrics->marketcap->current_marketcap_usd);
            $qte = round($value->metrics->supply->circulating);

            $crypto = new Crypto();
            $crypto->setNom($nom)
                ->setDevise($devise)
                ->setQuantite($qte)
                ->setPrix($prix)
                ->setCapitalisation($market_cap);
            $manager->persist($crypto);

            $this->addReference('crypto-' . strtolower($devise), $crypto);
        }

        $manager->flush();
    }

    function get_json() {
        $url = "https://data.messari.io/api/v2/assets?limit=100&fields=slug,symbol,metrics/market_data/price_usd,metrics/marketcap/current_marketcap_usd,metrics/supply/circulating";

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $headers = array(
            "Accept: application/json",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $resp = curl_exec($curl);

        if ($resp === false) {
            var_dump(curl_error($curl));
            $data = json_decode("public/data_crypto.json");

        } else {
            $data = json_decode($resp);

            $fp = fopen("public/data_crypto.json", 'w');
            fwrite($fp, $resp);
            fclose($fp);
        }
        curl_close($curl);

        return $data;


    }
}
