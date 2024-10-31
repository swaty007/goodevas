<template>
  <div>
    <Head :title="$t('craftable-pro', 'Login')" />

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

        <TextInput
          v-model="form.password"
          :label="$t('craftable-pro', 'Password')"
          name="password"
          type="password"
          autocomplete="current-password"
        />

        <div class="flex items-center justify-between">
          <Checkbox
            v-model="form.remember"
            :label="$t('craftable-pro', 'Remember me')"
            name="remember-me"
          />

          <div v-if="canResetPassword" class="text-sm">
            <Link
              :href="route('craftable-pro.password.request')"
              class="font-medium text-primary-600 hover:text-primary-500"
            >
              {{ $t("craftable-pro", "Forgot your password?") }}
            </Link>
          </div>
        </div>

        <Button class="w-full" type="submit" :disabled="form.processing">
          {{ $t("craftable-pro", "Sign in") }}
        </Button>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useForm, Head } from "@inertiajs/vue3";
import { Button, TextInput, Checkbox, Alert } from "craftable-pro/Components";
import { defineEmits } from "vue";

interface Props {
  canResetPassword: boolean;
  status: string;
}

defineProps<Props>();

const emit = defineEmits(["login"]);
const form = useForm({
  email: "",
  password: "",
  remember: false,
});

const submit = () => {
  form.post(route("craftable-pro.login"), {
      onFinish: () => form.reset("password"),
      onSuccess: () => {
        emit('login')
    },
  });
};
</script>
