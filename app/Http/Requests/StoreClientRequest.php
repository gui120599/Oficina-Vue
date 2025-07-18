<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule; // <-- Importar a classe Rule

class StoreClientRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'nullable',
                'string',
                'email',
                'max:255',
                // Regra unique que só verifica unicidade para valores NÃO NULOS
                Rule::unique('clients', 'email')->where(fn($query) => $query->whereNotNull('email')),
            ],
            // Ajuste o max para o tamanho sem máscara
            'phone' => ['nullable', 'string', 'max:11'], // Telefone sem máscara (ex: 11 dígitos)
            'cpf_cnpj' => [
                'nullable',
                'string',
                'max:14', // CPF (11) / CNPJ (14) sem máscara
                // Regra unique que só verifica unicidade para valores NÃO NULOS
                Rule::unique('clients', 'cpf_cnpj')->where(fn($query) => $query->whereNotNull('cpf_cnpj')),
            ],
            'address' => ['nullable', 'string'],
            'profile_photo' => ['nullable', 'image', 'max:2048'],
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