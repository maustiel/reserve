<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';

interface LoanSummary {
    id: number;
    reference: string;
    status: string;
    due_at: string;
    equipment: { inventory_code: string; name: string };
}

interface Borrower {
    id: number;
    name: string;
    email: string;
    phone: string | null;
    borrower_type: string | null;
    section_or_service: string | null;
    loans_count: number;
    loans: LoanSummary[];
}

defineProps<{
    borrowers: Borrower[];
}>();

const typeLabels: Record<string, string> = {
    student: 'Étudiant',
    staff: 'Personnel / Professeur',
    other: 'Autre',
};

const form = useForm({
    name: '',
    email: '',
    phone: '',
    borrower_type: 'student',
    section_or_service: '',
    password: '',
});

function submit(): void {
    form.post('/manager/borrowers', {
        onSuccess: () => form.reset(),
    });
}
</script>

<template>
    <Head title="Gestion des emprunteurs" />

    <div class="mx-auto max-w-6xl space-y-8 p-6">
        <h1 class="text-2xl font-bold">Emprunteurs</h1>

        <!-- Formulaire création compte emprunteur -->
        <form
            class="grid grid-cols-1 gap-4 rounded border p-4 md:grid-cols-3"
            @submit.prevent="submit"
        >
            <div>
                <label class="block text-sm font-medium">Nom complet</label>
                <input
                    v-model="form.name"
                    type="text"
                    placeholder="Ex: Aylin Demir"
                    class="mt-1 w-full rounded border p-2"
                    required
                />
                <p v-if="form.errors.name" class="text-sm text-red-600">
                    {{ form.errors.name }}
                </p>
            </div>

            <div>
                <label class="block text-sm font-medium">E-mail</label>
                <input
                    v-model="form.email"
                    type="email"
                    class="mt-1 w-full rounded border p-2"
                    required
                />
                <p v-if="form.errors.email" class="text-sm text-red-600">
                    {{ form.errors.email }}
                </p>
            </div>

            <div>
                <label class="block text-sm font-medium">Téléphone</label>
                <input
                    v-model="form.phone"
                    type="text"
                    placeholder="0470/00.00.00"
                    class="mt-1 w-full rounded border p-2"
                    required
                />
                <p v-if="form.errors.phone" class="text-sm text-red-600">
                    {{ form.errors.phone }}
                </p>
            </div>

            <div>
                <label class="block text-sm font-medium">Qualité</label>
                <select
                    v-model="form.borrower_type"
                    class="mt-1 w-full rounded border p-2"
                >
                    <option
                        v-for="(label, key) in typeLabels"
                        :key="key"
                        :value="key"
                    >
                        {{ label }}
                    </option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium"
                    >Section ou service</label
                >
                <input
                    v-model="form.section_or_service"
                    type="text"
                    placeholder="Ex: BES Webdev 2e"
                    class="mt-1 w-full rounded border p-2"
                    required
                />
            </div>

            <div>
                <label class="block text-sm font-medium"
                    >Mot de passe initial</label
                >
                <input
                    v-model="form.password"
                    type="password"
                    class="mt-1 w-full rounded border p-2"
                    required
                />
                <p v-if="form.errors.password" class="text-sm text-red-600">
                    {{ form.errors.password }}
                </p>
            </div>

            <div class="md:col-span-3">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="rounded bg-black px-4 py-2 text-white"
                >
                    Créer le compte emprunteur
                </button>
            </div>
        </form>

        <!-- Liste des emprunteurs et de leurs prêts -->
        <table class="w-full border-collapse border text-left text-sm">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border p-2">Nom</th>
                    <th class="border p-2">Qualité</th>
                    <th class="border p-2">Section / Service</th>
                    <th class="border p-2">Contact</th>
                    <th class="border p-2">
                        Prêts ({{
                            borrowers.reduce(
                                (sum, b) => sum + b.loans_count,
                                0,
                            )
                        }})
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="borrower in borrowers" :key="borrower.id">
                    <td class="border p-2 font-semibold">
                        {{ borrower.name }}
                    </td>
                    <td class="border p-2">
                        {{ typeLabels[borrower.borrower_type ?? ''] ?? '-' }}
                    </td>
                    <td class="border p-2">
                        {{ borrower.section_or_service ?? '-' }}
                    </td>
                    <td class="border p-2">
                        <div>{{ borrower.email }}</div>
                        <div class="text-gray-600">{{ borrower.phone }}</div>
                    </td>
                    <td class="border p-2">
                        <ul
                            v-if="borrower.loans.length > 0"
                            class="list-disc pl-4"
                        >
                            <li v-for="loan in borrower.loans" :key="loan.id">
                                {{ loan.equipment.inventory_code }} ({{
                                    loan.status
                                }})
                            </li>
                        </ul>
                        <span v-else class="text-gray-500">Aucun prêt</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
