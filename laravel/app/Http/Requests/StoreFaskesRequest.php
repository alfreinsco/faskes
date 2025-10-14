<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFaskesRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string|max:500',
            'no_telp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'waktu_buka' => 'nullable|date_format:H:i',
            'waktu_tutup' => 'nullable|date_format:H:i|after:waktu_buka',
            'layanan' => 'nullable|array',
            'layanan.*' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'nama.required' => 'Nama faskes harus diisi.',
            'nama.max' => 'Nama faskes maksimal 255 karakter.',
            'alamat.max' => 'Alamat maksimal 500 karakter.',
            'no_telp.max' => 'Nomor telepon maksimal 20 karakter.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 255 karakter.',
            'website.url' => 'Format website tidak valid.',
            'website.max' => 'Website maksimal 255 karakter.',
            'gambar.image' => 'File harus berupa gambar.',
            'gambar.mimes' => 'Gambar harus berformat jpeg, png, jpg, atau gif.',
            'gambar.max' => 'Ukuran gambar maksimal 2MB.',
            'waktu_buka.date_format' => 'Format waktu buka harus HH:MM.',
            'waktu_tutup.date_format' => 'Format waktu tutup harus HH:MM.',
            'waktu_tutup.after' => 'Waktu tutup harus setelah waktu buka.',
            'layanan.array' => 'Layanan harus berupa array.',
            'layanan.*.string' => 'Setiap layanan harus berupa teks.',
            'layanan.*.max' => 'Setiap layanan maksimal 255 karakter.',
            'latitude.numeric' => 'Latitude harus berupa angka.',
            'latitude.between' => 'Latitude harus antara -90 dan 90.',
            'longitude.numeric' => 'Longitude harus berupa angka.',
            'longitude.between' => 'Longitude harus antara -180 dan 180.',
        ];
    }
}
