<template>
    <Modal type="info">
        <template #trigger="{ setIsOpen }">
            <Button
                v-can="'global.order.edit'"
                :leftIcon="PlusIcon"
                color="warning"
                class="absolute"
                @click="() => setIsOpen(true)"
            >
                {{ $t("global", "Update") }}
            </Button>
        </template>

        <template #title>
            {{ $t('global', 'Bulk Edit User') }}
        </template>
        <template #content>
            {{
                $t(
                    'craftable-pro',
                    'Are you sure you want to proceed with the bulk update? This action will make extensive changes to the data. Please ensure that the update settings are correct, as this operation can be undone if necessary',
                )
            }}

            <div class="space-y-4">
                <Multiselect
                    :model-value="form.order_status"
                    name="order_status"
                    :options="orderStatuses"
                    :allow-absent="true"
                    mode="single"
                    :label="$t('global', 'Order Status')"
                />
                <Multiselect
                    :model-value="form.fulfillment_status"
                    name="fulfillment_status"
                    :options="fulfillmentStatuses"
                    :allow-absent="true"
                    mode="single"
                    :label="$t('global', 'Fulfilment Status')"
                />
                <Multiselect
                    :model-value="form.refund_status"
                    name="refund_status"
                    :options="refundStatuses"
                    :allow-absent="true"
                    mode="single"
                    :label="$t('global', 'Refund Status')"
                />
            </div>

        </template>

        <template #buttons="{ setIsOpen }">
            <Button
                v-can="'global.order.edit'"
                :left-icon="ArrowDownTrayIcon"
                :loading="form.processing"
                @click="() => {
                    submit()
                    setIsOpen()
                    emit('submit')
                }"
            >
                {{ $t('global', 'Save') }}
            </Button>
            <Button
                color="gray"
                variant="outline"
                @click.prevent="() => setIsOpen()"
            >
                {{ $t('global', 'Cancel') }}
            </Button>
        </template>
    </Modal>
</template>

<script setup lang="ts">
import { ArrowDownTrayIcon, PlusIcon } from "@heroicons/vue/24/outline";
import { Button, Modal, Multiselect } from "craftable-pro/Components";
import { useForm } from "craftable-pro/hooks/useForm";
import { defineEmits, watch } from "vue";


interface Props {
    ids: string[] | number[]
    orderStatuses: string[];
    fulfillmentStatuses: string[];
    refundStatuses: string[];
}

const props = defineProps<Props>();

const emit = defineEmits(["submit"]);

const {form, submit} = useForm(
    {
        ids: props.ids,
        order_status: "",
        fulfillment_status: "",
        refund_status: "",
    },
    route("craftable-pro.orders.bulk-update"),
    "post"
);

watch(() => props.ids, (newIds) => {
    form.ids = newIds;
}, { immediate: true });
</script>
