<template>
  <div>
    <Head :title="$t('craftable-pro', 'Reset password')" />

    <div
      class="bg-white dark:bg-gray-700 py-8 px-4 shadow sm:rounded-lg sm:px-10"
      v-auto-animate
    >
      <Alert v-if="status" type="info" class="mb-6">
        {{ status }}
      </Alert>

      <form class="space-y-6" @submit.prevent="submit">
        <TextInput
          v-model="form.email"
          :label="$t('craftable-pro', 'E-mail address')"
          name="email"
        />

        <Button class="w-full" type="submit" :disabled="form.processing">
          {{ $t("craftable-pro", "Email Password Reset Link") }}
        </Button>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useForm, Head } from "@inertiajs/vue3";
import { Button, TextInput, Alert } from "craftable-pro/Components";

interface Props {
  status: string;
}

defineProps<Props>();

const form = useForm({
  email: "",
});

const submit = () => {
  form.post(route("craftable-pro.password.email"));
};
</script>
