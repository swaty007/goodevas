<template>
  <div class="flex gap-1">
    <div v-if="!isPublished">
      <Modal type="info">
        <template #trigger="{ setIsOpen }">
          <Button
            @click="() => setIsOpen(true)"
            color="gray"
            variant="outline"
            :leftIcon="CheckCircleIcon"
            size="sm"
          >
            {{ $t("craftable-pro", "Publish") }}
          </Button>
        </template>

        <template #title>
          {{ $t("craftable-pro", "Publish") }}
        </template>

        <template #content>
          <DatePicker
            name="published_at"
            v-model="publishLaterForm.published_at"
            :mode="mode"
            :label="$t('craftable-pro', 'Set publish date and time')"
          />
        </template>

        <template #buttons="{ setIsOpen }">
          <Button
            @click.prevent="
              () => {
                action(
                  'patch',
                  updateUrl,
                  {
                    [columnName]: publishLaterForm.published_at,
                  },
                  {
                    onFinish: () => {
                      setIsOpen(false);
                    },
                  }
                );
              }
            "
            color="primary"
          >
            {{
              isScheduledPublish
                ? $t("craftable-pro", "Schedule publishing")
                : $t("craftable-pro", "Publish")
            }}
          </Button>

          <Button
            @click.prevent="() => setIsOpen()"
            color="gray"
            variant="outline"
          >
            {{ $t("craftable-pro", "Cancel") }}
          </Button>
        </template>
      </Modal>
    </div>

    <template v-if="isPublished">
      <ButtonGroup>
        <Modal type="warning">
          <template #trigger="{ setIsOpen }">
            <Tooltip position="top">
              <template #button>
                <Button
                  @click="() => setIsOpen(true)"
                  color="gray"
                  variant="outline"
                  size="sm"
                  class="rounded-r-none"
                >
                  <component
                    :is="isScheduledPublish ? ClockIcon : CheckCircleIconSolid"
                    class="-ml-1 mr-2 h-4 w-4 flex-shrink-0 stroke-2"
                    :class="isScheduledPublish ? '' : 'text-green-500'"
                    aria-hidden="true"
                  />

                  {{
                    mode === "dateTime"
                      ? dayjs(publishedAt).format("DD.MM.YYYY HH:mm")
                      : dayjs(publishedAt).format("DD.MM.YYYY")
                  }}
                </Button>
              </template>
              <template #content>
                {{
                  isScheduledPublish
                    ? $t("craftable-pro", "Reschedule")
                    : $t("craftable-pro", "Change published at")
                }}
              </template>
            </Tooltip>
          </template>

          <template #title>
            {{ $t("craftable-pro", "Edit publish date") }}
          </template>

          <template #content>
            <DatePicker
              name="published_at"
              v-model="publishLaterForm.published_at"
              :mode="mode"
              :label="$t('craftable-pro', 'Set publish date and time')"
            />
          </template>

          <template #buttons="{ setIsOpen }">
            <Button
              @click.prevent="
                () => {
                  action(
                    'patch',
                    updateUrl,
                    {
                      [columnName]: publishLaterForm.published_at,
                    },
                    {
                      onFinish: () => {
                        setIsOpen(false);
                      },
                    }
                  );
                }
              "
              color="primary"
            >
              {{
                isScheduledPublish
                  ? $t("craftable-pro", "Schedule publishing")
                  : $t("craftable-pro", "Publish")
              }}
            </Button>

            <Button
              @click.prevent="() => setIsOpen()"
              color="gray"
              variant="outline"
            >
              {{ $t("craftable-pro", "Cancel") }}
            </Button>
          </template>
        </Modal>

        <Modal type="danger">
          <template #trigger="{ setIsOpen }">
            <Tooltip position="top">
              <template #button>
                <IconButton
                  @click="() => setIsOpen(true)"
                  :icon="XMarkIcon"
                  color="gray"
                  variant="outline"
                  class="-ml-px rounded-l-none stroke-2"
                  size="sm"
                />
              </template>
              <template #content>
                {{
                  isScheduledPublish
                    ? $t("craftable-pro", "Cancel schedulement")
                    : $t("craftable-pro", "Unpublish")
                }}
              </template>
            </Tooltip>
          </template>

          <template #title>
            {{ $t("craftable-pro", "Unpublish") }}
          </template>

          <template #content>
            {{ $t("craftable-pro", "Are you sure you want to unpublish?") }}
          </template>

          <template #buttons="{ setIsOpen }">
            <Button
              @click.prevent="
                () => {
                  action(
                    'patch',
                    updateUrl,
                    {
                      [columnName]: null,
                    },
                    {
                      onFinish: () => {
                        setIsOpen(false);
                      },
                    }
                  );
                }
              "
              color="danger"
            >
              {{ $t("craftable-pro", "Unpublish") }}
            </Button>

            <Button
              @click.prevent="() => setIsOpen()"
              color="gray"
              variant="outline"
            >
              {{ $t("craftable-pro", "Cancel") }}
            </Button>
          </template>
        </Modal>
      </ButtonGroup>
    </template>
  </div>
</template>

<script setup lang="ts">
import {
  Button,
  Modal,
  DatePicker,
  IconButton,
  ButtonGroup,
  Tooltip,
} from "craftable-pro/Components";
import {
  CheckCircleIcon,
  ClockIcon,
  XMarkIcon,
} from "@heroicons/vue/24/outline";
import { CheckCircleIcon as CheckCircleIconSolid } from "@heroicons/vue/24/solid";
import { useAction } from "craftable-pro/hooks/useAction";
import { computed, watch } from "vue";
import dayjs from "dayjs";
import { useForm } from "@inertiajs/vue3";
import type { DatePickerMode } from "../types";

const { action } = useAction();

interface Props {
  publishedAt: string | null;
  updateUrl: string;
  columnName: string;
  mode?: DatePickerMode;
}

const props = withDefaults(defineProps<Props>(), {
  mode: "dateTime",
});

const isPublished = computed(() => {
  return props.publishedAt;
});

const isScheduledPublish = computed(() => {
  if (publishLaterForm.published_at) {
    return dayjs(publishLaterForm.published_at).isAfter(dayjs());
  }

  return false;
});

const publishLaterForm = useForm({
  published_at: props.publishedAt ?? dayjs(),
});
</script>
