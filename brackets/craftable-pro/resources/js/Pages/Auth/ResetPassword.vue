<template>
  <div>
    <Head :title="$t('craftable-pro', 'Reset password')" />

    <div class="bg-white dark:bg-gray-700 py-8 px-4 shadow sm:rounded-lg sm:px-10">
      <form class="space-y-6" @submit.prevent="submit">
        <TextInput
          v-model="form.email"
          :label="$t('craftable-pro', 'E-mail address')"
          name="email"
        />

        <TextInput
          v-model="form.password"
          :label="$t('craftable-pro', 'Password')"
          name="password"
          type="password"
          autocomplete="new-password"
        />

        <TextInput
          v-model="form.password_confirmation"
          :label="$t('craftable-pro', 'Confirm Password')"
          name="password_confirmation"
          type="password"
          autocomplete="new-password"
        />

        <Button class="w-full" type="submit" :disabled="form.processing">
          {{ $t("craftable-pro", "Reset Password") }}
        </Button>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useForm, Head } from "@inertiajs/vue3";
import { Button, TextInput } from "craftable-pro/Components";

interface Props {
  email: string;
  token: string;
}

const props = defineProps<Props>();

const form = useForm({
  token: props.token,
  email: props.email,
  password: "",
  password_confirmation: "",
});

const submit = () => {
  form.post(route("craftable-pro.password.update"), {
    onFinish: () => form.reset("password", "password_confirmation"),
  });
};
</script>
