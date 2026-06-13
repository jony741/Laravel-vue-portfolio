<script setup lang="ts">

import { Head, router, usePage } from '@inertiajs/vue3';
import { Pencil, Trash2, Plus, ExternalLink } from '@lucide/vue';
import { ref } from 'vue';
import { toast } from 'vue-sonner';
import Heading from '@/components/Heading.vue';

import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Technology',
                href: '/admin/technology'
            }
        ]
    }
});


const page = usePage();
const technologies = ref(page.props.technologies || []);
const showModal = ref(false);
const isEditing = ref(false);
const selectedTechnology = ref(null);
const isLoading = ref(false);
const formErrors = ref({});


import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

// Categories options
const categories = [
    'Backend',
    'Frontend',
    'AI',
    'Devops'
];


// Form data
const formData = ref({
    name: '',
    icon_slug: '',
    category: '',
    color_hex: '',
    icon_link: ''
});

// Reset form
const resetForm = () => {
    formData.value = {
        name: '',
        icon_slug: '',
        category: '',
        color_hex: '',
        icon_link: ''
    };
    isEditing.value = false;
    selectedTechnology.value = null;
};


// Open modal for add
const openAddModal = () => {
    resetForm();
    showModal.value = true;
};

// Open modal for edit
const openEditModal = (technology) => {
    isEditing.value = true;
    selectedTechnology.value = technology;
    formData.value = {
        name: technology.name,
        icon_slug: technology.icon_slug,
        category: technology.category,
        color_hex: technology.color_hex,
        icon_link: technology.icon_link
    };
    showModal.value = true;
};

// Delete project
const deleteTechnology = (technology) => {
    if (confirm(`Are you sure you want to delete "${technology.name}"?`)) {
        router.delete(`/admin/technologies/${technology.id}`, {
            onSuccess: () => {
                // Success
                toast.success('Technology deleted successfully.');
            },
            onError: (errors) => {
                console.error('Delete error:', errors);
                alert('Failed to delete project');
            }
        });
    }
};

// Submit form
const submitForm = async () => {
    isLoading.value = true;
    formErrors.value = {};

    try {
        if (isEditing.value) {
            router.put(`/admin/technologies/${selectedTechnology.value.id}`, formData.value, {
                onSuccess: () => {
                    showModal.value = false;
                    resetForm();
                    toast.success('Technology updated successfully.');
                },
                onError: (errors) => {
                    if (errors.response?.data?.errors) {
                        formErrors.value = errors.response.data.errors;
                    }
                }
            });
        } else {
            router.post('/admin/technologies', formData.value, {
                onSuccess: () => {
                    showModal.value = false;
                    resetForm();
                    toast.success('Technology added successfully.');
                },
                onError: (errors) => {
                    if (errors.response?.data?.errors) {
                        formErrors.value = errors.response.data.errors;
                    }
                }
            });
        }
    } catch (error) {
        console.error('Form submission error:', error);
    } finally {
        isLoading.value = false;
    }
};


// Close modal
const closeModal = () => {
    showModal.value = false;
    resetForm();
};

</script>

<template>
    <Head title="Technology Management" />

    <div class="container mx-auto py-8 space-y-6 px-4">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <Heading
                variant="small"
                title="Technology"
                description="Manage your technology portfolio"
            />

            <Button @click="openAddModal" class="gap-2">
                <Plus class="h-4 w-4" />
                Add New Technology
            </Button>
        </div>


        <!-- Table -->
        <div class="bg-white rounded-lg border shadow-sm overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Icon slug</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Icon Links</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="(technology, index) in technologies" :key="technology.id" class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ index + 1 }}
                    </td>

                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">{{ technology.name }}</div>
                    </td>

                    <td class="px-6 py-4 whitespace-nowrap">
                            <span :class="['px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full']">
                                {{ technology.category }}
                            </span>
                    </td>

                    <td>
                        <div class="text-sm font-medium text-gray-900">{{ technology.icon_slug }}</div>
                    </td>

                    <td>
                        <div class="text-sm font-medium text-gray-900">{{ technology.icon_link }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex justify-end gap-2">
                            <button
                                @click="openEditModal(technology)"
                                class="text-indigo-600 hover:text-indigo-900 transition-colors"
                                title="Edit"
                            >
                                <Pencil class="h-4 w-4" />
                            </button>
                            <button
                                @click="deleteTechnology(technology)"
                                class="text-red-600 hover:text-red-900 transition-colors"
                                title="Delete"
                            >
                                <Trash2 class="h-4 w-4" />
                            </button>
                        </div>
                    </td>
                </tr>

                </tbody>
            </table>
        </div>


        <!-- Simple Modal -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center">
            <!-- Backdrop -->
            <div class="fixed inset-0 modal-backdrop"  @click="closeModal"></div>

            <!-- Modal Content -->
            <div class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
                <form @submit.prevent="submitForm">
                    <!-- Modal Header -->
                    <div class="sticky top-0 bg-white border-b px-6 py-4 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">
                            {{ isEditing ? 'Edit Technology' : 'Add New Technology' }}
                        </h3>
                        <button
                            type="button"
                            @click="closeModal"
                            class="text-gray-400 hover:text-gray-500"
                        >
                            <X class="h-5 w-5" />
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="px-6 py-4 space-y-4">

                        <!-- Project Name -->
                        <div class="grid gap-2">
                            <Label for="name">Name *</Label>
                            <Input
                                id="name"
                                v-model="formData.name"
                                type="text"
                                required
                                placeholder="Enter technology name"
                            />
                            <InputError :message="formErrors.name?.[0]" />
                        </div>

                        <!-- Category -->
                        <div class="grid gap-2">
                            <Label for="category">Category *</Label>
                            <Select v-model="formData.category" required>
                                <SelectTrigger class="w-full">
                                    <SelectValue placeholder="Select a category" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem
                                        v-for="category in categories"
                                        :key="category"
                                        :value="category"
                                    >
                                        {{ category }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError :message="formErrors.category?.[0]" />
                        </div>

                        <!-- Description -->
                        <div class="grid gap-2">
                            <Label for="icon-slug">Icon Slug *</Label>
                            <Input
                                id="icon_slug"
                                v-model="formData.icon_slug"
                                type="text"
                                placeholder="icon_slug"
                            />
                            <InputError :message="formErrors.icon_slug?.[0]" />
                        </div>

                        <!-- URLs -->

                        <div class="grid gap-2">
                            <Label for="icon_link">Icon Link</Label>
                            <Input
                                id="icon_link"
                                v-model="formData.icon_link"
                                type="text"
                                placeholder="icon_link"
                            />
                            <InputError :message="formErrors.icon_link?.[0]" />
                        </div>


                    </div>

                    <!-- Modal Footer -->
                    <div class="sticky bottom-0 bg-gray-50 border-t px-6 py-4 flex justify-end gap-3">
                        <Button type="button" variant="outline" @click="closeModal">
                            Cancel
                        </Button>
                        <Button type="submit" :disabled="isLoading">
                            {{ isLoading ? 'Saving...' : (isEditing ? 'Update' : 'Create') }}
                        </Button>
                    </div>
                </form>
            </div>
        </div>


    </div>

</template>

<style scoped>
.modal-backdrop{
    background-color: rgba(0, 0, 0, 0.5);
}

</style>