<template>
    <PageHeader :title="$t('global', 'Activities')"> </PageHeader>

    <PageContent>
        <ActivityTable
            :baseUrl="route('craftable-pro.activity.index')"
            :activities="activities"
            :filterOptions="filterOptions"
        />
    </PageContent>
</template>

<script setup lang="ts">
import ActivityTable from '@/craftable-pro/Pages/Activity/ActivityTable.vue';
import { PageContent, PageHeader } from 'craftable-pro/Components';
import { PaginatedCollection } from 'craftable-pro/types/pagination';
import type { Activities } from './types';

interface Props {
    activities: PaginatedCollection<Activities>;
    filterOptions: {
        causer_type: string[];
        subject_type: string[];
        event: string[];
    };
}
const props = defineProps<Props>();
</script>
