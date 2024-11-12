<template>
  <PageHeader
    sticky
    :title="$t('global', 'Edit Warehouse')"
    :subtitle="`Last updated at ${dayjs(
      warehouse.updated_at
    ).format('DD.MM.YYYY')}`"
  >
    <Button
      :leftIcon="ArrowDownTrayIcon"
      @click="submit"
      :loading="form.processing"
      v-can="'global.warehouse.edit'"
    >
      {{ $t("global", "Save") }}
    </Button>
  </PageHeader>

  <Form :form="form" :submit="submit"  />
</template>

<script setup lang="ts">
import { ArrowDownTrayIcon } from "@heroicons/vue/24/outline";
import { PageHeader, Button } from "craftable-pro/Components";
import { useForm } from "craftable-pro/hooks/useForm";
import Form from "./Form.vue";
import type { Warehouse, WarehouseForm } from "./types";
import dayjs from "dayjs";




interface Props {
  warehouse: Warehouse;

}

const props = defineProps<Props>();

const { form, submit } = useForm<WarehouseForm>(
    {
          name: props.warehouse?.name ?? "",
country_id: props.warehouse?.country_id ?? "",
        virtual: props.warehouse?.country_id ?? "",
    },
    route("craftable-pro.warehouses.update", [props.warehouse?.id])
);
</script>
