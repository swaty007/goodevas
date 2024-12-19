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
            <template #actions>
                <div class="gap-5 md:flex">
                    <FiltersDropdown
                        :active-filters-count="activeFiltersCount"
                        :reset-filters="resetFilters"
                    >
                        <div
                            class="gap-4 space-y-1 sm:w-screen sm:max-w-xl sm:columns-2 dark:text-gray-100"
                        >
                            <!--                            <div class="shadow rounded-lg cursor-move bg-gray-200 dark:bg-gray-600 px-2"> </div>-->
                            <template
                                v-for="(filter, index) in filtersOptions"
                                :key="index"
                            >
                                <Multiselect
                                    v-if="filtersOptions[index]?.length"
                                    v-model="filtersForm[index]"
                                    :name="index"
                                    :label="index"
                                    :options="filtersOptions[index]"
                                    :can-clear="true"
                                />
                                <!--                                    :mode="Array.isArray(filtersForm[filter]) ? 'multiple' : 'single'"-->
                            </template>
                            <div class="flex">
                                <DatePicker
                                    v-model="filtersForm.start_date"
                                    name="start_date"
                                    :left-icon="null"
                                    mode="dateTime"
                                    :label="$t('global', 'Start Date')"
                                />
                                <DatePicker
                                    v-model="filtersForm.end_date"
                                    name="end_date"
                                    :left-icon="null"
                                    mode="dateTime"
                                    :label="$t('global', 'End Date')"
                                />
                            </div>
                        </div>
                    </FiltersDropdown>
                </div>
            </template>
            <template #bulkActions="{ bulkAction, bulkActionForm }">
                <ModalBulkEdit
                    :ids="bulkActionForm.ids"
                    :order-statuses="orderStatuses"
                    :fulfillment-statuses="fulfillmentStatuses"
                    :refund-statuses="refundStatuses" />
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
                <ListingHeaderCell v-width-dragging>
                    {{ $t('global', 'Api Key') }}
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
                <ListingHeaderCell v-width-dragging sortBy="fulfillment_status">
                    {{ $t('global', 'Fulfilment Status') }}
                </ListingHeaderCell>
                <ListingHeaderCell v-width-dragging sortBy="refund_status">
                    {{ $t('global', 'Refund Status') }}
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
                    {{ $t('global', 'Items') }}
                </ListingHeaderCell>
<!--                <ListingHeaderCell v-width-dragging>-->
<!--                    {{ $t('global', 'Original Object') }}-->
<!--                </ListingHeaderCell>-->
                <ListingHeaderCell v-width-dragging>
                    <span class="sr-only">{{ $t('global', 'Actions') }}</span>
                </ListingHeaderCell>
            </template>
            <template #tableRow="{ item, action }: { item: Order}">
                <ListingDataCell>
                    <Modal type="info" size="lg">
                        <template #trigger="{ setIsOpen }">
                            <IconButton
                                @click="() => setIsOpen(true)"
                                color="gray"
                                variant="ghost"
                                :icon="EyeIcon"
                            />
                        </template>

                        <template #title>
                            {{ $t('global', 'Show Order') }}
                        </template>
                        <template #content>
                            <pre>
                                {{ item.original_object }}
                            </pre>
                        </template>

                        <template #buttons="{ setIsOpen }">
                            <Button
                                @click.prevent="() => setIsOpen()"
                                color="gray"
                                variant="outline"
                            >
                                {{ $t('global', 'Cancel') }}
                            </Button>
                        </template>
                    </Modal>
                </ListingDataCell>
                <ListingDataCell>
                    {{ item?.api_key?.name }}
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
<!--                    {{ item.order_status }}-->
                    <Multiselect
                        :model-value="item.order_status"
                        name="order_status"
                        :options="orderStatuses"
                        :allow-absent="true"
                        mode="single"
                        @update:model-value="updateOrder(item, 'order_status', $event)"
                    />
                </ListingDataCell>
                <ListingDataCell>
<!--                    {{  item.fulfillment_status }}-->
                    <Multiselect
                        :model-value="item.fulfillment_status"
                        name="fulfillment_status"
                        :options="fulfillmentStatuses"
                        :allow-absent="true"
                        mode="single"
                        @update:model-value="updateOrder(item, 'fulfillment_status', $event)"
                    />
                </ListingDataCell>
                <ListingDataCell>
<!--                    {{ item.refund_status }}-->
                    <Multiselect
                        :model-value="item.refund_status"
                        name="refund_status"
                        :options="refundStatuses"
                        :allow-absent="true"
                        mode="single"
                        @update:model-value="updateOrder(item, 'refund_status', $event)"
                    />
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
                    <div v-for="item in item.items" :key="item.id" class="">
                        SKU: {{ item.sku }} Barcode: {{ item.barcode }} Quantity:{{ item.quantity }}
                        <p class="text-nowrap inline-block overflow-auto max-w-64">{{ item.title }}</p>
                    </div>
                </ListingDataCell>
<!--                <ListingDataCell>-->
<!--                    {{ item.original_object }}-->
<!--                </ListingDataCell>-->
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
    EyeIcon,
} from '@heroicons/vue/24/outline';
import { Link, usePage } from '@inertiajs/vue3';
import {
    Button,
    DatePicker,
    IconButton,
    Listing,
    ListingDataCell,
    ListingHeaderCell,
    Modal,
    Multiselect,
    PageContent,
    PageHeader,
    FiltersDropdown,
} from 'craftable-pro/Components';
import { useListingFilters } from 'craftable-pro/hooks/useListingFilters';
import { PaginatedCollection } from 'craftable-pro/types/pagination';
import dayjs from 'dayjs';
import type { Order } from './types';
import debounce from "lodash/debounce";
import ModalBulkEdit from "@/craftable-pro/Pages/Order/_ModalBulkEdit.vue";

interface Props {
    orders: PaginatedCollection<Order>;
    filtersOptions: Array<{ value: string | number; label: string }>;
    orderStatuses: string[];
    fulfillmentStatuses: string[];
    refundStatuses: string[];
}
defineProps<Props>();

const { filtersForm, resetFilters, activeFiltersCount } = useListingFilters(
    route('craftable-pro.orders.index'),
    {
        order_status: (usePage().props as unknown as PageProps).filter?.order_status ?? [],
        fulfillment_status: (usePage().props as unknown as PageProps).filter?.fulfillment_status ?? [],
        refund_status: (usePage().props as unknown as PageProps).filter?.refund_status ?? [],
        type: (usePage().props as unknown as PageProps).filter?.type ?? [],
        fulfillment: (usePage().props as unknown as PageProps).filter?.fulfillment ?? [],
        sales_channel: (usePage().props as unknown as PageProps).filter?.sales_channel ?? [],
        total_currency: (usePage().props as unknown as PageProps).filter?.total_currency ?? [],
        payment_method: (usePage().props as unknown as PageProps).filter?.payment_method ?? [],
        state: (usePage().props as unknown as PageProps).filter?.state ?? [],
        country_code: (usePage().props as unknown as PageProps).filter?.country_code ?? [],
        is_shipped: (usePage().props as unknown as PageProps).filter?.is_shipped ?? [],
        start_date: (usePage().props as unknown as PageProps).filter?.start_date ?? '',
        end_date: (usePage().props as unknown as PageProps).filter?.end_date ?? '',
    },
);
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

const updateOrder = debounce((item: Order, name: string, value: Array<string | number>) => {
    action('post', route('craftable-pro.orders.update', item.id), {
        [name]: value
    })
}, 1000)
</script>
