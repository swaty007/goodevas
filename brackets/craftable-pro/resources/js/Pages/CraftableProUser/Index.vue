<template>
    <PageHeader :title="$t('craftable-pro', 'Users')">
        <Modal
            align-buttons="right"
            size="sm"
        >
            <template #trigger="{ setIsOpen }">
                <Button
                    :left-icon="PlusIcon"
                    @click="() => setIsOpen(true)"
                >
                    {{ $t("craftable-pro", "Invite user") }}
                </Button>
            </template>
            <template #title>
                {{ $t("craftable-pro", "Invite user") }}
            </template>
            <template #content>
                <div class="mt-4 flex flex-col gap-2">
                    <TextInput
                        v-model="form.email"
                        name="email"
                        :label="$t('craftable-pro', 'Email')"
                    />
                    <Multiselect
                        v-model="form.role_id"
                        name="role"
                        :label="$t('craftable-pro', 'Role')"
                        mode="single"
                        :options="filterOptions.roles"
                        options-value-prop="id"
                        options-label="name"
                    />
                </div>
            </template>
            <template #buttons="{ setIsOpen }">
                <Button
                    size="sm"
                    :loading="form.processing"
                    @click="submit(setIsOpen)"
                >
                    {{ $t("craftable-pro", "Invite user") }}
                </Button>
                <Button
                    size="sm"
                    color="gray"
                    variant="outline"
                    @click.prevent="() => setIsOpen()"
                >
                    {{ $t("craftable-pro", "Cancel") }}
                </Button>
            </template>
        </Modal>
    </PageHeader>

    <PageContent>
        <Listing
            :base-url="route('craftable-pro.craftable-pro-users.index')"
            :data="craftableProUsers"
            data-key="craftableProUsers"
        >
            <template #actions>
                <FiltersDropdown
                    :active-filters-count="activeFiltersCount"
                    :reset-filters="resetFilters"
                >
                    <Multiselect
                        v-model="filtersForm.role"
                        name="role"
                        :label="$t('craftable-pro', 'Role')"
                        :options="filterOptions.roles"
                    />
                    <Multiselect
                        v-model="filtersForm.status"
                        name="status"
                        :label="$t('craftable-pro', 'Status')"
                        :options="statusOptions"
                        options-value-prop="id"
                        options-label="label"
                        mode="single"
                    />
                </FiltersDropdown>
            </template>

            <template #bulkActions="{ baseUrl, bulkAction }">
                <!-- TODO: there was some kind of an idea to soft/force destroy? -->
                <Button
                    v-can="'craftable-pro.craftable-pro-user.edit'"
                    color="gray"
                    variant="outline"
                    size="sm"
                    :left-icon="ShieldCheckIcon"
                    @click="() => bulkAction('post', `${baseUrl}/bulk-activate`)"
                >
                    {{ $t("craftable-pro", "Activate") }}
                </Button>

                <Modal
                    v-can="'craftable-pro.craftable-pro-user.destroy'"
                    type="danger"
                >
                    <template #trigger="{ setIsOpen }">
                        <Button
                            color="gray"
                            variant="outline"
                            size="sm"
                            :left-icon="NoSymbolIcon"
                            @click="setIsOpen(true)"
                        >
                            {{ $t("craftable-pro", "Deactivate") }}
                        </Button>
                    </template>

                    <template #title>
                        {{ $t("craftable-pro", "Deactivate users") }}
                    </template>

                    <template #content>
                        {{
                            $t(
                                "craftable-pro",
                                "Are you sure you want to deactivate selected users?"
                            )
                        }}
                    </template>

                    <template #buttons="{ setIsOpen }">
                        <Button
                            color="danger"
                            @click.prevent="
                                () => bulkAction('post', `${baseUrl}/bulk-deactivate`)
                            "
                        >
                            {{ $t("craftable-pro", "Deactivate") }}
                        </Button>
                        <Button
                            color="gray"
                            variant="outline"
                            @click.prevent="() => setIsOpen()"
                        >
                            {{ $t("craftable-pro", "Cancel") }}
                        </Button>
                    </template>
                </Modal>

                <Modal
                    v-can="'craftable-pro.craftable-pro-user.destroy'"
                    type="danger"
                >
                    <template #trigger="{ setIsOpen }">
                        <Button
                            color="gray"
                            variant="outline"
                            size="sm"
                            :left-icon="TrashIcon"
                            @click="() => setIsOpen(true)"
                        >
                            {{ $t("craftable-pro", "Delete") }}
                        </Button>
                    </template>

                    <template #title>
                        {{ $t("craftable-pro", "Delete users") }}
                    </template>
                    <template #content>
                        {{
                            $t(
                                "craftable-pro",
                                "Are you sure you want to delete selected users? All of their data will be permanently removed from our servers forever. This action cannot be undone."
                            )
                        }}
                    </template>

                    <template #buttons="{ setIsOpen }">
                        <!-- TODO: disable button while submitting... (done in other PR) -->
                        <Button
                            color="danger"
                            @click.prevent="
                                () => {
                                    bulkAction('delete', `${baseUrl}/bulk-destroy`, {
                                        onFinish: () => setIsOpen(false),
                                    });
                                }
                            "
                        >
                            {{ $t("craftable-pro", "Delete") }}
                        </Button>
                        <Button
                            color="gray"
                            variant="outline"
                            @click.prevent="() => setIsOpen()"
                        >
                            {{ $t("craftable-pro", "Cancel") }}
                        </Button>
                    </template>
                </Modal>
            </template>

            <template #tableHead>
                <ListingHeaderCell v-width-dragging
                    sort-by="id"
                    class="w-14"
                >
                    {{ $t("craftable-pro", "ID") }}
                </ListingHeaderCell>

                <ListingHeaderCell v-width-dragging sort-by="first_name">
                    {{ $t("craftable-pro", "User") }}
                </ListingHeaderCell>

                <ListingHeaderCell v-width-dragging>
                    {{ $t("craftable-pro", "Role") }}
                </ListingHeaderCell>

                <ListingHeaderCell v-width-dragging>
                    {{ $t("craftable-pro", "Status") }}
                </ListingHeaderCell>

                <ListingHeaderCell v-width-dragging
                    v-if="$page.props.config?.craftable_pro?.track_user_last_active_time"
                    sort-by="last_active_at"
                >
                    {{ $t("craftable-pro", "Last active") }}
                </ListingHeaderCell>

                <ListingHeaderCell v-width-dragging>
                    <span class="sr-only">{{ $t("craftable-pro", "Actions") }}</span>
                </ListingHeaderCell>
            </template>

            <template #tableRow="{ item, action }: any">
                <ListingDataCell>
                    {{ item.id }}
                </ListingDataCell>

                <ListingDataCell>
                    <div class="flex items-center">
                        <Avatar
                            :src="item.avatar_url"
                            :name="`${item.first_name} ${item.last_name}`"
                        />
                        <div class="ml-4">
                            <div class="font-medium text-gray-900 dark:text-white">
                                <!-- TODO: maybe have full_name attribute? -->
                                {{ item.first_name }} {{ item.last_name }}
                            </div>
                            <div class="text-gray-500 dark:text-gray-300">
                                {{ item.email }}
                            </div>
                        </div>
                    </div>
                </ListingDataCell>

                <ListingDataCell>
                    <span class="text-sm font-normal leading-5 text-slate-500">
                        {{ item.roles.length > 0 ? item.roles[0].name : "" }}
                    </span>
                </ListingDataCell>

                <ListingDataCell class="text-left">
                    <template v-if="item.email_verified_at">
                        <div v-if="item.active">
                            <Tag
                                :icon="CheckCircleIcon"
                                color="success"
                                rounded
                                size="sm"
                            >
                                {{ $t("craftable-pro", "Active") }}
                            </Tag>
                        </div>

                        <div v-else>
                            <Tag
                                :icon="XCircleIcon"
                                color="gray"
                                rounded
                                size="sm"
                            >
                                {{ $t("craftable-pro", "Inactive") }}
                            </Tag>
                        </div>
                    </template>

                    <div v-else>
                        <Tag
                            :icon="ExclamationCircleIcon"
                            color="warning"
                            rounded
                            size="sm"
                        >
                            {{ $t("craftable-pro", "Pending") }}
                        </Tag>
                    </div>
                </ListingDataCell>

                <ListingDataCell
                    v-if="$page.props.config?.craftable_pro?.track_user_last_active_time"
                >
                    <div
                        v-if="item.email_verified_at"
                        class="flex gap-2"
                    >
                        <div v-if="item.last_active_at === null">
                            {{ $t("craftable-pro", "Never") }}
                        </div>

                        <template v-else>
                            <div class="flex flex-col justify-center">
                                <ClockIcon class="h-4 w-4" />
                            </div>
                            <div>
                                {{ dayjs(item.last_active_at).format("DD.MM.YYYY HH:mm") }}
                            </div>
                        </template>
                    </div>

                    <template v-else>
                        <Button
                            variant="outline"
                            color="gray"
                            size="sm"
                            @click.prevent="
                                () => {
                                    action(
                                        'post',
                                        `craftable-pro-users/${item.id}/resend-verification-email`
                                    );
                                }
                            "
                        >
                            {{ $t("craftable-pro", "Resend invitation") }}
                        </Button>
                    </template>
                </ListingDataCell>

                <ListingDataCell>
                    <div class="flex justify-end">
                        <Dropdown
                            no-content-padding
                            :placement="isLastThreeItems(item) ? 'bottom-end' : 'top-end'"
                        >
                            <template #button>
                                <IconButton
                                    :icon="EllipsisVerticalIcon"
                                    variant="outline"
                                    color="gray"
                                    size="sm"
                                />
                            </template>

                            <template
                                #content
                                class="bg-red"
                            >
                                <div class="py-1">
                                    <DropdownItem
                                        v-can="'craftable-pro.craftable-pro-user.edit'"
                                        :href="`${item.resource_url}/edit`"
                                        :icon="PencilSquareIcon"
                                    >
                                        {{ $t("craftable-pro", "Edit") }}
                                    </DropdownItem>

                                    <template v-if="item.email_verified_at">
                                        <DropdownItem
                                            :icon="item.active ? NoSymbolIcon : ShieldCheckIcon"
                                            @click="changeActiveStatus(item)"
                                        >
                                            {{
                                                item.active
                                                    ? $t("craftable-pro", "Deactivate")
                                                    : $t("craftable-pro", "Activate")
                                            }}
                                        </DropdownItem>
                                    </template>

                                    <DropdownItem
                                        v-else
                                        :icon="EnvelopeIcon"
                                        @click="
                                            () => {
                                                action(
                                                    'post',
                                                    `craftable-pro-users/${item.id}/resend-verification-email`
                                                );
                                            }
                                        "
                                    >
                                        {{ $t("craftable-pro", "Resend invitation") }}
                                    </DropdownItem>

                                    <div>
                                        <Modal
                                            v-can="'craftable-pro.craftable-pro-user.destroy'"
                                            type="danger"
                                        >
                                            <template #trigger="{ setIsOpen }">
                                                <div
                                                    class="flex cursor-pointer gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 dark:text-white"
                                                    @click="() => setIsOpen(true)"
                                                >
                                                    <div class="flex flex-col justify-center">
                                                        <TrashIcon class="h-4 w-4" />
                                                    </div>
                                                    {{ $t("craftable-pro", "Delete") }}
                                                </div>
                                            </template>

                                            <template #title>
                                                {{ $t("craftable-pro", "Delete user") }}
                                            </template>

                                            <template #content>
                                                {{
                                                    $t(
                                                        "craftable-pro",
                                                        "Are you sure you want to delete selected user? All of his data will be permanently removed from our servers forever. This action cannot be undone."
                                                    )
                                                }}
                                            </template>

                                            <template #buttons="{ setIsOpen }">
                                                <Button
                                                    color="danger"
                                                    @click.prevent="
                                                        () => {
                                                            action('delete', item.resource_url, {
                                                                onFinish: () => setIsOpen(false),
                                                            });
                                                        }
                                                    "
                                                >
                                                    {{ $t("craftable-pro", "Delete") }}
                                                </Button>
                                                <Button
                                                    color="gray"
                                                    variant="outline"
                                                    @click.prevent="() => setIsOpen()"
                                                >
                                                    {{ $t("craftable-pro", "Cancel") }}
                                                </Button>
                                            </template>
                                        </Modal>
                                    </div>

                                    <DropdownItem
                                        v-if="item.id !== $page.props.auth.user.id"
                                        v-can="'craftable-pro.craftable-pro-user.impersonal-login'"
                                        :href="
                                            route(
                                                'craftable-pro.craftable-pro-user.impersonalLogin',
                                                {
                                                    craftableProUser: item.id,
                                                }
                                            )
                                        "
                                        :icon="ArrowLeftEndOnRectangleIcon"
                                    >
                                        {{ $t("craftable-pro", "Log as user") }}
                                    </DropdownItem>
                                </div>
                            </template>
                        </Dropdown>
                    </div>
                </ListingDataCell>
            </template>
        </Listing>
    </PageContent>
</template>

<script setup lang="ts">
import { Link, usePage, useForm } from "@inertiajs/vue3";

import {
  PlusIcon,
  TrashIcon,
  PencilSquareIcon,
  ClockIcon,
    ArrowLeftEndOnRectangleIcon,
  EllipsisVerticalIcon,
  EnvelopeIcon,
} from "@heroicons/vue/24/outline";
import { NoSymbolIcon, ShieldCheckIcon } from "@heroicons/vue/24/solid";
import {
  CheckCircleIcon,
  ExclamationCircleIcon,
  XCircleIcon,
} from "@heroicons/vue/20/solid";
import {
  PageHeader,
  PageContent,
  Button,
  Listing,
  Avatar,
  ListingHeaderCell,
  ListingDataCell,
  Modal,
  IconButton,
  FiltersDropdown,
  Multiselect,
  Tag,
  Dropdown,
  DropdownItem,
  TextInput,
} from "craftable-pro/Components";
import { PaginatedCollection } from "craftable-pro/types/pagination";
import type { CraftableProUser } from "craftable-pro/types/models";
import { useAction } from "craftable-pro/hooks/useAction";
import { useListingFilters } from "craftable-pro/hooks/useListingFilters";
import { PageProps } from "craftable-pro/types/page";
import { wTrans } from "craftable-pro/plugins/laravel-vue-i18n";
import dayjs from "dayjs";
import { CraftableProUserInviteUserForm } from "./types";
import { useToast } from "@brackets/vue-toastification";

interface Props {
  craftableProUsers: PaginatedCollection<CraftableProUser>;
  filterOptions: {
    roles: string[];
  };
}

const { action } = useAction();

const changeActiveStatus = (item: CraftableProUser) => {
  action("patch", route("craftable-pro.craftable-pro-users.update", item.id), {
    active: !item.active,
  });
};

const statusOptions = [
  { id: "true", label: wTrans("craftable-pro", "Active") },
  { id: "false", label: wTrans("craftable-pro", "Inactive") },
  { id: "pending", label: wTrans("craftable-pro", "Pending") },
];

const props = defineProps<Props>();

const { filtersForm, resetFilters, activeFiltersCount } = useListingFilters(
  "/admin/craftable-pro-users",
  {
    role: (usePage().props as PageProps).filter?.role ?? null,
    status: (usePage().props as PageProps).filter?.status ?? null,
  }
);

const isLastThreeItems = (item: CraftableProUser) => {
  const arrLength = props.craftableProUsers.data.length;

  let lastElement = props.craftableProUsers.data[arrLength - 1];
  let beforeLastElement = props.craftableProUsers.data[arrLength - 2];

  return lastElement.id === item.id || beforeLastElement.id === item.id;
};

const toast = useToast();

const form = useForm({
  email: "",
  role_id: "",
});

const submit = (closeModal: CallableFunction) => {
  form.post(route("craftable-pro.craftable-pro-user.invite-user"), {
    onSuccess: () => {
      form.email = "";
      form.role_id = "";

      closeModal();

      toast.success(usePage().props?.message);
    },
  });
};
</script>
