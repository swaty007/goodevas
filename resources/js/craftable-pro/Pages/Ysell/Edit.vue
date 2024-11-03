<template>
  <PageHeader
    sticky
    :title="$t('global', 'Edit Ysell')"
    :subtitle="`Last updated at ${dayjs(
      ysell.updated_at
    ).format('DD.MM.YYYY')}`"
  >
    <Button
      :leftIcon="ArrowDownTrayIcon"
      @click="submit"
      :loading="form.processing"
      v-can="'global.ysell.edit'"
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
import type { Ysell, YsellForm } from "./types";
import dayjs from "dayjs";




interface Props {
  ysell: Ysell;
  
}

const props = defineProps<Props>();

const { form, submit } = useForm<YsellForm>(
    {
          api_key: props.ysell?.api_key ?? "", 
name: props.ysell?.name ?? ""
    },
    route("craftable-pro.ysells.update", [props.ysell?.id])
);
</script>
