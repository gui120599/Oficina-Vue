<script setup lang="js">
import AppLayout from '@/layouts/AppLayout.vue'; // Verifique se o caminho do AppLayout está correto (Layouts ou layouts)
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/components/ui/table'; // Verifique se o caminho é Components ou components
import { Head, Link } from '@inertiajs/vue3'; // Importe usePage para acessar flash messages
import { Button, buttonVariants } from '@/components/ui/button'; // Verifique se o caminho é Components ou components
// Importar a função deleteClient do seu composable
import { deleteClient } from '@/composables/useClient'; // <--- AQUI!
import ClientCreateDialog from './ClientCreateDialog.vue'; // Importe o novo componente de dialog
import ClientEditDialog from './ClientEditDialog.vue'; // Importe o novo componente de dialog de edição
// Importe as novas funções de formatação
import { formatCpfCnpj, formatPhone } from '@/helpers/formatters'; // <--- Nova importação aqui!

defineProps({
    // A prop agora é 'clients' e terá a estrutura de paginação do Laravel
    clients: {
        type: Object, // Laravel paginate retorna um objeto com 'data', 'links', etc.
        required: true
    }
})

const breadcrumbs = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Clients', // Novo breadcrumb para Clientes
        href: '/clients',
    },
];

</script>

<template>

    <Head title="Clients" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
            <div class="mt-3 flex justify-between items-center">
                <ClientCreateDialog />
            </div>
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Photo</TableHead>
                        <TableHead>Name</TableHead>
                        <TableHead>Email</TableHead>
                        <TableHead>Phone</TableHead>
                        <TableHead>CPF/CNPJ</TableHead>
                        <TableHead>Address</TableHead>
                        <TableHead class="w-[120px]">Action</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="client in clients" :key="client.id">
                        <TableCell>
                            <img v-if="client.profile_photo_path" :src="`/storage/${client.profile_photo_path}`"
                                alt="Profile Photo" class="w-10 h-10 rounded-full object-cover" />
                            <div v-else
                                class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 text-xs">
                                No Photo</div>
                        </TableCell>
                        <TableCell>{{ client.name }}</TableCell>
                        <TableCell>{{ client.email || 'N/A' }}</TableCell>
                        <TableCell>
                            {{ formatPhone(client.phone) }}
                        </TableCell>
                        <TableCell>
                            {{ formatCpfCnpj(client.cpf_cnpj) }}
                        </TableCell>
                        <TableCell>{{ client.cpf_cnpj || 'N/A' }}</TableCell>
                        <TableCell class="max-w-[200px] truncate">{{ client.address || 'N/A' }}</TableCell>
                        <TableCell class="space-x-2">
                            <ClientEditDialog :client="client" />
                            <Button variant="destructive" @click="deleteClient(client.id)">Delete</Button>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>


        </div>
    </AppLayout>
</template>