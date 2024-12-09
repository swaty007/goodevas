<template>
    <PageHeader :title="$t('global', 'Api Keys')">
        <Button
            :leftIcon="PlusIcon"
            :as="Link"
            :href="route('craftable-pro.api-keys.create')"
            v-can="'global.api-key.create'"
        >
            {{ $t('global', 'New Api Key') }}
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
            :baseUrl="route('craftable-pro.api-keys.index')"
            :data="apiKeys"
            dataKey="apiKeys"
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
                            v-can="'global.api-key.destroy'"
                        >
                            {{ $t('global', 'Delete') }}
                        </Button>
                    </template>

                    <template #title>
                        {{ $t('global', 'Delete Api Key') }}
                    </template>
                    <template #content>
                        {{
                            $t(
                                'global',
                                'Are you sure you want to delete selected Api Key? All data will be permanently removed from our servers forever. This action cannot be undone.',
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
                                            'craftable-pro.api-keys.bulk-destroy',
                                        ),
                                        {
                                            onFinish: () => setIsOpen(false),
                                        },
                                    );
                                }
                            "
                            color="danger"
                            v-can="'global.api-key.destroy'"
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
                <ListingHeaderCell v-width-dragging sortBy="type">
                    {{ $t('global', 'Type') }}
                </ListingHeaderCell>
                <ListingHeaderCell v-width-dragging sortBy="key">
                    {{ $t('global', 'Key') }}
                </ListingHeaderCell>
                <ListingHeaderCell v-width-dragging sortBy="additional_data">
                    {{ $t('global', 'Additional Data') }}
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
                    {{ item.type }}
                </ListingDataCell>
                <ListingDataCell>
                    {{ item.key }}
                </ListingDataCell>
                <ListingDataCell>
                    {{ item.additional_data }}
                </ListingDataCell>
                <ListingDataCell>
                    <div class="flex items-center justify-end gap-3">
                        <IconButton
                            :as="Link"
                            :href="route('craftable-pro.api-keys.edit', item)"
                            variant="ghost"
                            color="gray"
                            :icon="PencilSquareIcon"
                            v-can="'global.api-key.edit'"
                        />

                        <Modal type="danger">
                            <template #trigger="{ setIsOpen }">
                                <IconButton
                                    @click="() => setIsOpen(true)"
                                    color="gray"
                                    variant="ghost"
                                    :icon="TrashIcon"
                                    v-can="'global.api-key.destroy'"
                                />
                            </template>

                            <template #title>
                                {{ $t('global', 'Delete Api Key') }}
                            </template>
                            <template #content>
                                {{
                                    $t(
                                        'global',
                                        'Are you sure you want to delete selected Api Key? All data will be permanently removed from our servers forever. This action cannot be undone.',
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
                                                    'craftable-pro.api-keys.destroy',
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
                                    v-can="'global.api-key.destroy'"
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
import type { ApiKey } from './types';

interface Props {
    apiKeys: PaginatedCollection<ApiKey>;
}
defineProps<Props>();
const downloadFile = () => {
    const url = window.location.href.split('?');
    if (url.length > 1) {
        window.location = route(
            'craftable-pro.api-keys.export',
            url.pop(),
        ).slice(0, -1);
    } else {
        window.location = route('craftable-pro.api-keys.export');
    }
};
</script>
