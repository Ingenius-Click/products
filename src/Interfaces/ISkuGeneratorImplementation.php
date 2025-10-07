<?php

namespace Ingenius\Products\Interfaces;

interface ISkuGeneratorImplementation
{
    public function generateSku(): string;
}