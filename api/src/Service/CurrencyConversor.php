<?php

namespace App\Service;

class CurrencyConversor
{
    private string $exchangeratesApiKey = '';
    private ?array $conversionRates = null;

    public function __construct(string $exchangeratesApiKey)
    {
        $this->exchangeratesApiKey = $exchangeratesApiKey;
    }

    public function convert(float $qty, string $from, string $to): float
    {
        if ($this->conversionRates == null) $this->conversionRates = $this->getconversionRates();

        return $qty * $this->conversionRates[$from][$to];
    }

    private function getConversionRates(): array
    {
        // Since we are using the free plan, get the conversion rates from EUR to everyone else
        $ch = curl_init("http://api.exchangeratesapi.io/v1/latest?access_key={$this->exchangeratesApiKey}&base=EUR");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $json = curl_exec($ch);
        curl_close($ch);

        $exchangeRates = json_decode($json, true);

        $conversionRates = [
            'EUR' => [
                'EUR' => 1,
                'USD' => $exchangeRates['rates']['USD']
            ],
            'USD' => [
                'EUR' => 1 / $exchangeRates['rates']['USD'],
                'USD' => 1
            ]
        ];
        return $conversionRates;
    }
}
