<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SearchListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['sometimes', Rule::in(['rent', 'sale', 'shortlet'])],
            'min_price' => ['sometimes', 'numeric', 'min:0'],
            'max_price' => ['sometimes', 'numeric', 'min:0', 'gte:min_price'],
            'bedrooms' => ['sometimes', 'integer', 'min:0', 'max:255'],
            'latitude' => ['required_with:longitude,radius_km', 'numeric', 'between:-90,90'],
            'longitude' => ['required_with:latitude,radius_km', 'numeric', 'between:-180,180'],
            'radius_km' => ['required_with:latitude,longitude', 'numeric', 'gt:0', 'max:20000'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ];
    }
}
