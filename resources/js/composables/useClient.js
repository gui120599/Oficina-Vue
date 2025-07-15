// resources/js/composables/useClient.js
import { router } from '@inertiajs/vue3'; // Já usa 'router'
import { toast } from 'vue-sonner';

export function deleteClient(clientId) { // Renomeado para deleteClient e recebe clientId
    if (confirm("Are you sure you want to delete this client?")) { // Mensagem mais específica
        router.delete(route('clients.destroy', clientId), { // Rotas e parâmetros para clients
            preserveScroll: true,
            onSuccess: () => toast.success('Client deleted successfully.') // Mensagem de sucesso para cliente
        });
    }
}