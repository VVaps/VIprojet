<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
   // l'utilisateur a le droit d'utiliser cette requète
    public function authorize(): bool
    {
        return true;
    }

    // protected function prepareForValidation(): void
    // {
    //     $this->merge([
    //         'price' => json_decode($this->price)
    //     ]);
    // }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description'=> ['required', 'text'],
            'qty_available' => 'nullable|numeric|min:0',
            'id_artisan' => 'required|exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'description.required' => 'La description est obligatoire.',
            'prix.required' => 'Le prix est obligatoire.',
            'prix.numeric' => 'Le prix doit être un nombre.',
            'id_artisan.required' => 'L\'identifiant artisan est obligatoire.',
            'id_artisan.exists' => 'L\'artisan n\'existe pas.',
        ];
    }

}
