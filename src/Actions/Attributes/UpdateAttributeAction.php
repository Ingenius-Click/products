<?php

namespace Ingenius\Products\Actions\Attributes;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Ingenius\Products\Models\Attribute;
use Ingenius\Products\Models\AttributeOption;

class UpdateAttributeAction
{
    public function __invoke(Attribute $attribute, array $data): Attribute
    {
        return DB::transaction(function () use ($attribute, $data) {
            $attribute->update($data);

            if (isset($data['options']) && is_array($data['options'])) {
                $existingIds = [];

                foreach ($data['options'] as $index => $optionData) {
                    if (isset($optionData['id'])) {
                        // Update existing option
                        $option = $attribute->options()->find($optionData['id']);
                        if ($option) {
                            $option->update([
                                'name' => $optionData['name'],
                                'value' => $this->resolveOptionValue($optionData, $option),
                                'position' => $optionData['position'] ?? $index,
                            ]);
                            $existingIds[] = $option->id;
                        }
                    } else {
                        // Create new option
                        $option = $attribute->options()->create([
                            'name' => $optionData['name'],
                            'value' => $this->resolveOptionValue($optionData),
                            'position' => $optionData['position'] ?? $index,
                        ]);
                        $existingIds[] = $option->id;
                    }
                }

                // Remove options not in the update (and their stored images)
                $toDelete = $attribute->options()->whereNotIn('id', $existingIds)->get();
                foreach ($toDelete as $deletedOption) {
                    $this->deleteOptionImage($deletedOption->value);
                    $deletedOption->delete();
                }
            }

            return $attribute->fresh('options');
        });
    }

    /**
     * Resolve the value to persist for an option.
     * If a new image file is uploaded, replace any prior stored image.
     */
    private function resolveOptionValue(array $optionData, ?AttributeOption $existing = null): ?string
    {
        if (isset($optionData['image']) && $optionData['image'] instanceof UploadedFile) {
            if ($existing) {
                $this->deleteOptionImage($existing->value);
            }

            return $this->storeOptionImage($optionData['image']);
        }

        return $optionData['value'] ?? ($existing?->value ?? null);
    }

    private function storeOptionImage(UploadedFile $file): string
    {
        $filename = uniqid('attr_opt_', true) . '.' . $file->getClientOriginalExtension();
        $path = 'attribute-options/' . $filename;

        Storage::put($path, file_get_contents($file->getRealPath()));

        $url = asset($path);

        if (tenant()) {
            $url .= (str_contains($url, '?') ? '&' : '?') . 'tenant=' . tenant()->domains->first()?->domain;
        }

        return $url;
    }

    private function deleteOptionImage(?string $url): void
    {
        if (!$url) {
            return;
        }

        // Only delete files we own (under attribute-options/)
        $path = parse_url($url, PHP_URL_PATH) ?? $url;
        $needle = 'attribute-options/';
        $pos = strpos($path, $needle);

        if ($pos === false) {
            return;
        }

        $relative = substr($path, $pos);

        if (Storage::exists($relative)) {
            Storage::delete($relative);
        }
    }
}
