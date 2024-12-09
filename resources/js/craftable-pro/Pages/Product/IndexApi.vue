<template>
    <PageHeader :title="$t('global', 'Products')">
        <Button
            :leftIcon="PlusIcon"
            :as="Link"
            :href="route('craftable-pro.products.create')"
            v-can="'global.product.create'"
        >
            {{ $t('global', 'New Product') }}
        </Button>
        <Button
            :leftIcon="ArrowDownTrayIcon"
            as="a"
            class="ml-2"
            @click="downloadFile"
        >
            {{ $t('global', 'Export') }}
        </Button>
    </PageHeader>

    <PageContent>
        <Listing
            :baseUrl="route('craftable-pro.products.index-api')"
            :data="productsApi"
            dataKey="productsApi"
            :with-bulk-select="false"
        >
            <template #actions>
                <FiltersDropdown
                    :active-filters-count="activeFiltersCount"
                    :reset-filters="resetFilters"
                >
                    <template
                        v-for="(filter, key) in productsOptions"
                        :key="key"
                    >
                        <Multiselect
                            v-if="filter?.length"
                            v-model="filtersForm[key]"
                            :name="key"
                            :label="key"
                            :options="filter"
                            :can-clear="true"
                        />
                        <!--                                    :mode="Array.isArray(filtersForm[filter]) ? 'multiple' : 'single'"-->
                    </template>
                </FiltersDropdown>
            </template>
            <template #tableHead>
                <template
                    v-for="(field, key) in productsApi?.data[0]"
                    :key="key"
                >
                    <ListingHeaderCell
                        v-width-dragging
                        v-if="tableColumns.includes(key)"
                        :sort-by="key"
                    >
                        {{ key }}
                    </ListingHeaderCell>
                </template>
                <ListingHeaderCell
                    v-width-dragging
                    v-for="warehouse in warehouses"
                    class="max-w-[70px]"
                    :key="warehouse.id"
                >
                    {{ warehouse.name }}
                </ListingHeaderCell>
                <ListingHeaderCell v-width-dragging>
                    <span class="sr-only">{{ $t('global', 'Actions') }}</span>
                </ListingHeaderCell>
            </template>
            <template #tableRow="{ item, action }: any">
                <template
                    v-for="(field, key) in productsApi?.data[0]"
                    :key="key"
                >
                    <ListingDataCell
                        v-if="tableColumns.includes(key)"
                        :sort-by="key"
                    >
                        <Avatar
                            v-if="key === 'image'"
                            :src="item[key]"
                            size="sm"
                            name="Logo"
                        />
                        <pre v-else-if="key === 'warehouseStock'">
                      {{ item[key] }}
                  </pre
                        >
                        <template v-else>
                            {{ item[key] }}
                        </template>
                    </ListingDataCell>
                </template>
                <ListingHeaderCell
                    v-width-dragging
                    v-for="warehouse in warehouses"
                    class="max-w-[70px]"
                    :key="warehouse.id"
                >
                    {{
                        item?.warehouseStock.find(
                            (stock: any) =>
                                stock.warehouse.name === warehouse.ysell_name,
                        )?.qty ?? 0
                    }}
                </ListingHeaderCell>
            </template>
        </Listing>
    </PageContent>
</template>

<script setup lang="ts">
import { Warehouse } from '@/craftable-pro/Pages/Warehouse/types';
import { ArrowDownTrayIcon, PlusIcon } from '@heroicons/vue/24/outline';
import { Link, usePage } from '@inertiajs/vue3';
import {
    Avatar,
    Button,
    FiltersDropdown,
    Listing,
    ListingDataCell,
    ListingHeaderCell,
    Multiselect,
    PageContent,
    PageHeader,
} from 'craftable-pro/Components';
import { useListingFilters } from 'craftable-pro/hooks/useListingFilters';
import type { PageProps } from 'craftable-pro/types/page';

interface Props {
    productsApi: object[];
    warehouses: Object<string, Warehouse>;
    productsOptions: Object<string, string[]>;
}
defineProps<Props>();

const tableColumns: string[] = [
    // "id",
    'ext_id',
    'title',
    'condition',
    'image',
];
const downloadFile = () => {
    const url = window.location.href.split('?');
    if (url.length > 1) {
        window.location = route(
            'craftable-pro.products.export',
            url.pop(),
        ).slice(0, -1);
    } else {
        window.location = route('craftable-pro.products.export');
    }
};

const { filtersForm, resetFilters, activeFiltersCount } = useListingFilters(
    route('craftable-pro.products.index-api'),
    {
        condition:
            (usePage().props as unknown as PageProps).filter?.condition ?? [],
    },
);
</script>
