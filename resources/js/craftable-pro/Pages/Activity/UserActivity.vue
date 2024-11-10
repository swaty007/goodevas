<template>
    <PageHeader
        sticky
        :title="`${user?.first_name} ${user?.last_name} ${user?.id}`"
        :subtitle="`Last updated at ${dayjs(
      user.updated_at
    ).format('DD.MM.YYYY HH:mm')}`"
    >
<!--        <Button-->
<!--            :leftIcon="ArrowDownTrayIcon"-->
<!--            @click="submit"-->
<!--            :loading="processing"-->
<!--            v-can="'global.user.edit'"-->
<!--        >-->
<!--            {{ $t("global", "Save") }}-->
<!--        </Button>-->
        <Button
            :leftIcon="PlusIcon"
            :as="Link"
            class="mr-3"
            :href="route('craftable-pro.transaction-replenishments.create', {user_id: props.user.id })"
            v-can="'global.transaction-replenishment.create'"
        >
            {{ $t("global", "New Replenishment") }}
        </Button>
        <Button
            :leftIcon="PlusIcon"
            :as="Link"
            :href="route('craftable-pro.transaction-withdraws.create', {user_id: props.user.id })"
            v-can="'global.transaction-withdraw.create'"
        >
            {{ $t("global", "New Withdraw") }}
        </Button>
    </PageHeader>
<!--    <EditTabs active="activity" :user="props.user" />-->


    <PageContent>
        <ActivityTable
            :baseUrl="route('craftable-pro.users.activity', props.user)"
            :activities="activities"
            :filterOptions="filterOptions"
        />
    </PageContent>
</template>

<script setup lang="ts">
// import EditTabs from "@/craftable-pro/Components/User/EditTabs.vue";
import dayjs from "dayjs";
import { PaginatedCollection } from "craftable-pro/types/pagination";
import { Link } from "@inertiajs/vue3";
import { PlusIcon, } from "@heroicons/vue/24/outline";
import { Button, PageContent, PageHeader, } from "craftable-pro/Components";
import { Activities } from "@/craftable-pro/Pages/Activity/types";
import ActivityTable from "@/craftable-pro/Pages/Activity/ActivityTable.vue";

interface Props {
    activities: PaginatedCollection<Activities>;
    filterOptions: {
        causer_type: string[];
        subject_type: string[];
        event: string[];
    };
    user: {
        id: number;
        first_name: string;
        last_name: string;
        updated_at: string;
    };
}

const props = defineProps<Props>();
</script>
