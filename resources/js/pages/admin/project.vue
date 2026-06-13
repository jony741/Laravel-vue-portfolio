<script setup lang="ts">
import { Head, router, usePage } from '@inertiajs/vue3';
import { Pencil, Trash2, Plus, ExternalLink, X, } from '@lucide/vue';
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

interface Technology {
    id: number;
    name: string;
    category: string;
    color: string | null;
}

interface Project {
    id: number;
    profile_id: number;
    name: string;
    description: string;
    category: string;
    live_url: string | null;
    repo_url: string | null;
    thumbnail_url: string | null;
    is_featured: boolean;
    sort_order: number;
    created_at: string;
    technology_ids: number[];
}

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Projects', href: '/admin/projects' }],
    },
});

const page = usePage();
const projects     = ref<Project[]>((page.props.projects as Project[]) || []);
const technologies = ref<Record<string, Technology[]>>(
    (page.props.technologies as Record<string, Technology[]>) || {}
);

const showModal        = ref(false);
const isEditing        = ref(false);
const selectedProject  = ref<Project | null>(null);
const isLoading        = ref(false);
const formErrors       = ref<Record<string, string[]>>({});

// Form data — includes technology_ids array
const formData = ref({
    profile_id:     '',
    name:           '',
    description:    '',
    category:       '',
    live_url:       '',
    repo_url:       '',
    thumbnail_url:  '',
    is_featured:    false,
    sort_order:     0,
    technology_ids: [] as number[],
});

const resetForm = () => {
    formData.value = {
        profile_id:     '',
        name:           '',
        description:    '',
        category:       '',
        live_url:       '',
        repo_url:       '',
        thumbnail_url:  '',
        is_featured:    false,
        sort_order:     0,
        technology_ids: [],
    };
    isEditing.value       = false;
    selectedProject.value = null;
    formErrors.value      = {};
};

const openAddModal = () => {
    resetForm();
    showModal.value = true;
};

const openEditModal = (project: Project) => {
    isEditing.value       = true;
    selectedProject.value = project;
    formData.value = {
        profile_id:     String(project.profile_id),
        name:           project.name,
        description:    project.description,
        category:       project.category,
        live_url:       project.live_url || '',
        repo_url:       project.repo_url || '',
        thumbnail_url:  project.thumbnail_url || '',
        is_featured:    project.is_featured,
        sort_order:     project.sort_order,
        technology_ids: [...project.technology_ids], // pre-tick existing techs
    };
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    resetForm();
};

// Toggle a technology checkbox
const toggleTechnology = (techId: number) => {
    const idx = formData.value.technology_ids.indexOf(techId);
    if (idx === -1) {
        formData.value.technology_ids.push(techId);
    } else {
        formData.value.technology_ids.splice(idx, 1);
    }
};

const isTechSelected = (techId: number): boolean => {
    return formData.value.technology_ids.includes(techId);
};

const getCategoryBadgeClass = (category: string) => {
    const classes: Record<string, string> = {
        'Web Development':  'bg-blue-100 text-blue-800',
        'Mobile App':       'bg-green-100 text-green-800',
        'E-commerce':       'bg-purple-100 text-purple-800',
        'Portfolio':        'bg-pink-100 text-pink-800',
        'CMS':              'bg-yellow-100 text-yellow-800',
        'API Development':  'bg-indigo-100 text-indigo-800',
        'Other':            'bg-gray-100 text-gray-800',
    };
    return classes[category] || 'bg-gray-100 text-gray-800';
};

const formatDate = (date: string) => {
    if (!date) return 'N/A';
    return new Date(date).toLocaleDateString();
};

const deleteProject = (project: Project) => {
    if (confirm(`Are you sure you want to delete "${project.name}"?`)) {
        router.delete(`/admin/projects/${project.id}`, {
            onSuccess: () => toast.success('Project deleted successfully.'),
            onError:   () => toast.error('Failed to delete project.'),
        });
    }
};

const submitForm = () => {
    isLoading.value  = true;
    formErrors.value = {};

    const options = {
        onSuccess: () => {
            toast.success(isEditing.value ? 'Project updated!' : 'Project created!');
            closeModal();
        },
        onError: (errors: Record<string, string[]>) => {
            formErrors.value = errors;
            toast.error('Please check the form for errors.');
        },
        onFinish: () => {
            isLoading.value = false;
        },
    };

    if (isEditing.value && selectedProject.value) {
        router.put(`/admin/projects/${selectedProject.value.id}`, formData.value, options);
    } else {
        router.post('/admin/projects', formData.value, options);
    }
};

const categories = [
    'Web Development', 'Mobile App', 'E-commerce',
    'Portfolio', 'CMS', 'API Development', 'Other',
];
</script>

<template>
    <Head title="Projects Management" />

    <div class="container mx-auto space-y-6 px-4 py-6">

        <!-- Header -->
        <div class="flex items-center justify-between">
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
        <div class="overflow-x-auto rounded-lg border bg-white shadow-sm">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">#</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Technologies</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Featured</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Links</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Created</th>
                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                <tr v-for="(project, index) in projects" :key="project.id" class="hover:bg-gray-50">

                    <td class="px-6 py-4 text-sm whitespace-nowrap text-gray-500">
                        {{ project.sort_order || index + 1 }}
                    </td>

                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">{{ project.name }}</div>
                        <div class="max-w-md truncate text-sm text-gray-500">
                            {{ project.description.substring(0, 80) }}...
                        </div>
                    </td>

                    <td class="px-6 py-4 whitespace-nowrap">
                            <span :class="['inline-flex rounded-full px-2 py-1 text-xs font-semibold leading-5', getCategoryBadgeClass(project.category)]">
                                {{ project.category }}
                            </span>
                    </td>

                    <!-- Technologies column — show dots with tooltip -->
                    <td class="px-6 py-4">
                        <div class="flex flex-wrap gap-1">
                                <span
                                    v-for="techId in project.technology_ids.slice(0, 4)"
                                    :key="techId"
                                    class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-700"
                                >
                                    {{
                                        Object.values(technologies)
                                            .flat()
                                            .find(t => t.id === techId)?.name ?? techId
                                    }}
                                </span>
                            <span
                                v-if="project.technology_ids.length > 4"
                                class="inline-flex items-center rounded-full bg-gray-200 px-2 py-0.5 text-xs text-gray-600"
                            >
                                    +{{ project.technology_ids.length - 4 }}
                                </span>
                            <span v-if="project.technology_ids.length === 0" class="text-xs text-gray-400">—</span>
                        </div>
                    </td>

                    <td class="px-6 py-4 whitespace-nowrap">
                            <span :class="['inline-flex rounded-full px-2 py-1 text-xs font-semibold leading-5', project.is_featured ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800']">
                                {{ project.is_featured ? 'Featured' : 'No' }}
                            </span>
                    </td>

                    <td class="px-6 py-4 text-sm whitespace-nowrap">
                        <div class="flex gap-3">
                            <a v-if="project.live_url" :href="project.live_url" target="_blank" class="text-blue-600 transition-colors hover:text-blue-800" title="Live URL">
                                <ExternalLink class="h-4 w-4" />
                            </a>
                            <a v-if="project.repo_url" :href="project.repo_url" target="_blank" class="text-gray-600 transition-colors hover:text-gray-800" title="Repository">
                                <Github class="h-4 w-4" />
                            </a>
                            <span v-if="!project.live_url && !project.repo_url" class="text-gray-400">-</span>
                        </div>
                    </td>

                    <td class="px-6 py-4 text-sm whitespace-nowrap text-gray-500">
                        {{ formatDate(project.created_at) }}
                    </td>

                    <td class="px-6 py-4 text-sm whitespace-nowrap text-right font-medium">
                        <div class="flex justify-end gap-2">
                            <button @click="openEditModal(project)" class="text-indigo-600 transition-colors hover:text-indigo-900" title="Edit">
                                <Pencil class="h-4 w-4" />
                            </button>
                            <button @click="deleteProject(project)" class="text-red-600 transition-colors hover:text-red-900" title="Delete">
                                <Trash2 class="h-4 w-4" />
                            </button>
                        </div>
                    </td>

                </tr>

                <tr v-if="projects.length === 0">
                    <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                        No projects found. Click "Add New Project" to create one.
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <!-- Add / Edit Modal -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="fixed inset-0 bg-black/50" @click="closeModal" />

            <div class="relative mx-4 max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-lg bg-white shadow-xl">
                <form @submit.prevent="submitForm">

                    <!-- Modal Header -->
                    <div class="sticky top-0 z-10 flex items-center justify-between border-b bg-white px-6 py-4">
                        <h3 class="text-lg font-semibold text-gray-900">
                            {{ isEditing ? 'Edit Project' : 'Add New Project' }}
                        </h3>
                        <button type="button" @click="closeModal" class="text-gray-400 hover:text-gray-500">
                            <X class="h-5 w-5" />
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="space-y-4 px-6 py-4">

                        <!-- Name -->
                        <div class="grid gap-2">
                            <Label for="name">Project Name *</Label>
                            <Input id="name" v-model="formData.name" type="text" required placeholder="Enter project name" />
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
                                    <SelectItem v-for="cat in categories" :key="cat" :value="cat">
                                        {{ cat }}
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
                                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500"
                                required
                                placeholder="Describe your project..."
                            />
                            <InputError :message="formErrors.description?.[0]" />
                        </div>

                        <!-- URLs -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="grid gap-2">
                                <Label for="live_url">Live URL</Label>
                                <Input id="live_url" v-model="formData.live_url" type="url" placeholder="https://example.com" />
                                <InputError :message="formErrors.live_url?.[0]" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="repo_url">Repository URL</Label>
                                <Input id="repo_url" v-model="formData.repo_url" type="url" placeholder="https://github.com/..." />
                                <InputError :message="formErrors.repo_url?.[0]" />
                            </div>
                        </div>

                        <!-- Thumbnail -->
                        <div class="grid gap-2">
                            <Label for="thumbnail_url">Thumbnail URL</Label>
                            <Input id="thumbnail_url" v-model="formData.thumbnail_url" type="url" placeholder="https://example.com/image.jpg" />
                            <InputError :message="formErrors.thumbnail_url?.[0]" />
                        </div>

                        <!-- ── TECHNOLOGIES ── -->
                        <div class="grid gap-2">
                            <Label>Technologies Used</Label>
                            <p class="text-xs text-muted-foreground -mt-1">
                                Select all technologies used in this project
                            </p>

                            <div class="rounded-md border p-4 space-y-4">

                                <div
                                    v-for="(techs, categoryName) in technologies"
                                    :key="categoryName"
                                >
                                    <!-- Category heading -->
                                    <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-gray-400">
                                        {{ categoryName }}
                                    </p>

                                    <!-- Tech checkboxes in 3-column grid -->
                                    <div class="grid grid-cols-3 gap-x-4 gap-y-2">
                                        <div
                                            v-for="tech in techs"
                                            :key="tech.id"
                                            class="flex items-center gap-2"
                                        >

                                            <input
                                                type="checkbox"
                                                :id="`tech-${tech.id}`"
                                                :checked="isTechSelected(tech.id)"
                                                @change="toggleTechnology(tech.id)"
                                                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer"
                                            />
<!--                                            <Checkbox-->
<!--                                                :id="`tech-${tech.id}`"-->
<!--                                                :checked="isTechSelected(tech.id)"-->
<!--                                                @update:checked="toggleTechnology(tech.id)"-->
<!--                                            />-->
                                            <label
                                                :for="`tech-${tech.id}`"
                                                class="flex cursor-pointer items-center gap-1.5 text-sm"
                                            >
                                                <!-- Color dot from technology record -->
                                                <span
                                                    v-if="tech.color"
                                                    class="inline-block h-2 w-2 rounded-full shrink-0"
                                                    :style="{ backgroundColor: tech.color }"
                                                />
                                                {{ tech.name }}
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Empty state -->
                                <p v-if="Object.keys(technologies).length === 0" class="text-sm text-gray-400">
                                    No technologies found. Add some in the Technologies section first.
                                </p>

                            </div>
                            <InputError :message="formErrors.technology_ids?.[0]" />
                        </div>

                        <!-- Featured + Sort Order -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="flex items-center space-x-2">
                                <Checkbox id="is_featured" v-model:checked="formData.is_featured" />
                                <Label for="is_featured" class="mb-0">Featured Project</Label>
                            </div>
                            <div class="grid gap-2">
                                <Label for="sort_order">Sort Order</Label>
                                <Input id="sort_order" v-model.number="formData.sort_order" type="number" min="0" />
                                <InputError :message="formErrors.sort_order?.[0]" />
                            </div>
                        </div>

                    </div>

                    <!-- Modal Footer -->
                    <div class="sticky bottom-0 flex justify-end gap-3 border-t bg-gray-50 px-6 py-4">
                        <Button type="button" variant="outline" @click="closeModal">Cancel</Button>
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