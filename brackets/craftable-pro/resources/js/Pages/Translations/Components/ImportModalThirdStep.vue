<template>
  <p class="text-sm font-medium text-gray-700 dark:text-gray-200">
    {{
      $t("craftable-pro", ":count successfully imported", {
        count: numberOfSuccessfullyImportedTranslations.toString(),
      })
    }}
    {{
      $t("craftable-pro", ":count successfully updated", {
        count: numberOfSuccessfullyUpdatedTranslations.toString(),
      })
    }}
  </p>
</template>

<script lang="ts" setup>
interface Props {
  numberOfSuccessfullyImportedTranslations: number;
  numberOfSuccessfullyUpdatedTranslations: number;
}

const props = defineProps<Props>();
</script>
