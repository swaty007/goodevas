<template>
    <PageHeader sticky :title="$t('global', 'Create Warehouse')">
        <Button
            :leftIcon="ArrowDownTrayIcon"
            @click="submit"
            :loading="form.processing"
            v-can="'global.warehouse.create'"
        >
            {{ $t("global", "Save") }}
        </Button>
    </PageHeader>

    <Form :form="form" :submit="submit" :defaultSettings="defaultSettings"/>
</template>

<script setup lang="ts">
import { ArrowDownTrayIcon } from "@heroicons/vue/24/outline";
import { Button, PageHeader } from "craftable-pro/Components";
import { useForm } from "craftable-pro/hooks/useForm";
import Form from "./Form.vue";
import { WarehouseForm, WarehouseSettings } from "./types";


interface Props {
    defaultSettings: WarehouseSettings
}

const props = defineProps<Props>();

const {form, submit} = useForm<WarehouseForm>(
    {
        name: "",
        country_id: "",
        virtual: false,
        ysell_name: "",
        settings: {
            ranges: props.defaultSettings.ranges
        },
    },
    route("craftable-pro.warehouses.store"),
    "post"
);
</script>
