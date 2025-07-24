<?php

namespace Ingenius\Products\Traits;

trait HasInventoriableData
{
    public function getStock(): ?float
    {
        return $this->stock_for_sale;
    }

    public function addStock(float $amount): void
    {
        $this->stock += $amount;
        $this->stock_for_sale += $amount;

        $this->save();
    }

    public function removeStock(float $amount): void
    {
        $this->stock -= $amount;
        $this->stock_for_sale -= $amount;

        if ($this->stock_for_sale < 0) {
            $this->stock_for_sale = 0;
        }

        if ($this->stock < 0) {
            $this->stock = 0;
        }

        $this->save();
    }

    public function inStock(): bool
    {
        return $this->stock_for_sale > 0;
    }

    public function handleStock(): bool
    {
        return $this->handle_stock;
    }

    public function hasEnoughStock(float $quantity): bool
    {
        // If stock_for_sale is null, we assume unlimited stock
        if ($this->stock_for_sale === null) {
            return true;
        }

        return $this->stock_for_sale >= $quantity;
    }
}
