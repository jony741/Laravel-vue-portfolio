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

import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';




defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Projects',
                href: '/admin/projects',
            },
        ],
    },
});


const page = usePage();
const projects = ref(page.props.projects || []);
const showModal = ref(false);
const isEditing = ref(false);
const selectedProject = ref(null);
const isLoading = ref(false);
const formErrors = ref({});



// Form data
const formData = ref({
    profile_id: '',
    name: 'Default',
    description: '',
    category: '',
    live_url: '',
    repo_url: '',
    thumbnail_url: '',
    is_featured: false,
    sort_order: 0,
});

// Reset form
const resetForm = () => {
    formData.value = {
        profile_id: '',
        name: '',
        description: '',
        category: '',
        live_url: '',
        repo_url: '',
        thumbnail_url: '',
        is_featured: false,
        sort_order: 0,
    };
    isEditing.value = false;
    selectedProject.value = null;
};

// Open modal for add
const openAddModal = () => {
    resetForm();
    showModal.value = true;
};

// Open modal for edit
const openEditModal = (project) => {
    isEditing.value = true;
    selectedProject.value = project;
    formData.value = {
        profile_id: project.profile_id,
        name: project.name,
        description: project.description,
        category: project.category,
        live_url: project.live_url || '',
        repo_url: project.repo_url || '',
        thumbnail_url: project.thumbnail_url || '',
        is_featured: project.is_featured,
        sort_order: project.sort_order,
    };
    showModal.value = true;
};


// Get category badge color
const getCategoryBadgeClass = (category) => {
    const classes = {
        'Web Development': 'bg-blue-100 text-blue-800',
        'Mobile App': 'bg-green-100 text-green-800',
        'E-commerce': 'bg-purple-100 text-purple-800',
        'Portfolio': 'bg-pink-100 text-pink-800',
        'CMS': 'bg-yellow-100 text-yellow-800',
        'API Development': 'bg-indigo-100 text-indigo-800',
        'Other': 'bg-gray-100 text-gray-800',
    };
    return classes[category] || 'bg-gray-100 text-gray-800';
};


// Format date
const formatDate = (date) => {
    if (!date) return 'N/A';
    return new Date(date).toLocaleDateString();
};

// Delete project
const deleteProject = (project) => {
    if (confirm(`Are you sure you want to delete "${project.name}"?`)) {
        router.delete(`/admin/projects/${project.id}`, {
            onSuccess: () => {
                // Success
                toast.success('Project deleted successfully.')
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
             router.put(`/admin/projects/${selectedProject.value.id}`, formData.value, {
                onSuccess: () => {
                    showModal.value = false;
                    resetForm();
                },
                onError: (errors) => {
                    if (errors.response?.data?.errors) {
                        formErrors.value = errors.response.data.errors;
                    }
                }
            });
        } else {
            router.post('/admin/projects', formData.value, {
                onSuccess: () => {
                    showModal.value = false;
                    resetForm();
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


// Categories options
const categories = [
    'Web Development',
    'Mobile App',
    'E-commerce',
    'Portfolio',
    'CMS',
    'API Development',
    'Other'
];

// Close modal
const closeModal = () => {
    showModal.value = false;
    resetForm();
};
</script>


<template>
    <Head title="Projects Management" />

    <div class="container mx-auto py-6 space-y-6 px-4">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <Heading
                variant="small"
                title="Projects"
                description="Manage your projects portfolio"
            />

            <Button @click="openAddModal" class="gap-2">
                <Plus class="h-4 w-4" />
                Add New Project
            </Button>
        </div>


        <!-- Projects Table -->
        <div class="bg-white rounded-lg border shadow-sm overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Featured</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Links</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="(project, index) in projects" :key="project.id" class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ project.sort_order || index + 1 }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">{{ project.name }}</div>
                        <div class="text-sm text-gray-500 truncate max-w-md">
                            {{ project.description.substring(0, 80) }}...
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                            <span :class="['px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full', getCategoryBadgeClass(project.category)]">
                                {{ project.category }}
                            </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                            <span :class="['px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full', project.is_featured ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800']">
                                {{ project.is_featured ? 'Featured' : 'No' }}
                            </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <div class="flex gap-3">
                            <a
                                v-if="project.live_url"
                                :href="project.live_url"
                                target="_blank"
                                class="text-blue-600 hover:text-blue-800 transition-colors"
                                title="Live URL"
                            >
                                <ExternalLink class="h-4 w-4" />
                            </a>
                            <a
                                v-if="project.repo_url"
                                :href="project.repo_url"
                                target="_blank"
                                class="text-gray-600 hover:text-gray-800 transition-colors"
                                title="Repository"
                            >
                                <Github class="h-4 w-4" />
                            </a>
                            <span v-if="!project.live_url && !project.repo_url" class="text-gray-400">-</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ formatDate(project.created_at) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex justify-end gap-2">
                            <button
                                @click="openEditModal(project)"
                                class="text-indigo-600 hover:text-indigo-900 transition-colors"
                                title="Edit"
                            >
                                <Pencil class="h-4 w-4" />
                            </button>
                            <button
                                @click="deleteProject(project)"
                                class="text-red-600 hover:text-red-900 transition-colors"
                                title="Delete"
                            >
                                <Trash2 class="h-4 w-4" />
                            </button>
                        </div>
                    </td>
                </tr>

                <!-- Empty state -->
                <tr v-if="projects.length === 0">
                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                        No projects found. Click "Add New Project" to create one.
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <!-- Modal Overlay -->
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
                            {{ isEditing ? 'Edit Project' : 'Add New Project' }}
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
                            <Label for="name">Project Name *</Label>
                            <Input
                                id="name"
                                v-model="formData.name"
                                type="text"
                                required
                                placeholder="Enter project name"
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
                            <Label for="description">Description *</Label>
                            <textarea
                                id="description"
                                v-model="formData.description"
                                rows="4"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                required
                                placeholder="Describe your project..."
                            ></textarea>
                            <InputError :message="formErrors.description?.[0]" />
                        </div>

                        <!-- URLs -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="grid gap-2">
                                <Label for="live_url">Live URL</Label>
                                <Input
                                    id="live_url"
                                    v-model="formData.live_url"
                                    type="url"
                                    placeholder="https://example.com"
                                />
                                <InputError :message="formErrors.live_url?.[0]" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="repo_url">Repository URL</Label>
                                <Input
                                    id="repo_url"
                                    v-model="formData.repo_url"
                                    type="url"
                                    placeholder="https://github.com/..."
                                />
                                <InputError :message="formErrors.repo_url?.[0]" />
                            </div>
                        </div>

                        <!-- Thumbnail URL -->
                        <div class="grid gap-2">
                            <Label for="thumbnail_url">Thumbnail URL</Label>
                            <Input
                                id="thumbnail_url"
                                v-model="formData.thumbnail_url"
                                type="url"
                                placeholder="https://example.com/image.jpg"
                            />
                            <InputError :message="formErrors.thumbnail_url?.[0]" />
                        </div>

                        <!-- Featured and Sort Order -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="flex items-center space-x-2">
                                <Checkbox
                                    id="is_featured"
                                    v-model:checked="formData.is_featured"
                                />
                                <Label for="is_featured" class="mb-0">Featured Project</Label>
                            </div>
                            <div class="grid gap-2">
                                <Label for="sort_order">Sort Order</Label>
                                <Input
                                    id="sort_order"
                                    v-model.number="formData.sort_order"
                                    type="number"
                                    min="0"
                                />
                                <InputError :message="formErrors.sort_order?.[0]" />
                            </div>
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
.modal-backdrop {
    background-color: rgba(0, 0, 0, 0.5);
}
</style>