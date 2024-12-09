<template>
    <PageHeader :title="$t('global', 'Product Types')">
        <Button
            :leftIcon="PlusIcon"
            :as="Link"
            :href="route('craftable-pro.product-types.create')"
            v-can="'global.product-type.create'"
        >
            {{ $t('global', 'New Product Type') }}
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
            :baseUrl="route('craftable-pro.product-types.index')"
            :data="productTypes"
            dataKey="productTypes"
        >
            <template #bulkActions="{ bulkAction }">
                <Modal type="danger">
                    <template #trigger="{ setIsOpen }">
                        <Button
                            @click="() => setIsOpen(true)"
                            color="gray"
                            variant="outline"
                            size="sm"
                            :leftIcon="TrashIcon"
                            v-can="'global.product-type.destroy'"
                        >
                            {{ $t('global', 'Delete') }}
                        </Button>
                    </template>

                    <template #title>
                        {{ $t('global', 'Delete Product Type') }}
                    </template>
                    <template #content>
                        {{
                            $t(
                                'global',
                                'Are you sure you want to delete selected Product Type? All data will be permanently removed from our servers forever. This action cannot be undone.',
                            )
                        }}
                    </template>

                    <template #buttons="{ setIsOpen }">
                        <Button
                            @click.prevent="
                                () => {
                                    bulkAction(
                                        'post',
                                        route(
                                            'craftable-pro.product-types.bulk-destroy',
                                        ),
                                        {
                                            onFinish: () => setIsOpen(false),
                                        },
                                    );
                                }
                            "
                            color="danger"
                            v-can="'global.product-type.destroy'"
                        >
                            {{ $t('global', 'Delete') }}
                        </Button>
                        <Button
                            @click.prevent="() => setIsOpen()"
                            color="gray"
                            variant="outline"
                        >
                            {{ $t('global', 'Cancel') }}
                        </Button>
                    </template>
                </Modal>
            </template>
            <template #tableHead>
                <ListingHeaderCell v-width-dragging sortBy="id">
                    {{ $t('global', 'Id') }}
                </ListingHeaderCell>
                <ListingHeaderCell v-width-dragging sortBy="name">
                    {{ $t('global', 'Name') }}
                </ListingHeaderCell>
                <ListingHeaderCell v-width-dragging>
                    <span class="sr-only">{{ $t('global', 'Actions') }}</span>
                </ListingHeaderCell>
            </template>
            <template #tableRow="{ item, action }: any">
                <ListingDataCell>
                    {{ item.id }}
                </ListingDataCell>
                <ListingDataCell>
                    {{ item.name }}
                </ListingDataCell>
                <ListingDataCell>
                    <div class="flex items-center justify-end gap-3">
                        <IconButton
                            :as="Link"
                            :href="
                                route('craftable-pro.product-types.edit', item)
                            "
                            variant="ghost"
                            color="gray"
                            :icon="PencilSquareIcon"
                            v-can="'global.product-type.edit'"
                        />

                        <Modal type="danger">
                            <template #trigger="{ setIsOpen }">
                                <IconButton
                                    @click="() => setIsOpen(true)"
                                    color="gray"
                                    variant="ghost"
                                    :icon="TrashIcon"
                                    v-can="'global.product-type.destroy'"
                                />
                            </template>

                            <template #title>
                                {{ $t('global', 'Delete Product Type') }}
                            </template>
                            <template #content>
                                {{
                                    $t(
                                        'global',
                                        'Are you sure you want to delete selected Product Type? All data will be permanently removed from our servers forever. This action cannot be undone.',
                                    )
                                }}
                            </template>

                            <template #buttons="{ setIsOpen }">
                                <Button
                                    @click.prevent="
                                        () => {
                                            action(
                                                'delete',
                                                route(
                                                    'craftable-pro.product-types.destroy',
                                                    item,
                                                ),
                                                {
                                                    onFinish: () =>
                                                        setIsOpen(false),
                                                },
                                            );
                                        }
                                    "
                                    color="danger"
                                    v-can="'global.product-type.destroy'"
                                >
                                    {{ $t('global', 'Delete') }}
                                </Button>
                                <Button
                                    @click.prevent="() => setIsOpen()"
                                    color="gray"
                                    variant="outline"
                                >
                                    {{ $t('global', 'Cancel') }}
                                </Button>
                            </template>
                        </Modal>
                    </div>
                </ListingDataCell>
            </template>
        </Listing>
    </PageContent>
</template>

<script setup lang="ts">
import {
    ArrowDownTrayIcon,
    PencilSquareIcon,
    PlusIcon,
    TrashIcon,
} from '@heroicons/vue/24/outline';
import { Link } from '@inertiajs/vue3';
import {
    Button,
    IconButton,
    Listing,
    ListingDataCell,
    ListingHeaderCell,
    Modal,
    PageContent,
    PageHeader,
} from 'craftable-pro/Components';
import { PaginatedCollection } from 'craftable-pro/types/pagination';
import type { ProductType } from './types';

interface Props {
    productTypes: PaginatedCollection<ProductType>;
}
defineProps<Props>();
const downloadFile = () => {
    const url = window.location.href.split('?');
    if (url.length > 1) {
        window.location = route(
            'craftable-pro.product-types.export',
            url.pop(),
        ).slice(0, -1);
    } else {
        window.location = route('craftable-pro.product-types.export');
    }
};
</script>
