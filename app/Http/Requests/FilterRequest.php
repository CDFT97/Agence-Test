<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FilterRequest extends FormRequest
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
            'tipo'       => 'required',
            'co_usuario' => 'required',
            'fechaInicio'  => 'required',
            'fechaFin'    => 'required',
        ];
    }

    public function messages()
    {
        return [
            'tipo.required' => 'El tipo de resultado es requerido.',
            'co_usuario.required' => 'Los usuarios son requeridos para consultar.',
            'fechaInicio.required' => 'El fecha de inicio es requerido.',
            'fechaFin.required' => 'El fecha fin es requerido.',
        ];
    }
}
