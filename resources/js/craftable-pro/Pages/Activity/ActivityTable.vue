<template>
    <Listing
        :base-url="baseUrl"
        :data="activities"
        data-key="activities"
        :with-bulk-select="false"
        :class="['global__table']"
    >
        <template #actions>
            <FiltersDropdown
                :active-filters-count="activeFiltersCount"
                :reset-filters="resetFilters"
            >
                <Multiselect
                    v-if="filterOptions.causer_type?.length"
                    v-model="filtersForm.causer_type"
                    name="causer_type"
                    :label="$t('global', 'Causer Type')"
                    :options="filterOptions.causer_type"
                    :can-clear="true"
                />
                <Multiselect
                    v-if="filterOptions.subject_type?.length"
                    v-model="filtersForm.subject_type"
                    name="subject_type"
                    :label="$t('global', 'Subject Type')"
                    :options="filterOptions.subject_type"
                    :can-clear="true"
                />
                <Multiselect
                    v-if="filterOptions.event?.length"
                    v-model="filtersForm.event"
                    name="event"
                    :label="$t('global', 'Event')"
                    :options="filterOptions.event"
                    :can-clear="true"
                />
            </FiltersDropdown>
        </template>
        <template #bulkActions="{ bulkAction, bulkActionForm }" />
        <template #tableHead>
            <ListingHeaderCell sort-by="id">
                {{ $t("global", "Id") }}
            </ListingHeaderCell>
            <ListingHeaderCell>
                {{ $t("global", "Actions") }}
            </ListingHeaderCell>
            <ListingHeaderCell sort-by="properties">
                {{ $t("global", "Properties") }}
            </ListingHeaderCell>
            <ListingHeaderCell sort-by="subject_type">
                {{ $t("global", "Subject Type") }}
            </ListingHeaderCell>
            <ListingHeaderCell>
                {{ $t("global", "Subject") }}
            </ListingHeaderCell>
            <ListingHeaderCell sort-by="causer_type">
                {{ $t("global", "Causer Type") }}
            </ListingHeaderCell>
            <ListingHeaderCell>
                {{ $t("global", "Causer") }}
            </ListingHeaderCell>
            <ListingHeaderCell sort-by="event">
                {{ $t("global", "Event") }}
            </ListingHeaderCell>
            <ListingHeaderCell sort-by="description">
                {{ $t("global", "Description") }}
            </ListingHeaderCell>
            <ListingHeaderCell sort-by="created_at">
                {{ $t("global", "Created At") }}
            </ListingHeaderCell>
        </template>
        <template #tableRow="{ item, action }: any">
            <ListingDataCell>
                {{ item.id }}
            </ListingDataCell>
            <ListingDataCell>
                <template v-if="item.subject_type === 'Brackets\\CraftablePro\\Models\\CraftableProUser'">
                    {{ $t("global", "Admin") }}
                </template>
                <template v-else-if="item.subject_type">
                    {{ item.subject_type?.split('\\')?.pop() }}
                </template>
                {{ item.subject?.email || item.subject?.id }}
                {{ item.event }}
                {{ $t("global", "by") }}
                <template v-if="item.causer_type === 'Brackets\\CraftablePro\\Models\\CraftableProUser'">
                    {{ $t("global", "Admin") }}
                </template>
                <template v-else-if="item.causer_type">
                    {{ item.causer_type?.split('\\')?.pop() }}
                </template>
                {{ item.causer?.email }}
            </ListingDataCell>
            <ListingDataCell>
                <PropertiesDisplay :properties="item.properties" />
            </ListingDataCell>
            <ListingDataCell>
                <template v-if="item.subject_type">
                    <template v-if="item.subject_type === 'Brackets\\CraftablePro\\Models\\CraftableProUser'">
                        {{ $t("global", "Admin") }}
                    </template>
                    <template v-else-if="item.subject_type">
                        {{ item.subject_type?.split('\\')?.pop() }}
                    </template>
                </template>
            </ListingDataCell>
            <ListingDataCell>
                {{ item.subject?.email }}
<!--                <Link-->
<!--                    v-if="item?.subject_id && item.subject_type === 'App\\Models\\User'"-->
<!--                    :href="route('craftable-pro.users.activity', { user: item?.subject_id })"-->
<!--                >-->
<!--                    {{ item.subject?.email }}-->
<!--                </Link>-->
<!--                <template v-else>-->
<!--                    {{ item.subject?.email || item.subject?.id }}-->
<!--                </template>-->
            </ListingDataCell>
            <ListingDataCell>
                <template v-if="item.causer_type === 'Brackets\\CraftablePro\\Models\\CraftableProUser'">
                    {{ $t("global", "Admin") }}
                </template>
                <template v-else-if="item.causer_type">
                    {{ item.causer_type?.split('\\')?.pop() }}
                </template>
            </ListingDataCell>
            <ListingDataCell>
                {{ item.causer?.email }}
<!--                <Link-->
<!--                    v-if="item?.causer_id && item.causer_type === 'App\\Models\\User'"-->
<!--                    :href="route('craftable-pro.users.activity', { user: item?.causer_id })"-->
<!--                    target="_blank"-->
<!--                >-->
<!--                    {{ item.causer?.email }}-->
<!--                </Link>-->
<!--                <template v-else>-->
<!--                    {{ item.causer?.email }}-->
<!--                </template>-->
            </ListingDataCell>
            <ListingDataCell>
                {{ item.event }}
            </ListingDataCell>
            <ListingDataCell>
                {{ item.description }}
            </ListingDataCell>
            <ListingDataCell>
                <div
                    v-if="item.created_at"
                    class="flex gap-2"
                >
                    <div class="flex flex-col justify-center">
                        <ClockIcon class="h-4 w-4" />
                    </div>
                    <div>
                        {{ dayjs(item.created_at).format('DD.MM.YYYY HH:mm:ss') }}
                    </div>
                </div>
            </ListingDataCell>
        </template>
    </Listing>
</template>

<script setup lang="ts">
import { ClockIcon, } from "@heroicons/vue/24/outline";
import { FiltersDropdown, Listing, ListingDataCell, ListingHeaderCell, Multiselect, } from "craftable-pro/Components";
import { PaginatedCollection } from "craftable-pro/types/pagination";
import type { Activities } from "./types";
import { useListingFilters } from "craftable-pro/hooks/useListingFilters";
import { Link, usePage } from "@inertiajs/vue3";
import type { PageProps } from "craftable-pro/types/page";
import dayjs from "dayjs";
import PropertiesDisplay from "@/craftable-pro/Components/PropertiesDisplay.vue";

interface Props {
    baseUrl: string;
    activities: PaginatedCollection<Activities>;
    filterOptions: {
        causer_type: string[];
        subject_type: string[];
        event: string[];
    };
}
const props = defineProps<Props>();

const { filtersForm, resetFilters, activeFiltersCount } = useListingFilters(
    props.baseUrl,
    {
        causer_type: (usePage().props as unknown as PageProps).filter?.causer_type ?? [],
        subject_type: (usePage().props as unknown as PageProps).filter?.subject_type ?? [],
        event: (usePage().props as unknown as PageProps).filter?.event ?? [],
    }
);
</script>
