<?php

namespace Ingenius\Products\Http\Controllers;

use Illuminate\Support\Facades\Response;
use Ingenius\Core\Http\Controllers\Controller;
use Ingenius\Products\Services\SkuGenerator;

class ProductSkuController extends Controller {

    public function generate() {

        $skuGenerator = new SkuGenerator();

        $sku = $skuGenerator->generateSku();

        return Response::api(data: $sku, message: 'SKU generated successfully');
    }

}