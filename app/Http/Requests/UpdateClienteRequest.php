<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClienteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255',
            'tipo_documento' => 'nullable|string|in:DUI,NIT,PASAPORTE,OTRO',
            'numero_documento' => 'nullable|string|max:20',
            'nrc' => 'nullable|string|max:20',
            'giro' => 'nullable|string|max:100',
            'telefono' => 'nullable|string|max:15',
            'correo' => 'nullable|email|max:255',
            'direccion' => 'nullable|string|max:500',
            'departamento' => 'nullable|string|max:50',
            'municipio' => 'nullable|string|max:50',
        ];
    }
}
