<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'    => 'nullable|string|max:60',
            'rating'  => 'required|integer|between:1,5',
            'comment' => 'required|string|min:3|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'rating.required'  => 'Pilih rating bintang.',
            'rating.between'   => 'Rating harus antara 1 sampai 5.',
            'comment.required' => 'Tulis ulasan Anda.',
            'comment.min'      => 'Ulasan minimal 3 karakter.',
        ];
    }
}
