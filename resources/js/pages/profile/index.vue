<!-- resources/js/pages/profile/Index.vue -->
<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { toast } from 'vue-sonner';
import ProfileController from '@/actions/App/Http/Controllers/Admin/ProfileController';
import InputError from '@/components/InputError.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';

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

// These refs are ONLY for the live preview — not for form submission
// <Form> reads the actual input values via name="" attribute on submit
const previewName       = ref(props.profile?.full_name || '');
const previewDesig      = ref(props.profile?.designation || '');
const previewDesc       = ref(props.profile?.short_description || '');
const previewAvatar     = ref(props.profile?.avatar_url || '');

const initials = computed(() => {
    const name = previewName.value.trim();

    if (!name) {
        return 'YN';
    }

    const parts = name.split(/\s+/).filter(Boolean);

    if (parts.length === 0) {
        return 'YN';
    }

    if (parts.length === 1) {
        return parts[0].slice(0, 2).toUpperCase();
    }

    return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
});
</script>

<template>
    <Head title="Profile" />

    <div class="container mx-auto space-y-6 px-4 py-8">

        <div>
            <h1 class="text-3xl font-bold text-foreground">Profile</h1>
            <p class="mt-1 text-muted-foreground">
                Manage the single portfolio profile displayed across the application.
            </p>
        </div>

        <!-- Live preview card — reads from refs, not the form -->
        <Card>
            <CardHeader>
                <CardTitle>Preview</CardTitle>
                <CardDescription>
                    How your profile will appear to visitors.
                </CardDescription>
            </CardHeader>
            <CardContent>
                <div class="flex items-start space-x-4 rounded-lg border bg-muted/20 p-4">
                    <Avatar class="h-16 w-16">
                        <AvatarImage
                            v-if="previewAvatar"
                            :src="previewAvatar"
                            :alt="previewName || 'Avatar'"
                        />
                        <AvatarFallback>{{ initials }}</AvatarFallback>
                    </Avatar>
                    <div class="flex-1 space-y-1">
                        <h3 class="text-lg font-semibold">
                            {{ previewName || 'Your Name' }}
                        </h3>
                        <p class="text-sm text-muted-foreground">
                            {{ previewDesig || 'Your Designation' }}
                        </p>
                        <p class="text-sm">
                            {{ previewDesc || 'Your short description will appear here…' }}
                        </p>
                    </div>
                </div>
            </CardContent>
        </Card>

        <Card>
            <CardHeader>
                <CardTitle>Profile Information</CardTitle>
                <CardDescription>
                    Update your personal, professional, and social details.
                </CardDescription>
            </CardHeader>
            <CardContent>
                <Form
                    v-bind="ProfileController.store.form()"
                    :options="{ preserveScroll: true }"
                    reset-on-success
                    class="space-y-6"
                    v-slot="{ errors, processing, reset }"
                    @success="toast.success('Profile saved successfully!')"
                    @error="toast.error('Please check the form for errors.')"
                >
                    <!-- Hidden id field for updateOrCreate -->
                    <input
                        type="hidden"
                        name="id"
                        :value="props.profile?.id"
                    />

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

                        <div class="space-y-2">
                            <Label for="full_name">Full Name *</Label>
                            <Input
                                id="full_name"
                                name="full_name"
                                type="text"
                                placeholder="John Doe"
                                :default-value="props.profile?.full_name"
                                required
                                @input="previewName = ($event.target as HTMLInputElement).value"
                            />
                            <InputError :message="errors.full_name" />
                        </div>

                        <div class="space-y-2">
                            <Label for="designation">Designation *</Label>
                            <Input
                                id="designation"
                                name="designation"
                                type="text"
                                placeholder="Senior Developer"
                                :default-value="props.profile?.designation"
                                @input="previewDesig = ($event.target as HTMLInputElement).value"
                            />
                            <InputError :message="errors.designation" />
                        </div>

                        <div class="space-y-2 md:col-span-2">
                            <Label for="short_description">Short Description</Label>
                            <Textarea
                                id="short_description"
                                name="short_description"
                                placeholder="A brief introduction about yourself..."
                                :default-value="props.profile?.short_description ?? ''"
                                rows="3"
                                @input="previewDesc = ($event.target as HTMLTextAreaElement).value"
                            />
                            <InputError :message="errors.short_description" />
                        </div>

                        <div class="space-y-2 md:col-span-2">
                            <Label for="experience_summary">Experience Summary</Label>
                            <Textarea
                                id="experience_summary"
                                name="experience_summary"
                                placeholder="Summarize your professional experience..."
                                :default-value="props.profile?.experience_summary ?? ''"
                                rows="5"
                            />
                            <InputError :message="errors.experience_summary" />
                        </div>

                        <div class="space-y-2">
                            <Label for="email">Email</Label>
                            <Input
                                id="email"
                                name="email"
                                type="email"
                                placeholder="you@example.com"
                                :default-value="props.profile?.email ?? ''"
                            />
                            <InputError :message="errors.email" />
                        </div>

                        <div class="space-y-2">
                            <Label for="avatar_url">Avatar URL</Label>
                            <Input
                                id="avatar_url"
                                name="avatar_url"
                                type="url"
                                placeholder="https://example.com/avatar.jpg"
                                :default-value="props.profile?.avatar_url ?? ''"
                                @input="previewAvatar = ($event.target as HTMLInputElement).value"
                            />
                            <p class="text-xs text-muted-foreground">
                                Provide a URL to your profile picture/avatar.
                            </p>
                            <InputError :message="errors.avatar_url" />
                        </div>

                        <div class="space-y-2">
                            <Label for="portfolio_github_folder_link">
                                Portfolio / GitHub Link
                            </Label>
                            <Input
                                id="portfolio_github_folder_link"
                                name="portfolio_github_folder_link"
                                type="url"
                                placeholder="https://github.com/username/repo"
                                :default-value="props.profile?.portfolio_github_folder_link ?? ''"
                            />
                            <p class="text-xs text-muted-foreground">
                                Link to your GitHub repository or portfolio project.
                            </p>
                            <InputError :message="errors.portfolio_github_folder_link" />
                        </div>

                        <div class="space-y-2">
                            <Label for="linked_link">LinkedIn Profile</Label>
                            <Input
                                id="linked_link"
                                name="linked_link"
                                type="url"
                                placeholder="https://linkedin.com/in/username"
                                :default-value="props.profile?.linked_link ?? ''"
                            />
                            <InputError :message="errors.linked_link" />
                        </div>

                        <div class="space-y-2 md:col-span-2">
                            <div class="flex items-center space-x-2 pt-2">
                                <Checkbox
                                    id="is_active"
                                    name="is_active"
                                    :default-checked="props.profile?.is_active ?? true"
                                    value="1"
                                />
                                <Label for="is_active" class="text-sm font-normal">
                                    Active profile
                                </Label>
                            </div>
                            <p class="text-xs text-muted-foreground">
                                Active profile is displayed publicly.
                            </p>
                            <InputError :message="errors.is_active" />
                        </div>

                    </div>

                    <div class="flex justify-end space-x-4">
                        <Button
                            type="button"
                            variant="outline"
                            @click="reset()"
                        >
                            Reset
                        </Button>
                        <Button type="submit" :disabled="processing">
                            {{ processing ? 'Saving…' : 'Save Profile' }}
                        </Button>
                    </div>

                </Form>
            </CardContent>
        </Card>



    </div>
</template>