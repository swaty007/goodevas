<template>
  <div>
    <Head :title="$t('craftable-pro', 'Confirm password')" />

    <div class="bg-white dark:bg-gray-700 py-8 px-4 shadow sm:rounded-lg sm:px-10">
      <p class="mb-6 text-center text-sm text-gray-600">
        {{
          $t(
            "craftable-pro",
            "This is a secure area of the application. Please confirm your password before continuing."
          )
        }}
      </p>

      <form class="space-y-6" @submit.prevent="submit">
        <TextInput
          v-model="form.password"
          :label="$t('craftable-pro', 'Password')"
          name="password"
          type="password"
          autocomplete="current-password"
        />

        <Button class="w-full" type="submit" :disabled="form.processing">
          {{ $t("craftable-pro", "Confirm") }}
        </Button>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useForm, Head } from "@inertiajs/vue3";
import { Button, TextInput } from "craftable-pro/Components";

const form = useForm({
  password: "",
});

const submit = () => {
  form.post(route("craftable-pro.password.confirm"), {
    onFinish: () => form.reset(),
  });
};
</script>
