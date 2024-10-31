<template>
  <Modal class="flex items-center justify-center">
    <template #trigger="{ setIsOpen }">
      <ToolbarButton
        @click="
          () => {
            form.url = editor?.getAttributes('link').href;
            setIsOpen(true);
          }
        "
        :active="editor?.isActive('link')"
        :icon="LinkIcon"
      />
    </template>
    <template #title> {{ $t("craftable-pro", "Add link") }} </template>
    <template #content>
      <TextInput
        name="link_url"
        :label="$t('craftable-pro', 'URL')"
        v-model="form.url"
      />
    </template>
    <template #buttons="{ setIsOpen }">
      <Button
        @click="
          () => {
            setIsOpen(false);
            submit();
          }
        "
      >
        {{ $t("craftable-pro", "Submit") }}
      </Button>
      <Button variant="outline" @click="() => setIsOpen(false)">
        {{ $t("craftable-pro", "Cancel") }}
      </Button>
    </template>
  </Modal>
</template>
<script setup lang="ts">
import { defineEmits, watch } from "vue";
import ToolbarButton from "./ToolbarButton.vue";
import { Modal, Button, TextInput } from "craftable-pro/Components";
import { useForm } from "@inertiajs/vue3";
import { LinkIcon } from "./icons";
import { Editor } from "@tiptap/vue-3";

const emit = defineEmits(["linkAdded"]);

interface Props {
  editor?: Editor;
}

const props = defineProps<Props>();

const form = useForm({
  url: "",
});

const submit = () => {
  emit("linkAdded", form.url);
  form.url = "";
};
</script>
