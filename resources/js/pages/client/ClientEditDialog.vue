<script setup>
import { ref, watch } from 'vue'; // Importar watch
import { useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';

// Shadcn UI Components
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import InputError from '@/components/InputError.vue';
import { Checkbox } from '@/components/ui/checkbox'; // Para a opção de remover foto

import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';

const props = defineProps({
    client: { // Recebe o objeto cliente para edição
        type: Object,
        required: true,
    },
});

const isDialogOpen = ref(false);

const form = useForm({
    _method: 'put', // Importante para o Laravel interpretar como PUT
    name: props.client.name,
    email: props.client.email,
    phone: props.client.phone,
    cpf_cnpj: props.client.cpf_cnpj,
    address: props.client.address,
    profile_photo: null, // Para o novo arquivo de imagem
    profile_photo_path: props.client.profile_photo_path, // Caminho da foto existente
    profile_photo_remove: false, // Flag para indicar remoção da foto existente
});

// Observar mudanças na prop 'client' para resetar o formulário
// Isso é útil se o mesmo dialog for reutilizado para diferentes clientes sem recarregar a página.
watch(() => props.client, (newClient) => {
    form.name = newClient.name;
    form.email = newClient.email;
    form.phone = newClient.phone;
    form.cpf_cnpj = newClient.cpf_cnpj;
    form.address = newClient.address;
    form.profile_photo = null; // Limpa a nova foto selecionada
    form.profile_photo_path = newClient.profile_photo_path; // Atualiza caminho da foto existente
    form.profile_photo_remove = false; // Reseta flag de remoção
    form.clearErrors(); // Limpa quaisquer erros anteriores
}, { deep: true });


const handleSubmit = () => {
    // Usamos form.post e _method: 'put' para lidar com upload de arquivos
    // Inertia.js requer que envios com arquivos usem o método POST, e o _method spoofing para PUT/PATCH
    form.post(route('clients.update', props.client.id), {
        forceFormData: true, // Necessário para enviar arquivos
        onSuccess: () => {
            toast.success('Client updated successfully!');
            // Não resetamos o form para manter os dados atualizados, mas limpamos erros e foto nova
            form.profile_photo = null;
            form.profile_photo_remove = false;
            form.clearErrors();
            isDialogOpen.value = false; // Fecha o dialog
        },
        onError: (errors) => {
            console.error('Validation errors:', errors);
            toast.error('Failed to update client. Please check the form.');
        },
    });
};

const handleFileChange = (event) => {
    form.profile_photo = event.target.files[0];
    if (form.profile_photo) {
        form.profile_photo_remove = false; // Se uma nova foto for selecionada, desmarca a remoção
    }
};

const handleRemovePhotoChange = (checked) => {
    form.profile_photo_remove = checked;
    if (checked) {
        form.profile_photo = null; // Se marcou para remover, limpa qualquer nova foto selecionada
    }
};

// Expose the dialog state for parent component to control
defineExpose({
    isDialogOpen,
    form
});
</script>

<template>
    <Dialog v-model:open="isDialogOpen">
        <DialogTrigger as-child>
            <Button variant="default">Edit</Button>
        </DialogTrigger>
        <DialogContent class="lg:min-w-[800px] max-h-[90vh] overflow-y-auto">
            <DialogHeader>
                <DialogTitle>Edit Client</DialogTitle>
                <DialogDescription>Atualize os dados do cliente.</DialogDescription>
            </DialogHeader>
            <Card>
                <CardContent class="space-y-3">
                    <form class="space-y-6" @submit.prevent="handleSubmit">
                        <div class="grid w-full gap-2">
                            <Label for="name">Name</Label>
                            <Input id="name" autocomplete="off" v-model="form.name" />
                            <InputError :message="form.errors.name" />
                        </div>
                        <div class="grid w-full gap-2">
                            <Label for="cpf_cnpj">CPF/CNPJ</Label>
                            <Input id="cpf_cnpj" autocomplete="off" v-model="form.cpf_cnpj" />
                            <InputError :message="form.errors.cpf_cnpj" />
                        </div>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div class="grid w-full gap-2">
                                <Label for="phone">Phone</Label>
                                <Input type="tel" autocomplete="off" id="phone" v-model="form.phone" />
                                <InputError :message="form.errors.phone" />
                            </div>
                            <div class="grid w-full gap-2">
                                <Label for="email">Email</Label>
                                <Input type="email" autocomplete="off" id="email" v-model="form.email" />
                                <InputError :message="form.errors.email" />
                            </div>
                        </div>
                        <div class="grid w-full gap-2">
                            <Label for="address">Address</Label>
                            <Textarea id="address" autocomplete="off" v-model="form.address" />
                            <InputError :message="form.errors.address" />
                        </div>

                        <div class="grid w-full gap-2">
                            <Label>Profile Photo</Label>
                            <div v-if="form.profile_photo_path && !form.profile_photo_remove" class="mb-2">
                                <img :src="`/storage/${form.profile_photo_path}`" alt="Current Profile Photo" class="w-24 h-24 rounded-full object-cover">
                            </div>
                            <Input id="profile_photo" type="file" @change="handleFileChange" />
                            <InputError :message="form.errors.profile_photo" />
                            <div class="flex items-center space-x-2 mt-2">
                                <Checkbox id="remove_photo" :checked="form.profile_photo_remove" @update:checked="handleRemovePhotoChange" />
                                <Label for="remove_photo">Remove current photo</Label>
                            </div>
                        </div>

                        <div class="flex justify-end items-center gap-4">
                            <Button type="button" variant="outline" @click="isDialogOpen = false">Cancel</Button>
                            <Button type="submit" :disabled="form.processing">
                                {{ form.processing ? 'Updating...' : 'Update Client' }}
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </DialogContent>
    </Dialog>
</template>