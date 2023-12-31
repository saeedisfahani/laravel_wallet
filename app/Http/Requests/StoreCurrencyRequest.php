<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCurrencyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
        return in_array($this->method(), $this->allowedMethods());
    }


    public function allowedMethods(): array
    {
        return ['POST'];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'key' => ['required', 'string', 'unique:currencies'],
            'symbol' => ['required', 'string'],
            'iso_code' => ['required', 'string', 'unique:currencies']
        ];
    }
}
