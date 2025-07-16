<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';

// Shadcn UI Components
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import InputError from '@/components/InputError.vue'; // Assumindo que você tem um componente InputError
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';

const isDialogOpen = ref(false); // Estado para controlar a abertura/fechamento do dialog

const form = useForm({
    name: '',
    email: '',
    phone: '',
    cpf_cnpj: '',
    address: '',
    profile_photo: null, // Para o arquivo de imagem
});

const handleSubmit = () => {
    form.post(route('clients.store'), {
        forceFormData: true, // Importante para enviar arquivos (profile_photo)
        onSuccess: () => {
            toast.success('Client created successfully!');
            form.reset(); // Limpa o formulário após o sucesso
            isDialogOpen.value = false; // Fecha o dialog
        },
        onError: (errors) => {
            // Os erros são automaticamente exibidos pelos componentes InputError
            console.error('Validation errors:', errors);
            toast.error('Failed to create client. Please check the form.');
        },
    });
};

// Função para lidar com a seleção do arquivo de imagem
const handleFileChange = (event) => {
    form.profile_photo = event.target.files[0];
};

// Expose the dialog state and form for parent component if needed (e.g., to open programmatically)
defineExpose({
    isDialogOpen,
    form
});
</script>

<template>
    <Dialog v-model:open="isDialogOpen">
        <DialogTrigger as-child>
            <Button variant="outline">Add Client</Button> <!-- Botão que abre o dialog -->
        </DialogTrigger>
        <DialogContent class="lg:min-w-[800px] max-h-[90vh] overflow-y-auto">
            <!-- Ajuste o min-w conforme necessário e adicione scroll -->
            <DialogHeader>
                <DialogTitle>New Client</DialogTitle>
                <DialogDescription>Insira os dados do novo cliente.</DialogDescription>
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

                        <!-- Campo para upload da foto de perfil -->
                        <div class="grid w-full gap-2">
                            <Label for="profile_photo">Profile Photo</Label>
                            <Input id="profile_photo" type="file" @change="handleFileChange" />
                            <InputError :message="form.errors.profile_photo" />
                        </div>

                        <div class="flex justify-end items-center gap-4"> <!-- Ajuste para alinhar botões à direita -->
                            <Button type="button" variant="outline" @click="isDialogOpen = false">Cancel</Button>
                            <Button type="submit" :disabled="form.processing">
                                {{ form.processing ? 'Saving...' : 'Save Client' }}
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </DialogContent>
    </Dialog>
</template>