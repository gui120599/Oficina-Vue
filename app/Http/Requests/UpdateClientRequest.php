<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClientRequest extends FormRequest
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
        // Pega o ID do cliente da rota. Assumimos que o parâmetro da rota é 'client'.
        // Ex: /clients/{client}
        $clientId = $this->route('client');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'nullable',
                'string',
                'email',
                'max:255',
                Rule::unique('clients', 'email')->ignore($clientId), // Ignora o cliente atual
            ],
            'phone' => ['nullable', 'string', 'max:20'],
            'cpf_cnpj' => [
                'nullable',
                'string',
                'max:18',
                Rule::unique('clients', 'cpf_cnpj')->ignore($clientId), // Ignora o cliente atual
            ],
            'address' => ['nullable', 'string'],
            'profile_photo' => ['nullable', 'image', 'max:2048'], // Regras para a imagem: opcional, deve ser imagem, max 2MB
        ];
    }
    
    /**
     * Get custom messages for validation errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => 'O nome é obrigatório.',
            'name.string' => 'O nome deve ser um texto.',
            'name.max' => 'O nome não pode ter mais de :max caracteres.',
            'email.email' => 'O e-mail deve ser um endereço de e-mail válido.',
            'email.unique' => 'Este e-mail já está em uso por outro cliente.',
            'cpf_cnpj.unique' => 'Este CPF/CNPJ já está cadastrado para outro cliente.',
            'profile_photo.image' => 'O arquivo deve ser uma imagem.',
            'profile_photo.max' => 'A imagem não pode ter mais de :max kilobytes.',
        ];
    }
}
