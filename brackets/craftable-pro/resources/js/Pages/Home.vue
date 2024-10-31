<template>
  <PageContent>
    <div
      class="flex h-96 items-center justify-center rounded-lg border-4 border-dashed border-gray-200 dark:border-gray-600 p-4"
    >
      <span class="text-center text-xl italic text-gray-300">
        {{ $t("craftable-pro", "Welcome to Craftable PRO") }}
      </span>
    </div>
  </PageContent>
</template>

<script setup lang="ts">
import { PageContent } from "craftable-pro/Components";
import { useToast } from "@brackets/vue-toastification";
import { trans, transChoice } from "craftable-pro/plugins/laravel-vue-i18n";

const toast = useToast();

toast(trans("craftable-pro", "I'm a :type toast!", { type: "default" }));
toast.info(trans("craftable-pro", "I'm a :type toast!", { type: "info" }), {
  description: transChoice(
    "craftable-pro",
    "{1} :count minute ago|[2,*] :count minutes ago",
    10
  ),
});
toast.warning(
  trans("craftable-pro", "I'm a :type toast!", { type: "warning" })
);
toast.error(trans("craftable-pro", "I'm a :type toast!", { type: "error" }));
toast.success(
  trans("craftable-pro", "I'm a :type toast!", { type: "success" }),
  {
    description: "...with an optional description!",
  }
);
</script>
