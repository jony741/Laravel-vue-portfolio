<!-- resources/js/pages/profile/Index.vue -->
<script setup lang="ts">
import { Form, Head, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Checkbox } from '@/components/ui/checkbox';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { toast } from 'vue-sonner';
import ProfileController from '@/actions/App/Http/Controllers/ProfileController';

interface ProfileData {
    id?: number;
    full_name: string;
    designation: string;
    short_description: string | null;
    experience_summary: string | null;
    avatar_url: string | null;
    portfolio_github_folder_link: string | null;
    linked_link: string | null;
    email: string | null;
    is_active: boolean;
}

const props = defineProps<{
    profile?: ProfileData;
}>();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Profile', href: '/profile' }],
    },
});

const form = useForm({
    id: props.profile?.id,
    full_name: props.profile?.full_name || '',
    designation: props.profile?.designation || '',
    short_description: props.profile?.short_description || '',
    experience_summary: props.profile?.experience_summary || '',
    avatar_url: props.profile?.avatar_url || '',
    portfolio_github_folder_link: props.profile?.portfolio_github_folder_link || '',
    linked_link: props.profile?.linked_link || '',
    email: props.profile?.email || '',
    is_active: props.profile?.is_active ?? true,
});

const initials = computed(() => {
    const name = form.full_name.trim();
    if (!name) return 'YN';
    const parts = name.split(/\s+/).filter(Boolean);
    if (parts.length === 0) return 'YN';
    if (parts.length === 1) return parts[0].slice(0, 2).toUpperCase();
    return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
});

const submitForm = () => {
    form.post(route('profile.store'), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Profile saved successfully!');
        },
        onError: () => {
            toast.error('Please check the form for errors.');
        },
    });
};
</script>

<template>
    <Head title="Profile" />
    <div class="container mx-auto space-y-6 px-4 py-8">
            <div>
                <h1 class="text-3xl font-bold text-foreground">Profile</h1>
                <p class="mt-1 text-muted-foreground">
                    Manage the single portfolio profile displayed across the
                    application.
                </p>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Profile Information</CardTitle>
                    <CardDescription>
                        Update your personal, professional, and social details.
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <form class="space-y-6"
                          v-bind="ProfileController.store.form()">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="full_name">Full Name *</Label>
                                <Input
                                    id="full_name"
                                    v-model="form.full_name"
                                    type="text"
                                    placeholder="John Doe"
                                    required
                                />
                                <p
                                    v-if="form.errors.full_name"
                                    class="text-sm text-destructive"
                                >
                                    {{ form.errors.full_name }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <Label for="designation">Designation *</Label>
                                <Input
                                    id="designation"
                                    v-model="form.designation"
                                    type="text"
                                    placeholder="Senior Developer"
                                />
                                <p
                                    v-if="form.errors.designation"
                                    class="text-sm text-destructive"
                                >
                                    {{ form.errors.designation }}
                                </p>
                            </div>

                            <div class="space-y-2 md:col-span-2">
                                <Label for="short_description">
                                    Short Description
                                </Label>
                                <Textarea
                                    id="short_description"
                                    v-model="form.short_description"
                                    placeholder="A brief introduction about yourself..."
                                    rows="3"
                                />
                                <p
                                    v-if="form.errors.short_description"
                                    class="text-sm text-destructive"
                                >
                                    {{ form.errors.short_description }}
                                </p>
                            </div>

                            <div class="space-y-2 md:col-span-2">
                                <Label for="experience_summary">
                                    Experience Summary
                                </Label>
                                <Textarea
                                    id="experience_summary"
                                    v-model="form.experience_summary"
                                    placeholder="Summarize your professional experience..."
                                    rows="5"
                                />
                                <p
                                    v-if="form.errors.experience_summary"
                                    class="text-sm text-destructive"
                                >
                                    {{ form.errors.experience_summary }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <Label for="email">Email</Label>
                                <Input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    placeholder="you@example.com"
                                />
                                <p
                                    v-if="form.errors.email"
                                    class="text-sm text-destructive"
                                >
                                    {{ form.errors.email }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <Label for="avatar_url">Avatar URL</Label>
                                <Input
                                    id="avatar_url"
                                    v-model="form.avatar_url"
                                    type="url"
                                    placeholder="https://example.com/avatar.jpg"
                                />
                                <p class="text-xs text-muted-foreground">
                                    Provide a URL to your profile picture/avatar.
                                </p>
                                <p
                                    v-if="form.errors.avatar_url"
                                    class="text-sm text-destructive"
                                >
                                    {{ form.errors.avatar_url }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <Label for="portfolio_github_folder_link">
                                    Portfolio / GitHub Link
                                </Label>
                                <Input
                                    id="portfolio_github_folder_link"
                                    v-model="form.portfolio_github_folder_link"
                                    type="url"
                                    placeholder="https://github.com/username/repo"
                                />
                                <p class="text-xs text-muted-foreground">
                                    Link to your GitHub repository or portfolio
                                    project.
                                </p>
                                <p
                                    v-if="form.errors.portfolio_github_folder_link"
                                    class="text-sm text-destructive"
                                >
                                    {{ form.errors.portfolio_github_folder_link }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <Label for="linked_link">LinkedIn Profile</Label>
                                <Input
                                    id="linked_link"
                                    v-model="form.linked_link"
                                    type="url"
                                    placeholder="https://linkedin.com/in/username"
                                />
                                <p
                                    v-if="form.errors.linked_link"
                                    class="text-sm text-destructive"
                                >
                                    {{ form.errors.linked_link }}
                                </p>
                            </div>

                            <div class="space-y-2 md:col-span-2">
                                <div class="flex items-center space-x-2 pt-2">
                                    <Checkbox
                                        id="is_active"
                                        v-model:checked="form.is_active"
                                    />
                                    <Label
                                        for="is_active"
                                        class="text-sm font-normal"
                                    >
                                        Active profile
                                    </Label>
                                </div>
                                <p class="text-xs text-muted-foreground">
                                    Active profile is displayed publicly (always
                                    set to true on save).
                                </p>
                                <p
                                    v-if="form.errors.is_active"
                                    class="text-sm text-destructive"
                                >
                                    {{ form.errors.is_active }}
                                </p>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-4">
                            <Button
                                type="button"
                                variant="outline"
                                @click="form.reset()"
                            >
                                Reset
                            </Button>
                            <Button type="submit" :disabled="form.processing">
                                {{
                                    form.processing
                                        ? 'Saving…'
                                        : 'Save Profile'
                                }}
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Preview</CardTitle>
                    <CardDescription>
                        How your profile will appear to visitors.
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div
                        class="flex items-start space-x-4 rounded-lg border bg-muted/20 p-4"
                    >
                        <Avatar class="h-16 w-16">
                            <AvatarImage
                                v-if="form.avatar_url"
                                :src="form.avatar_url"
                                :alt="form.full_name || 'Avatar'"
                            />
                            <AvatarFallback>{{ initials }}</AvatarFallback>
                        </Avatar>
                        <div class="flex-1 space-y-1">
                            <h3 class="text-lg font-semibold">
                                {{ form.full_name || 'Your Name' }}
                            </h3>
                            <p class="text-sm text-muted-foreground">
                                {{
                                    form.designation || 'Your Designation'
                                }}
                            </p>
                            <p class="text-sm">
                                {{
                                    form.short_description ||
                                    'Your short description will appear here…'
                                }}
                            </p>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
</template>
