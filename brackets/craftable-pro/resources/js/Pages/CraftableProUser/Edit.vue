<template>
  <PageHeader
    sticky
    :title="$t('craftable-pro', 'Edit user')"
    :subtitle="`Last updated at ${dayjs(craftableProUser.updated_at).format(
      'DD.MM.YYYY HH:mm'
    )}`"
  >
    <Button
      :leftIcon="ArrowDownTrayIcon"
      @click="submit"
      :loading="form.processing"
    >
      {{ $t("craftable-pro", "Save") }}
    </Button>
  </PageHeader>

  <PageContent>
    <Form
      :locales="locales"
      :form="form"
      :craftableProUser="craftableProUser"
      :submit="submit"
      :roles="roles"
    />
  </PageContent>
</template>

<script setup lang="ts">
import { ArrowDownTrayIcon } from "@heroicons/vue/24/outline";
import { PageHeader, PageContent, Button } from "craftable-pro/Components";
import { useForm } from "craftable-pro/hooks/useForm";
import Form from "./Form.vue";
import type { CraftableProUser } from "craftable-pro/types/models";
import type { CraftableProUserForm } from "./types";
import dayjs from "dayjs";
import type { UploadedFile } from "../../types";
import type { Role } from "../../types/models";

interface Props {
  craftableProUser: CraftableProUser;
  avatar: UploadedFile[];
  roles: Role[];
  locales: string[];
}

const props = defineProps<Props>();

const { form, submit } = useForm<CraftableProUserForm>(
  {
    first_name: props.craftableProUser.first_name ?? "",
    last_name: props.craftableProUser.last_name ?? "",
    email: props.craftableProUser.email ?? "",
    password: "",
    password_confirmation: "",
    locale: props.craftableProUser.locale ?? "",
    active: props.craftableProUser.active ?? false,
    role_id: props.craftableProUser.roles
      ? props.craftableProUser.roles?.[0]?.id
      : null,
    avatar: props.craftableProUser.avatar ?? [],
  },
  route("craftable-pro.craftable-pro-users.update", [props.craftableProUser.id])
);
</script>
