<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCreneauRequest extends FormRequest
{
   public function authorize(): bool
{
    return $this->user()?->role === 'admin';
}

public function rules(): array
{
    return [
        'date' => ['required', 'date', 'after_or_equal:today'],
        'heure_debut' => ['required', 'date_format:H:i'],
        'duree' => ['required', 'integer', 'min:1'],
    ];
    }
}