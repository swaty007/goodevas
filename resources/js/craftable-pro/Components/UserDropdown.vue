<template>
  <div class="divide-y divide-gray-100">
    <div class="py-1">
      <DropdownItem class="font-medium text-gray-900 dark:text-white">
        {{ user?.email }}
      </DropdownItem>
    </div>
    <div class="py-1">
      <DropdownItem href="/admin/profile">
        {{ $t("craftable-pro", "Profile") }}
      </DropdownItem>
      <DropdownItem href="/admin/password">
        {{ $t("craftable-pro", "Password") }}
      </DropdownItem>
    </div>
  </div>
</template>

<script setup lang="ts">
import { DropdownItem } from "craftable-pro/Components";
import { useUser } from "craftable-pro/hooks/useUser";

const { user } = useUser();
</script>
