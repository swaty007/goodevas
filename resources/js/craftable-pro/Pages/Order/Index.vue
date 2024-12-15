<template>
    <PageHeader :title="$t('global', 'Orders')">
        <Button
            :leftIcon="PlusIcon"
            :as="Link"
            :href="route('craftable-pro.orders.create')"
            v-can="'global.order.create'"
        >
            {{ $t('global', 'New Order') }}
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
            :baseUrl="route('craftable-pro.orders.index')"
            :data="orders"
            dataKey="orders"
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
                            v-can="'global.order.destroy'"
                        >
                            {{ $t('global', 'Delete') }}
                        </Button>
                    </template>

                    <template #title>
                        {{ $t('global', 'Delete Order') }}
                    </template>
                    <template #content>
                        {{
                            $t(
                                'global',
                                'Are you sure you want to delete selected Order? All data will be permanently removed from our servers forever. This action cannot be undone.',
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
                                            'craftable-pro.orders.bulk-destroy',
                                        ),
                                        {
                                            onFinish: () => setIsOpen(false),
                                        },
                                    );
                                }
                            "
                            color="danger"
                            v-can="'global.order.destroy'"
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
                <ListingHeaderCell v-width-dragging sortBy="order_id">
                    {{ $t('global', 'Order Item') }}
                </ListingHeaderCell>
                <ListingHeaderCell v-width-dragging sortBy="type">
                    {{ $t('global', 'Type') }}
                </ListingHeaderCell>
                <ListingHeaderCell v-width-dragging sortBy="order_date">
                    {{ $t('global', 'Order Date') }}
                </ListingHeaderCell>
                <ListingHeaderCell v-width-dragging sortBy="update_date">
                    {{ $t('global', 'Update Date') }}
                </ListingHeaderCell>
                <ListingHeaderCell v-width-dragging sortBy="order_status">
                    {{ $t('global', 'Order Status') }}
                </ListingHeaderCell>
                <ListingHeaderCell v-width-dragging sortBy="fulfillment">
                    {{ $t('global', 'Fulfillment') }}
                </ListingHeaderCell>
                <ListingHeaderCell v-width-dragging sortBy="sales_channel">
                    {{ $t('global', 'Sales Channel') }}
                </ListingHeaderCell>
                <ListingHeaderCell v-width-dragging sortBy="total_amount">
                    {{ $t('global', 'Total Amount') }}
                </ListingHeaderCell>
                <ListingHeaderCell v-width-dragging sortBy="total_currency">
                    {{ $t('global', 'Total Currency') }}
                </ListingHeaderCell>
                <ListingHeaderCell v-width-dragging sortBy="payment_method">
                    {{ $t('global', 'Payment Method') }}
                </ListingHeaderCell>
                <ListingHeaderCell v-width-dragging sortBy="buyer_name">
                    {{ $t('global', 'Buyer Name') }}
                </ListingHeaderCell>
                <ListingHeaderCell v-width-dragging sortBy="address_line_1">
                    {{ $t('global', 'Address Line 1') }}
                </ListingHeaderCell>
                <ListingHeaderCell v-width-dragging sortBy="address_line_2">
                    {{ $t('global', 'Address Line 2') }}
                </ListingHeaderCell>
                <ListingHeaderCell v-width-dragging sortBy="city">
                    {{ $t('global', 'City') }}
                </ListingHeaderCell>
                <ListingHeaderCell v-width-dragging sortBy="state">
                    {{ $t('global', 'State') }}
                </ListingHeaderCell>
                <ListingHeaderCell v-width-dragging sortBy="postal_code">
                    {{ $t('global', 'Postal Code') }}
                </ListingHeaderCell>
                <ListingHeaderCell v-width-dragging sortBy="country_code">
                    {{ $t('global', 'Country Code') }}
                </ListingHeaderCell>
                <ListingHeaderCell v-width-dragging sortBy="expected_ship_date">
                    {{ $t('global', 'Expected Ship Date') }}
                </ListingHeaderCell>
                <ListingHeaderCell v-width-dragging sortBy="is_shipped">
                    {{ $t('global', 'Is Shipped') }}
                </ListingHeaderCell>
                <ListingHeaderCell v-width-dragging>
                    {{ $t('global', 'Original Object') }}
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
                    {{ item.order_id }}
                </ListingDataCell>
                <ListingDataCell>
                    {{ item.type }}
                </ListingDataCell>
                <ListingDataCell>
                    {{ dayjs(item.order_date).format('DD.MM.YYYY HH:mm') }}
                </ListingDataCell>
                <ListingDataCell>
                    {{ dayjs(item.update_date).format('DD.MM.YYYY HH:mm') }}
                </ListingDataCell>
                <ListingDataCell>
                    {{ item.order_status }}
                </ListingDataCell>
                <ListingDataCell>
                    {{ item.fulfillment }}
                </ListingDataCell>
                <ListingDataCell>
                    {{ item.sales_channel }}
                </ListingDataCell>
                <ListingDataCell>
                    {{ item.total_amount }}
                </ListingDataCell>
                <ListingDataCell>
                    {{ item.total_currency }}
                </ListingDataCell>
                <ListingDataCell>
                    {{ item.payment_method }}
                </ListingDataCell>
                <ListingDataCell>
                    {{ item.buyer_name }}
                </ListingDataCell>
                <ListingDataCell>
                    {{ item.address_line_1 }}
                </ListingDataCell>
                <ListingDataCell>
                    {{ item.address_line_2 }}
                </ListingDataCell>
                <ListingDataCell>
                    {{ item.city }}
                </ListingDataCell>
                <ListingDataCell>
                    {{ item.state }}
                </ListingDataCell>
                <ListingDataCell>
                    {{ item.postal_code }}
                </ListingDataCell>
                <ListingDataCell>
                    {{ item.country_code }}
                </ListingDataCell>
                <ListingDataCell>
                    {{
                        dayjs(item.expected_ship_date).format(
                            'DD.MM.YYYY HH:mm',
                        )
                    }}
                </ListingDataCell>
                <ListingDataCell>
                    {{ item.is_shipped }}
                </ListingDataCell>
                <ListingDataCell>
                    {{ item.original_object }}
                </ListingDataCell>
                <ListingDataCell>
                    <div class="flex items-center justify-end gap-3">
                        <IconButton
                            :as="Link"
                            :href="route('craftable-pro.orders.edit', item)"
                            variant="ghost"
                            color="gray"
                            :icon="PencilSquareIcon"
                            v-can="'global.order.edit'"
                        />

                        <Modal type="danger">
                            <template #trigger="{ setIsOpen }">
                                <IconButton
                                    @click="() => setIsOpen(true)"
                                    color="gray"
                                    variant="ghost"
                                    :icon="TrashIcon"
                                    v-can="'global.order.destroy'"
                                />
                            </template>

                            <template #title>
                                {{ $t('global', 'Delete Order') }}
                            </template>
                            <template #content>
                                {{
                                    $t(
                                        'global',
                                        'Are you sure you want to delete selected Order? All data will be permanently removed from our servers forever. This action cannot be undone.',
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
                                                    'craftable-pro.orders.destroy',
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
                                    v-can="'global.order.destroy'"
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
import dayjs from 'dayjs';
import type { Order } from './types';

interface Props {
    orders: PaginatedCollection<Order>;
}
defineProps<Props>();
const downloadFile = () => {
    const url = window.location.href.split('?');
    if (url.length > 1) {
        window.location = route('craftable-pro.orders.export', url.pop()).slice(
            0,
            -1,
        );
    } else {
        window.location = route('craftable-pro.orders.export');
    }
};
</script>
