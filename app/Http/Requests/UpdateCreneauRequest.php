<?php

namespace App\Http\Requests;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCreneauRequest extends FormRequest
{
    public function authorize(): bool
    {
          return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'date' => ['required', 'date'],
            'heure_debut' => ['required', 'date_format:H:i'],
            'duree' => ['required', 'integer', 'min:1'],
        ];
    }
}