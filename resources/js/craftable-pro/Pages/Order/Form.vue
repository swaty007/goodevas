<template>
    <PageContent>
        <div class="mx-auto max-w-3xl 2xl:max-w-4xl">
            <Card>
                <div class="space-y-4">
                    <TextInput
                        v-model="form.order_id"
                        name="order_id"
                        :label="$t('global', 'Order Id')"
                    />

                    <TextInput
                        v-model="form.type"
                        name="type"
                        :label="$t('global', 'Type')"
                    />

                    <TextInput
                        v-model="form.order_date"
                        name="order_date"
                        :label="$t('global', 'Order Date')"
                    />

                    <TextInput
                        v-model="form.update_date"
                        name="update_date"
                        :label="$t('global', 'Update Date')"
                    />

                    <TextInput
                        v-model="form.order_status"
                        name="order_status"
                        :label="$t('global', 'Order Status')"
                    />

                    <TextInput
                        v-model="form.fulfillment"
                        name="fulfillment"
                        :label="$t('global', 'Fulfillment')"
                    />

                    <TextInput
                        v-model="form.sales_channel"
                        name="sales_channel"
                        :label="$t('global', 'Sales Channel')"
                    />

                    <TextInput
                        v-model="form.total_amount"
                        name="total_amount"
                        :label="$t('global', 'Total Amount')"
                    />

                    <TextInput
                        v-model="form.total_currency"
                        name="total_currency"
                        :label="$t('global', 'Total Currency')"
                    />

                    <TextInput
                        v-model="form.payment_method"
                        name="payment_method"
                        :label="$t('global', 'Payment Method')"
                    />

                    <TextInput
                        v-model="form.buyer_name"
                        name="buyer_name"
                        :label="$t('global', 'Buyer Name')"
                    />

                    <TextInput
                        v-model="form.address_line_1"
                        name="address_line_1"
                        :label="$t('global', 'Address Line 1')"
                    />

                    <TextInput
                        v-model="form.address_line_2"
                        name="address_line_2"
                        :label="$t('global', 'Address Line 2')"
                    />

                    <TextInput
                        v-model="form.city"
                        name="city"
                        :label="$t('global', 'City')"
                    />

                    <TextInput
                        v-model="form.state"
                        name="state"
                        :label="$t('global', 'State')"
                    />

                    <TextInput
                        v-model="form.postal_code"
                        name="postal_code"
                        :label="$t('global', 'Postal Code')"
                    />

                    <TextInput
                        v-model="form.country_code"
                        name="country_code"
                        :label="$t('global', 'Country Code')"
                    />

                    <TextInput
                        v-model="form.expected_ship_date"
                        name="expected_ship_date"
                        :label="$t('global', 'Expected Ship Date')"
                    />

                    <TextInput
                        v-model="form.is_shipped"
                        name="is_shipped"
                        :label="$t('global', 'Is Shipped')"
                    />

                    <Multiselect
                        v-model="form.orderItems_ids"
                        name="orderItems_ids"
                        :label="$t('global', 'OrderItems')"
                        :options="orderItemsOptions"
                    />

                    <pre>{{ form.original_object }}</pre>
                </div>
            </Card>
        </div>
    </PageContent>
</template>

<script setup lang="ts">
import {
    Card,
    Multiselect,
    PageContent,
    TextInput,
} from 'craftable-pro/Components';
import { InertiaForm } from 'craftable-pro/types';
import type { OrderForm } from './types';

interface Props {
    form: InertiaForm<OrderForm>;
    submit: void;
    orderItemsOptions: Array<{ value: string | number; label: string }>;
}

const props = defineProps<Props>();
</script>
