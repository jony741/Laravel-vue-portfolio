<!-- resources/js/pages/admin/projects/ProjectFormFields.vue -->
<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';

interface Project {
    id?: number;
    name?: string;
    description?: string;
    category?: string;
    live_url?: string | null;
    repo_url?: string | null;
    thumbnail_url?: string | null;
    is_featured?: boolean;
    sort_order?: number;
}

defineProps<{
    errors: Record<string, string>;
    project?: Project;
}>();
</script>

<template>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

        <div class="space-y-2">
            <Label for="name">Project Name *</Label>
            <Input
                id="name"
                name="name"
                type="text"
                placeholder="My Awesome Project"
                :value="project?.name ?? ''"
                required
            />
            <InputError :message="errors.name" />
        </div>

        <div class="space-y-2">
            <Label for="category">Category *</Label>
            <Input
                id="category"
                name="category"
                type="text"
                placeholder="Web App, Mobile, API…"
                :value="project?.category ?? ''"
                required
            />
            <InputError :message="errors.category" />
        </div>

        <div class="space-y-2 sm:col-span-2">
            <Label for="description">Description *</Label>
            <Textarea
                id="description"
                name="description"
                placeholder="Describe your project..."
                :value="project?.description ?? ''"
                rows="4"
                required
            />
            <InputError :message="errors.description" />
        </div>

        <div class="space-y-2">
            <Label for="live_url">Live URL</Label>
            <Input
                id="live_url"
                name="live_url"
                type="url"
                placeholder="https://myproject.com"
                :value="project?.live_url ?? ''"
            />
            <InputError :message="errors.live_url" />
        </div>

        <div class="space-y-2">
            <Label for="repo_url">Repository URL</Label>
            <Input
                id="repo_url"
                name="repo_url"
                type="url"
                placeholder="https://github.com/user/repo"
                :value="project?.repo_url ?? ''"
            />
            <InputError :message="errors.repo_url" />
        </div>

        <div class="space-y-2">
            <Label for="thumbnail_url">Thumbnail URL</Label>
            <Input
                id="thumbnail_url"
                name="thumbnail_url"
                type="url"
                placeholder="https://example.com/thumb.jpg"
                :value="project?.thumbnail_url ?? ''"
            />
            <InputError :message="errors.thumbnail_url" />
        </div>

        <div class="space-y-2">
            <Label for="sort_order">Sort Order</Label>
            <Input
                id="sort_order"
                name="sort_order"
                type="number"
                placeholder="0"
                :value="project?.sort_order ?? 0"
                min="0"
            />
            <InputError :message="errors.sort_order" />
        </div>

        <div class="space-y-2 sm:col-span-2">
            <div class="flex items-center space-x-2">
                <Checkbox
                    id="is_featured"
                    name="is_featured"
                    value="1"
                    :checked="project?.is_featured ?? false"
                />
                <Label for="is_featured" class="text-sm font-normal">
                    Featured project
                </Label>
            </div>
            <p class="text-xs text-muted-foreground">
                Featured projects are highlighted on your portfolio.
            </p>
            <InputError :message="errors.is_featured" />
        </div>

    </div>
</template>