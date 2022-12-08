<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePredictionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'date' => ['required', 'date'],
            'y_hat' => ['required', 'numeric', 'min:-200', 'max:400'],
            'y_hat_lower' => ['required', 'numeric', 'min:-200', 'max:400'],
            'y_hat_upper' => ['required', 'numeric', 'min:-200', 'max:400']
        ];
    }
}
