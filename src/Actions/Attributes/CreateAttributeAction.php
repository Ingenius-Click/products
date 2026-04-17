<?php

namespace Ingenius\Products\Actions\Attributes;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Ingenius\Products\Models\Attribute;

class CreateAttributeAction
{
    public function __invoke(array $data): Attribute
    {
        return DB::transaction(function () use ($data) {
            $attribute = Attribute::create($data);

            if (isset($data['options']) && is_array($data['options'])) {
                foreach ($data['options'] as $index => $option) {
                    $value = $option['value'] ?? null;

                    if (isset($option['image']) && $option['image'] instanceof UploadedFile) {
                        $value = $this->storeOptionImage($option['image']);
                    }

                    $attribute->options()->create([
                        'name' => $option['name'],
                        'value' => $value,
                        'position' => $option['position'] ?? $index,
                    ]);
                }
            }

            return $attribute->load('options');
        });
    }

    private function storeOptionImage(UploadedFile $file): string
    {
        $filename = uniqid('attr_opt_', true) . '.' . $file->getClientOriginalExtension();
        $path = 'attribute-options/' . $filename;

        Storage::put($path, file_get_contents($file->getRealPath()));

        return asset($path);
    }
}
