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

  <Form :form="form" :submit="submit"  />
</template>

<script setup lang="ts">
import { ArrowDownTrayIcon } from "@heroicons/vue/24/outline";
import { PageHeader, Button } from "craftable-pro/Components";
import { useForm } from "craftable-pro/hooks/useForm";
import Form from "./Form.vue";
import type { WarehouseForm } from "./types";



interface Props {
  
}

const props = defineProps<Props>();

const { form, submit } = useForm<WarehouseForm>(
    {
          name: "", 
country_id: ""
    },
    route("craftable-pro.warehouses.store"),
    "post"
);
</script>
