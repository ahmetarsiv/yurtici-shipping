<?php

namespace Webkul\YurticiShipping\Carriers;

use Illuminate\Support\Facades\Http;
use Webkul\Shipping\Carriers\AbstractShipping;
use Webkul\Checkout\Models\CartShippingRate;
use Webkul\Checkout\Facades\Cart;

class YurticiShipping extends AbstractShipping
{
    /**
     * Shipping method code
     *
     * @var string
     */
    protected $code = 'yurticishipping';

    /**
     * Returns rate for shipping method
     *
     * @return CartShippingRate|false
     */
    public function calculate(): false|CartShippingRate
    {
        if (! $this->isAvailable()) {
            return false;
        }

        $totalShippingCost = $this->calculateTotalShippingCost();

        return $this->createShippingRateObject($totalShippingCost);
    }

    /**
     * Calculates total shipping cost for cart items
     *
     * @return float
     */
    private function calculateTotalShippingCost(): float
    {
        $totalShippingCost = 0;

        foreach (Cart::getCart()->items as $item) {
            $chargeableWeight = $this->getChargeableWeight($item);
            $totalShippingCost += $this->calculateShippingCost($chargeableWeight) * $item->quantity;
        }

        return $totalShippingCost;
    }

    /**
     * Determines chargeable weight of an item
     *
     * @param object $item
     * @return float
     */
    private function getChargeableWeight(object $item): float
    {
        $product = $item->product;
        $productType = $product->type ?? 'simple';

        if ($productType === 'configurable') {
            $productData = $this->fetchProductData($product->sku);
            $variant = $productData[0]['variants'][0] ?? null;

            $height = $variant['height'] ?? $product->height ?? 1;
            $width = $variant['width'] ?? $product->width ?? 1;
            $length = $variant['length'] ?? $product->length ?? 1;
            $weight = $variant['weight'] ?? $product->weight ?? 1;
        } else {
            $height = $product->height ?? 1;
            $width = $product->width ?? 1;
            $length = $product->length ?? 1;
            $weight = $product->weight ?? 1;
        }

        $volumetricWeight = ($width * $height * $length) / 3000;

        return max($weight, $volumetricWeight);
    }

    /**
     * Fetch product data from API
     *
     * @param string $sku
     * @return array
     */
    private function fetchProductData(string $sku): array
    {
        try {
            $response = Http::get("http://localhost:8000/api/v1/products", [
                'sku' => $sku
            ]);

            if ($response->successful()) {
                return $response->json()['data'] ?? [];
            }
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
        }

        return [];
    }

    /**
     * Creates a shipping rate object
     *
     * @param float $totalShippingCost
     * @return CartShippingRate
     */
    private function createShippingRateObject(float $totalShippingCost): CartShippingRate
    {
        $object = new CartShippingRate;
        $object->carrier = 'yurticishipping';
        $object->carrier_title = $this->getConfigData('title');
        $object->method = 'yurticishipping_standard';
        $object->method_title = $this->getConfigData('title');
        $object->method_description = $this->getConfigData('description');

        $object->price = core()->convertPrice($totalShippingCost, 'TRY');
        $object->base_price = $totalShippingCost;

        return $object;
    }

    /**
     * Determines shipping cost based on chargeable weight
     *
     * @param float $chargeableWeight
     * @return float
     */
    private function calculateShippingCost(float $chargeableWeight): float
    {
        $currentCurreny = core()->getCurrentCurrency()->id;
        $exchangeRateData = core()->getExchangeRate($currentCurreny);
        $exchangeRate = $exchangeRateData ? ($exchangeRateData->rate ?? 1) : 1;

        if ($exchangeRate <= 0) {
            $exchangeRate = 1;
        }

        return match (true) {
            $chargeableWeight == 0 => 101.5 / $exchangeRate,
            $chargeableWeight <= 5 => 135.51 / $exchangeRate,
            $chargeableWeight <= 10 => 155.71 / $exchangeRate,
            $chargeableWeight <= 15 => 185.95 / $exchangeRate,
            $chargeableWeight <= 20 => 255.78 / $exchangeRate,
            $chargeableWeight <= 25 => 326.52 / $exchangeRate,
            $chargeableWeight <= 30 => 397.57 / $exchangeRate,
            default => 13297 / $exchangeRate,
        };
    }
}
