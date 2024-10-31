<template>
  <div class="flex flex-1 flex-col border-r border-gray-200 dark:border-gray-600 bg-gray-100 dark:bg-gray-800">
    <div class="h-0 flex-1 overflow-y-auto pt-5 pb-4 xl:pl-2">
      <div class="flex flex-shrink-0 items-center px-4">
        <div class="h-10" />
        <h1 class="h-7">
          <Logo />
        </h1>
      </div>
      <SidebarContent class="px-4" />
    </div>
    <div class="flex flex-shrink-0 border-t border-gray-200 dark:border-gray-600 bg-gray-100 dark:bg-gray-800 p-4">
      <div class="flex w-full items-center">
        <Avatar
          :src="user?.avatar_url"
          :name="`${user?.first_name} ${user?.last_name}`"
        />
        <div v-show="!mini" class="ml-3">
          <p class="text-sm font-medium text-gray-700 dark:text-gray-200">
            {{ `${user?.first_name} ${user?.last_name}` }}
          </p>
        </div>
        <UserDropdown class="ml-auto" />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { UserDropdown, Avatar } from "../index";
import { useUser } from "craftable-pro/hooks/useUser";
import Logo from "@/craftable-pro/Components/Logo.vue";
import SidebarContent from "@/craftable-pro/Components/Sidebar.vue";
import { inject } from "vue";

const mini = inject('mini');

const { user } = useUser();
</script>
