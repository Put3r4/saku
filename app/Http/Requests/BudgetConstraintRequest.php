<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BudgetConstraintRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'budget' => [
                'required',
                'numeric',
                'min:1000',
                'max:1000000',
            ],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'budget.required' => 'Budget harus diisi.',
            'budget.numeric' => 'Budget harus berupa angka.',
            'budget.min' => 'Budget minimal Rp 1.000.',
            'budget.max' => 'Budget maksimal Rp 1.000.000.',
        ];
    }
}
