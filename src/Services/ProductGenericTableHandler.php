<?php

namespace Ingenius\Products\Services;

use Ingenius\Core\Services\AbstractTableHandler;
use Ingenius\Core\Services\GenericTableHandler;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ProductGenericTableHandler extends GenericTableHandler {
    protected function search(array $data, Builder $query): AbstractTableHandler
    {
        if (isset($data['search']) && is_array($data['search'])) {
            $query->where(function (Builder $query) use ($data) {
                $query->where(function(Builder $query) use ($data) {
                    $query->where(function ($subQuery) use ($data) {
                        foreach ($data['search'] as $search) {
                            if (isset($search['field']) && isset($search['value'])) {
                                $subQuery->orWhere($search['field'], 'ilike', '%' . $search['value'] . '%');
                            }
                        }
                    });
                })->orWhere(function(Builder $query) use ($data) {
                    $query->where(function ($subQuery) use ($data) {
                        foreach ($data['search'] as $search) {
                            if (isset($search['field']) && isset($search['value'])) {
                                $subQuery->orWhere(DB::raw("LOWER(searchby)"), 'ilike', '%' . strtolower($search['value']) . '%');
                            }
                        }
                    });
                })
                ;
            });
        }

        return $this;
    }
}