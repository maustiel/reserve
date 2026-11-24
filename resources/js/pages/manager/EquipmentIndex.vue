<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

interface Category {
    id: number;
    name: string;
}

interface EquipmentItem {
    id: number;
    inventory_code: string;
    category_id: number;
    name: string;
    condition: string;
    location: string;
    default_accessories: string | null;
    notes: string | null;
    category: Category;
    open_loan: { id: number; user: { name: string } } | null;
}

const props = defineProps<{
    equipment: EquipmentItem[];
    categories: Category[];
    filters: { category_id: number | string; condition: string };
}>();

const conditionLabels: Record<string, string> = {
    good: 'Bon',
    worn: 'Usé',
    damaged: 'Abîmé',
    out_of_service: 'Hors service',
};

const categoryFilter = ref<string | number>(props.filters.category_id);
const conditionFilter = ref<string>(props.filters.condition);
const editingId = ref<number | null>(null);

const form = useForm({
    inventory_code: '',
    category_id: props.categories[0]?.id ?? '',
    name: '',
    condition: 'good',
    location: 'A.04',
    default_accessories: '',
    notes: '',
});

function applyFilters(): void {
    router.get(
        '/manager/equipment',
        {
            category_id: categoryFilter.value || undefined,
            condition: conditionFilter.value || undefined,
        },
        { preserveState: true, replace: true },
    );
}

function startEdit(item: EquipmentItem): void {
    editingId.value = item.id;
    form.inventory_code = item.inventory_code;
    form.category_id = item.category_id;
    form.name = item.name;
    form.condition = item.condition;
    form.location = item.location;
    form.default_accessories = item.default_accessories ?? '';
    form.notes = item.notes ?? '';
}

function resetForm(): void {
    editingId.value = null;
    form.reset();
}

function submit(): void {
    if (editingId.value !== null) {
        form.put(`/manager/equipment/${editingId.value}`, {
            onSuccess: () => resetForm(),
        });
    } else {
        form.post('/manager/equipment', {
            onSuccess: () => resetForm(),
        });
    }
}

function markOutOfService(item: EquipmentItem): void {
    router.put(`/manager/equipment/${item.id}`, {
        inventory_code: item.inventory_code,
        category_id: item.category_id,
        name: item.name,
        condition: 'out_of_service',
        location: item.location,
        default_accessories: item.default_accessories,
        notes: item.notes,
    });
}
</script>

<template>
    <Head title="Inventaire de la réserve" />

    <div class="mx-auto max-w-6xl space-y-8 p-6">
        <h1 class="text-2xl font-bold">Inventaire de la réserve</h1>

        <!-- Formulaire Ajout / Modification -->
        <form
            class="grid grid-cols-1 gap-4 rounded border p-4 md:grid-cols-3"
            @submit.prevent="submit"
        >
            <div>
                <label class="block text-sm font-medium">N° inventaire</label>
                <input
                    v-model="form.inventory_code"
                    type="text"
                    placeholder="Ex: CAM-06"
                    class="mt-1 w-full rounded border p-2"
                    required
                />
                <p
                    v-if="form.errors.inventory_code"
                    class="text-sm text-red-600"
                >
                    {{ form.errors.inventory_code }}
                </p>
            </div>

            <div>
                <label class="block text-sm font-medium">Catégorie</label>
                <select
                    v-model="form.category_id"
                    class="mt-1 w-full rounded border p-2"
                    required
                >
                    <option
                        v-for="cat in categories"
                        :key="cat.id"
                        :value="cat.id"
                    >
                        {{ cat.name }}
                    </option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium">Désignation</label>
                <input
                    v-model="form.name"
                    type="text"
                    placeholder="Ex: Sony HDR-CX405"
                    class="mt-1 w-full rounded border p-2"
                    required
                />
            </div>

            <div>
                <label class="block text-sm font-medium">État</label>
                <select
                    v-model="form.condition"
                    class="mt-1 w-full rounded border p-2"
                >
                    <option
                        v-for="(label, key) in conditionLabels"
                        :key="key"
                        :value="key"
                    >
                        {{ label }}
                    </option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium">Local</label>
                <input
                    v-model="form.location"
                    type="text"
                    class="mt-1 w-full rounded border p-2"
                    required
                />
            </div>

            <div>
                <label class="block text-sm font-medium">Remarques</label>
                <input
                    v-model="form.notes"
                    type="text"
                    class="mt-1 w-full rounded border p-2"
                />
            </div>

            <div class="flex gap-2 md:col-span-3">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="rounded bg-black px-4 py-2 text-white"
                >
                    {{
                        editingId
                            ? 'Enregistrer la modification'
                            : 'Ajouter un appareil'
                    }}
                </button>
                <button
                    v-if="editingId"
                    type="button"
                    class="rounded border px-4 py-2"
                    @click="resetForm"
                >
                    Annuler
                </button>
            </div>
        </form>

        <!-- Filtres -->
        <div class="flex flex-wrap gap-4">
            <select
                v-model="categoryFilter"
                class="rounded border p-2"
                @change="applyFilters"
            >
                <option value="">Toutes les catégories</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                    {{ cat.name }}
                </option>
            </select>

            <select
                v-model="conditionFilter"
                class="rounded border p-2"
                @change="applyFilters"
            >
                <option value="">Tous les états</option>
                <option
                    v-for="(label, key) in conditionLabels"
                    :key="key"
                    :value="key"
                >
                    {{ label }}
                </option>
            </select>
        </div>

        <!-- Tableau inventaire -->
        <table class="w-full border-collapse border text-left text-sm">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border p-2">Code</th>
                    <th class="border p-2">Catégorie</th>
                    <th class="border p-2">Désignation</th>
                    <th class="border p-2">État</th>
                    <th class="border p-2">Local</th>
                    <th class="border p-2">Statut prêt</th>
                    <th class="border p-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in equipment" :key="item.id">
                    <td class="border p-2 font-mono font-semibold">
                        {{ item.inventory_code }}
                    </td>
                    <td class="border p-2">{{ item.category.name }}</td>
                    <td class="border p-2">{{ item.name }}</td>
                    <td class="border p-2">
                        {{ conditionLabels[item.condition] ?? item.condition }}
                    </td>
                    <td class="border p-2">{{ item.location }}</td>
                    <td class="border p-2">
                        <span v-if="item.open_loan"
                            >Prêté à {{ item.open_loan.user.name }}</span
                        >
                        <span
                            v-else-if="
                                item.condition === 'out_of_service' ||
                                item.condition === 'damaged'
                            "
                            >Indisponible</span
                        >
                        <span v-else>Disponible</span>
                    </td>
                    <td class="border p-2">
                        <div class="flex gap-2">
                            <button
                                type="button"
                                class="underline"
                                @click="startEdit(item)"
                            >
                                Modifier
                            </button>
                            <button
                                v-if="item.condition !== 'out_of_service'"
                                type="button"
                                class="text-red-600 underline"
                                @click="markOutOfService(item)"
                            >
                                Hors service
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
