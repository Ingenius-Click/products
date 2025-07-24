<?php

namespace Ingenius\Products\Traits;

trait HasBaseProductibleData
{
    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getSku(): string
    {
        return $this->sku;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function images(): ?array
    {
        return $this->getImagesAttribute();
    }
}
