<script setup lang="ts">
import { Head, router, usePage } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2, X } from '@lucide/vue';
import { computed, ref } from 'vue';
import { toast } from 'vue-sonner';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

interface Profile {
    id: number;
    full_name: string;
}

interface Technology {
    id: number;
    name: string;
    category: string | null;
    color_hex: string | null;
    icon_slug: string | null;
    icon_link: string | null;
}

interface StackItem {
    id: number;
    profile_id: number;
    technology_id: number;
    proficiency_level: number;
    sort_order: number;
    technology: Technology | null;
}

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Tech Stack', href: '/admin/tech-stack' }],
    },
});

const page = usePage();
const profile = computed(() => (page.props.profile as Profile | null) || null);
const stackItems = computed(() => (page.props.stackItems as StackItem[]) || []);
const technologies = computed(
    () => (page.props.technologies as Technology[]) || [],
);

const showModal = ref(false);
const isEditing = ref(false);
const selectedStackItem = ref<StackItem | null>(null);
const isLoading = ref(false);
const formErrors = ref<Record<string, string>>({});

const formData = ref({
    technology_id: '',
    proficiency_level: 3,
    sort_order: 0,
});

const proficiencyLabels: Record<number, string> = {
    1: 'Beginner',
    2: 'Basic',
    3: 'Intermediate',
    4: 'Advanced',
    5: 'Expert',
};

const resetForm = () => {
    formData.value = {
        technology_id: '',
        proficiency_level: 3,
        sort_order: 0,
    };
    isEditing.value = false;
    selectedStackItem.value = null;
    formErrors.value = {};
};

const openAddModal = () => {
    resetForm();
    showModal.value = true;
};

const openEditModal = (stackItem: StackItem) => {
    isEditing.value = true;
    selectedStackItem.value = stackItem;
    formData.value = {
        technology_id: String(stackItem.technology_id),
        proficiency_level: stackItem.proficiency_level,
        sort_order: stackItem.sort_order,
    };
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    resetForm();
};

const submitForm = () => {
    isLoading.value = true;
    formErrors.value = {};

    const options = {
        onSuccess: () => {
            toast.success(
                isEditing.value
                    ? 'Tech stack item updated.'
                    : 'Tech stack item added.',
            );
            closeModal();
        },
        onError: (errors: Record<string, string>) => {
            formErrors.value = errors;
            toast.error('Please check the form for errors.');
        },
        onFinish: () => {
            isLoading.value = false;
        },
    };

    if (isEditing.value && selectedStackItem.value) {
        router.put(
            `/admin/tech-stack/${selectedStackItem.value.id}`,
            formData.value,
            options,
        );
    } else {
        router.post('/admin/tech-stack', formData.value, options);
    }
};

const deleteStackItem = (stackItem: StackItem) => {
    const technologyName = stackItem.technology?.name ?? 'this item';

    if (
        confirm(
            `Are you sure you want to delete "${technologyName}" from your tech stack?`,
        )
    ) {
        router.delete(`/admin/tech-stack/${stackItem.id}`, {
            onSuccess: () => toast.success('Tech stack item deleted.'),
            onError: () => toast.error('Failed to delete tech stack item.'),
        });
    }
};
</script>

<template>
    <Head title="Tech Stack Management" />

    <div class="container mx-auto space-y-6 px-4 py-6">
        <div class="flex items-center justify-between">
            <Heading
                variant="small"
                title="Tech Stack"
                description="Manage technologies shown in your portfolio stack"
            />

            <Button
                class="gap-2"
                :disabled="!profile || technologies.length === 0"
                @click="openAddModal"
            >
                <Plus class="h-4 w-4" />
                Add Stack Item
            </Button>
        </div>

        <div
            v-if="!profile"
            class="rounded-md border border-yellow-200 bg-yellow-50 px-4 py-3 text-sm text-yellow-800"
        >
            Create your profile first before adding tech stack items.
        </div>

        <div
            v-else-if="technologies.length === 0"
            class="rounded-md border border-yellow-200 bg-yellow-50 px-4 py-3 text-sm text-yellow-800"
        >
            Add technologies first before adding tech stack items.
        </div>

        <div class="overflow-x-auto rounded-lg border bg-white shadow-sm">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                        >
                            #
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                        >
                            Technology
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                        >
                            Category
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                        >
                            Proficiency
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                        >
                            Sort Order
                        </th>
                        <th
                            class="px-6 py-3 text-right text-xs font-medium tracking-wider text-gray-500 uppercase"
                        >
                            Actions
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 bg-white">
                    <tr
                        v-for="(stackItem, index) in stackItems"
                        :key="stackItem.id"
                        class="hover:bg-gray-50"
                    >
                        <td
                            class="px-6 py-4 text-sm whitespace-nowrap text-gray-500"
                        >
                            {{ index + 1 }}
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <span
                                    v-if="stackItem.technology?.color_hex"
                                    class="h-2.5 w-2.5 shrink-0 rounded-full"
                                    :style="{
                                        backgroundColor:
                                            stackItem.technology.color_hex,
                                    }"
                                />
                                <div>
                                    <div
                                        class="text-sm font-medium text-gray-900"
                                    >
                                        {{
                                            stackItem.technology?.name ??
                                            'Unknown technology'
                                        }}
                                    </div>
                                    <div
                                        v-if="stackItem.technology?.icon_slug"
                                        class="text-xs text-gray-500"
                                    >
                                        {{ stackItem.technology.icon_slug }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        <td
                            class="px-6 py-4 text-sm whitespace-nowrap text-gray-500"
                        >
                            {{ stackItem.technology?.category ?? '-' }}
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div
                                    class="h-2 w-24 overflow-hidden rounded-full bg-gray-100"
                                >
                                    <div
                                        class="h-full rounded-full bg-indigo-600"
                                        :style="{
                                            width: `${stackItem.proficiency_level * 20}%`,
                                        }"
                                    />
                                </div>
                                <span class="text-sm text-gray-700">
                                    {{ stackItem.proficiency_level }}/5
                                    {{
                                        proficiencyLabels[
                                            stackItem.proficiency_level
                                        ]
                                    }}
                                </span>
                            </div>
                        </td>

                        <td
                            class="px-6 py-4 text-sm whitespace-nowrap text-gray-500"
                        >
                            {{ stackItem.sort_order }}
                        </td>

                        <td
                            class="px-6 py-4 text-right text-sm font-medium whitespace-nowrap"
                        >
                            <div class="flex justify-end gap-2">
                                <button
                                    class="text-indigo-600 transition-colors hover:text-indigo-900"
                                    title="Edit"
                                    @click="openEditModal(stackItem)"
                                >
                                    <Pencil class="h-4 w-4" />
                                </button>
                                <button
                                    class="text-red-600 transition-colors hover:text-red-900"
                                    title="Delete"
                                    @click="deleteStackItem(stackItem)"
                                >
                                    <Trash2 class="h-4 w-4" />
                                </button>
                            </div>
                        </td>
                    </tr>

                    <tr v-if="stackItems.length === 0">
                        <td
                            colspan="6"
                            class="px-6 py-8 text-center text-gray-500"
                        >
                            No tech stack items found.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div
            v-if="showModal"
            class="fixed inset-0 z-50 flex items-center justify-center"
        >
            <div class="fixed inset-0 bg-black/50" @click="closeModal" />

            <div
                class="relative mx-4 max-h-[90vh] w-full max-w-xl overflow-y-auto rounded-lg bg-white shadow-xl"
            >
                <form @submit.prevent="submitForm">
                    <div
                        class="sticky top-0 z-10 flex items-center justify-between border-b bg-white px-6 py-4"
                    >
                        <h3 class="text-lg font-semibold text-gray-900">
                            {{
                                isEditing ? 'Edit Stack Item' : 'Add Stack Item'
                            }}
                        </h3>
                        <button
                            type="button"
                            class="text-gray-400 hover:text-gray-500"
                            @click="closeModal"
                        >
                            <X class="h-5 w-5" />
                        </button>
                    </div>

                    <div class="space-y-4 px-6 py-4">
                        <InputError :message="formErrors.profile_id" />

                        <div class="grid gap-2">
                            <Label for="technology_id">Technology *</Label>
                            <Select v-model="formData.technology_id" required>
                                <SelectTrigger
                                    id="technology_id"
                                    class="w-full"
                                >
                                    <SelectValue
                                        placeholder="Select a technology"
                                    />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem
                                        v-for="technology in technologies"
                                        :key="technology.id"
                                        :value="String(technology.id)"
                                    >
                                        {{ technology.name
                                        }}{{
                                            technology.category
                                                ? ` - ${technology.category}`
                                                : ''
                                        }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError :message="formErrors.technology_id" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="proficiency_level"
                                >Proficiency Level *</Label
                            >
                            <Select
                                v-model.number="formData.proficiency_level"
                                required
                            >
                                <SelectTrigger
                                    id="proficiency_level"
                                    class="w-full"
                                >
                                    <SelectValue
                                        placeholder="Select proficiency"
                                    />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem
                                        v-for="level in [1, 2, 3, 4, 5]"
                                        :key="level"
                                        :value="level"
                                    >
                                        {{ level }} -
                                        {{ proficiencyLabels[level] }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError
                                :message="formErrors.proficiency_level"
                            />
                        </div>

                        <div class="grid gap-2">
                            <Label for="sort_order">Sort Order *</Label>
                            <Input
                                id="sort_order"
                                v-model.number="formData.sort_order"
                                type="number"
                                min="0"
                                required
                            />
                            <InputError :message="formErrors.sort_order" />
                        </div>
                    </div>

                    <div
                        class="sticky bottom-0 flex justify-end gap-3 border-t bg-gray-50 px-6 py-4"
                    >
                        <Button
                            type="button"
                            variant="outline"
                            @click="closeModal"
                            >Cancel</Button
                        >
                        <Button type="submit" :disabled="isLoading">
                            {{
                                isLoading
                                    ? 'Saving...'
                                    : isEditing
                                      ? 'Update'
                                      : 'Create'
                            }}
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
