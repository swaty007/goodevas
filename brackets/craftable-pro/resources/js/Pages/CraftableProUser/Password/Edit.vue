<template>
  <PageHeader sticky :title="$t('craftable-pro', 'Password')">
    <div class="flex gap-3">
      <Button
        :leftIcon="ArrowDownTrayIcon"
        @click="submit"
        :loading="form.processing"
      >
        {{ $t("craftable-pro", "Save") }}
      </Button>
    </div>
  </PageHeader>

  <PageContent>
    <div class="mx-auto flex max-w-3xl flex-col gap-6 2xl:max-w-4xl">
      <PasswordCard :form="form" />
    </div>
  </PageContent>
</template>

<script setup lang="ts">
import { ArrowDownTrayIcon } from "@heroicons/vue/24/outline";
import { PageHeader, PageContent, Button } from "craftable-pro/Components";
import type { CraftableProUser } from "craftable-pro/types/models";
import { useForm } from "craftable-pro/hooks/useForm";
import PasswordCard from "../Components/PasswordCard.vue";

interface Props {
  craftableProUser: CraftableProUser;
}

const props = defineProps<Props>();

const { form, submit } = useForm(
  {
    password: "",
    password_confirmation: "",
  },
  route("craftable-pro.craftable-pro-users.password.update")
);
</script>
