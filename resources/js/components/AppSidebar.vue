<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { BookOpen, FolderGit2, LayoutGrid, User,Folder,Terminal, Book } from '@lucide/vue';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { showExperienceData } from '@/routes/admin/experience';
import { showProfile } from '@/routes/admin/profile';
import { getProject  } from '@/routes/admin/projects';
import { getTechStack } from '@/routes/admin/tech-stack';
import { getTechnology } from '@/routes/admin/technologies';
import type { NavItem } from '@/types';

const mainNavItems: NavItem[] = [
    {
        title: 'Profile',
        href: showProfile(),
        icon: User,
    },
    {
        title: 'Experience',
        href: showExperienceData(),
        icon: FolderGit2,
    },
    {
        title: 'Project',
        href: getProject(),
        icon: Folder,
    },
    {
        title: 'Technology',
        href: getTechnology(),
        icon: Terminal,
    },
    {
        title: 'TechStack',
        href: getTechStack(),
        icon: Book,
    },
];

</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
