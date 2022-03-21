<?php

namespace App\Service;

class CurrencyConversor
{
    private string $exchangeratesApiKey = '';
    private array $currencies;

    private ?array $conversionRates = null; //array of arrays of strings which contains the conversion rate from the first index to the second index

    public function __construct(string $exchangeratesApiKey, array $currencies)
    {
        $this->exchangeratesApiKey = $exchangeratesApiKey;
        $this->currencies = $currencies;
    }

    public function convert(float $qty, string $from, string $to): float
    {
        if ($this->conversionRates == null) $this->conversionRates = $this->getconversionRates();

        return $qty * $this->conversionRates[$from][$to];
    }

    private function getConversionRates(): array
    {
        // Since we are using the free plan, get the conversion rates from EUR to everyone else
        $base = 'EUR';
        $exchangeRates = json_decode($this->getConversionRatesFromApi($base), true);

        $conversionRates = [
            $base => []
        ];
        foreach ($this->currencies as $currency) {
            $conversionRate                    = $exchangeRates['rates'][$currency];
            $conversionRates[$base][$currency] = $conversionRate;
            $conversionRates[$currency]        = [$base => 1 / $conversionRate, $currency => 1];
        }

        return $conversionRates;
    }

    private function getConversionRatesFromApi(string $base): string
    {
        $ch = curl_init("http://api.exchangeratesapi.io/v1/latest?access_key={$this->exchangeratesApiKey}&base={$base}");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $json = curl_exec($ch);
        curl_close($ch);

        return $json;
    }
}
