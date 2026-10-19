<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterApiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'     => 'required|string|max:100',
            'email'    => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'     => 'Nama pengguna wajib diisi.',
            'email.required'    => 'Alamat email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'email.unique'      => 'Email sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal terdiri dari 6 karakter.',
        ];
    }
}
