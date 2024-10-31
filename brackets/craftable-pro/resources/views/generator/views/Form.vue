<template>
  <PageContent>
    <div class="mx-auto max-w-3xl 2xl:max-w-4xl">
      [[translatableLocaleSwitcher]]

      <Card>
        <div class="space-y-4">[[modelFullForm]]</div>
      </Card>
    </div>
  </PageContent>
</template>

<script setup lang="ts">
import {
    Card,
    TextInput,
    Wysiwyg,
    TextArea,
    PageContent,
    DatePicker,
    Checkbox,
    Dropzone,
    ImageUpload,
    Multiselect,
    CardLocaleSwitcher
} from "craftable-pro/Components";
import { InertiaForm } from "craftable-pro/types";
import type { [[modelIndexName]]Form } from "./types";

[[translatableFunctionality]]

interface Props {
  form: InertiaForm<[[modelIndexName]]Form>;
  submit: void;
  [[relationsProps]]
}

const props = defineProps<Props>();
</script>
