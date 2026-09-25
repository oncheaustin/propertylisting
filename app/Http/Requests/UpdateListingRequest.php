<?php

namespace App\Http\Requests;

class UpdateListingRequest extends StoreListingRequest
{
    public function rules(): array
    {
        return array_map(function (array $rules): array {
            array_unshift($rules, 'sometimes');

            return $rules;
        }, parent::rules());
    }
}
