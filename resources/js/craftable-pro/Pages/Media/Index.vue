<template>
    <MediaPage v-bind="props" />
</template>

<script lang="ts" setup>
import MediaPage from 'craftable-pro/Pages/Media/Index.vue';
import type { Media } from 'craftable-pro/Pages/Media/types';
import { PaginatedCollection } from 'craftable-pro/types/pagination';

interface Props {
    data: PaginatedCollection<Media>;
    filterOptions: {
        model_type: string[];
        collection_name: string[];
        mime_type: string[];
    };
}

const props = defineProps<Props>();
</script>
