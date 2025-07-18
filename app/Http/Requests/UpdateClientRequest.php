<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule; // <-- Importar a classe Rule

class UpdateClientRequest extends FormRequest
{
    // ... (authorize() e outros métodos)

    public function rules(): array
    {
        $clientId = $this->route('client'); // Pega o ID do cliente da rota

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'nullable',
                'string',
                'email',
                'max:255',
                Rule::unique('clients', 'email')
                    ->ignore($clientId) // Ignora o registro atual
                    ->where(fn($query) => $query->whereNotNull('email')), // Permite múltiplos NULLs
            ],
            'phone' => ['nullable', 'string', 'max:11'],
            'cpf_cnpj' => [
                'nullable',
                'string',
                'max:14',
                Rule::unique('clients', 'cpf_cnpj')
                    ->ignore($clientId) // Ignora o registro atual
                    ->where(fn($query) => $query->whereNotNull('cpf_cnpj')), // Permite múltiplos NULLs
            ],
            'address' => ['nullable', 'string'],
            'profile_photo' => ['nullable', 'image', 'max:2048'],
            'profile_photo_remove' => ['boolean'], // Valide este campo também para a lógica de remoção
        ];
    }

    /**
     * Prepare the data for validation.
     * Este método é executado antes das regras de validação.
     */
    protected function prepareForValidation(): void
    {
        // Limpar máscaras
        $phone = preg_replace('/[^0-9]/', '', $this->phone);
        $cpf_cnpj = preg_replace('/[^0-9]/', '', $this->cpf_cnpj);

        // Converter strings vazias (após a limpeza) para NULL
        $email = $this->email === '' ? null : $this->email; // Garante que e-mail vazio seja null
        $phone = $phone === '' ? null : $phone;
        $cpf_cnpj = $cpf_cnpj === '' ? null : $cpf_cnpj;

        $this->merge([
            'email' => $email,
            'phone' => $phone,
            'cpf_cnpj' => $cpf_cnpj,
        ]);
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
            'email.unique' => 'Este e-mail já está em uso.',
            'cpf_cnpj.unique' => 'Este CPF/CNPJ já está cadastrado.',
            'profile_photo.image' => 'O arquivo deve ser uma imagem.',
            'profile_photo.max' => 'A imagem não pode ter mais de :max kilobytes.',
        ];
    }
}