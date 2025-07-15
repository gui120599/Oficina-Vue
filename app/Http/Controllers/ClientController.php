<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Models\Client;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Recupera todos os clientes, ordenados pelo nome e paginados
        $clients = Client::orderBy('name')
            ->get(); // Paginação de 10 clientes por página

        // Retorna a view 'Clients/Index' do Vue com os dados dos clientes
        return Inertia::render('client/Index', [
            'clients' => $clients,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Retorna a view 'Clients/Create' do Vue
        return Inertia::render('client/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClientRequest $request)
    {
        // Os dados já foram validados pela StoreClientRequest
        $data = $request->validated();

        // Lógica para upload da imagem
        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profile-photos', 'public'); // Armazena em storage/app/public/profile-photos
            $data['profile_photo_path'] = $path; // Salva o caminho no array de dados
        }

        // Cria o novo cliente no banco de dados
        Client::create($data);

        // Redireciona de volta para a lista de clientes com uma mensagem de sucesso
        return redirect()->route('clients.index')
            ->with('success', 'Cliente cadastrado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        // Retorna a view 'Clients/Show' do Vue com os dados do cliente específico
        return Inertia::render('Clients/Show', [
            'client' => $client,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        // Retorna a view 'Clients/Edit' do Vue com os dados do cliente para edição
        return Inertia::render('Clients/Edit', [
            'client' => $client,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClientRequest $request, Client $client)
    {
        // Os dados já foram validados pela UpdateClientRequest
        $data = $request->validated();

        // Lógica para upload/atualização da imagem
        if ($request->hasFile('profile_photo')) {
            // Se já existir uma foto, a deleta para evitar lixo
            if ($client->profile_photo_path) {
                Storage::disk('public')->delete($client->profile_photo_path);
            }
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $data['profile_photo_path'] = $path;
        } elseif (isset($data['profile_photo_remove']) && $data['profile_photo_remove']) {
            // Se o usuário marcou para remover a foto existente
            if ($client->profile_photo_path) {
                Storage::disk('public')->delete($client->profile_photo_path);
            }
            $data['profile_photo_path'] = null; // Define o caminho como nulo no BD
        } else {
            // Se não tem nova foto e não marcou para remover, mantém a existente
            unset($data['profile_photo_path']); // Remove do array de dados para não sobrescrever
        }

        // Atualiza o cliente no banco de dados
        $client->update($data);

        // Redireciona de volta para a lista de clientes com uma mensagem de sucesso
        return redirect()->route('clients.index')
            ->with('success', 'Cliente atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        // softDelete o cliente
        $client->delete();

        // Redireciona de volta para a lista de clientes com uma mensagem de sucesso
        return redirect()->route('clients.index')
            ->with('success', 'Cliente deletado com sucesso!');
    }
}
