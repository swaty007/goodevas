<template>
  <img
    v-if="isFileImage(file.file_name)"
    :src="file.base64 || file.preview_url || file.original_url"
    :alt="file.custom_properties?.name || file.file_name"
    class="aspect-square w-full rounded object-cover"
  />
    <!-- Video rendering -->
    <template v-else-if="isVideo(file)">
        <div class="aspect-square w-full rounded object-cover relative">
            <video
                controls
                :src="file.original_url"
                class="max-w-full rounded-lg shadow-md absolute inset-x-0 top-0 z-10"
            />
        </div>
    </template>

    <!-- Audio rendering -->
    <template v-else-if="isAudio(file)">
        <div class="aspect-square w-full rounded object-cover relative">
            <audio
                controls
                :src="file.original_url"
                class="max-w-full absolute inset-x-0 top-0 z-10"
            />
        </div>
    </template>
  <div
    v-else
    class="relative flex aspect-square h-full items-center justify-center"
  >
    <DocumentIcon class="w-full max-w-[5rem] stroke-1 text-gray-500 dark:text-gray-300" />
    <span
      class="absolute translate-y-1 font-mono text-sm font-semibold uppercase text-gray-500 dark:text-gray-300"
    >
      {{ getFileExtension(file.file_name) }}
    </span>
  </div>
</template>

<script setup lang="ts">
import { defineProps } from "vue";
import { DocumentIcon } from "@heroicons/vue/24/outline";
import { UploadedFile } from "craftable-pro/types";
import { isFileImage, isVideo, isAudio, getFileExtension } from "craftable-pro/helpers";
import { Media } from "craftable-pro/Pages/Media/types";

interface Props {
  file: UploadedFile & Media;
}

const props = defineProps<Props>();
</script>
