<?php

namespace Webkul\YurticiShipping\Carriers;

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
    protected $code  = 'yurticishipping';

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

        $totalShippingCost = 0;

        foreach (Cart::getCart()->items as $item) {
            $height = $item->product->height ?? 1;
            $width  = $item->product->width ?? 1;
            $length = $item->product->length ?? 1;
            $weight = $item->product->weight ?? 1;

            $volumetricWeight = ($width * $height * $length) / 3000;
            $chargeableWeight = max($weight, $volumetricWeight);

            $itemShippingCost = $this->calculateShippingCost($chargeableWeight) * $item->quantity;

            $totalShippingCost += $itemShippingCost;
        }

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
        $exchangeRate = core()->getExchangeRate(2)->rate;

        if (!$exchangeRate || $exchangeRate <= 0) {
            $exchangeRate = 1;
        }

        if ($chargeableWeight == 0) return 101.5 / $exchangeRate;
        if ($chargeableWeight >= 1 && $chargeableWeight <= 5) return 135.51 / $exchangeRate;
        if ($chargeableWeight >= 6 && $chargeableWeight <= 10) return 155.71 / $exchangeRate;
        if ($chargeableWeight >= 11 && $chargeableWeight <= 15) return 185.95 / $exchangeRate;
        if ($chargeableWeight >= 16 && $chargeableWeight <= 20) return 255.78 / $exchangeRate;
        if ($chargeableWeight >= 21 && $chargeableWeight <= 25) return 326.52 / $exchangeRate;
        if ($chargeableWeight >= 26 && $chargeableWeight <= 30) return 397.57 / $exchangeRate;

        return 13297 / $exchangeRate;
    }
}
