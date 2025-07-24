<?php

namespace Ingenius\Products\Traits;

trait HasPurchasableData
{
    public function getFinalPrice(): int
    {
        return $this->sale_price;
    }

    public function getRegularPrice(): int
    {
        return $this->regular_price;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
