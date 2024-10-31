<template>
  <Card :title="title">
    <div class="grid grid-cols-6 gap-6">
      <slot></slot>
      <TextInput
        v-model="form.password"
        name="password"
        :label="$t('craftable-pro', 'Password')"
        type="password"
        autocomplete="new-password"
        class="col-span-6 sm:col-span-3 sm:col-start-1"
      />
      <TextInput
        v-model="form.password_confirmation"
        name="password_confirmation"
        :label="$t('craftable-pro', 'Password confirmation')"
        type="password"
        autocomplete="new-password"
        class="col-span-6 sm:col-span-3 sm:col-start-1"
      />
    </div>
  </Card>
</template>

<script setup lang="ts">
import { Card, TextInput } from "craftable-pro/Components";
import { InertiaForm } from "@inertiajs/vue3";
import type { CraftableProUserPasswordForm } from "../types";
import { wTrans } from "craftable-pro/plugins/laravel-vue-i18n";

interface Props {
  form: InertiaForm<CraftableProUserPasswordForm>;
  title?: string;
}

const props = withDefaults(defineProps<Props>(), {
  title: wTrans("craftable-pro", "Password").value,
});
</script>
