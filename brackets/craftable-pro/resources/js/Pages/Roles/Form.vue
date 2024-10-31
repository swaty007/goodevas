<template>
  <form>
    <Card>
      <div class="divide-y divide-gray-200 dark:divide-gray-600">
        <Permission
          v-model="form.permissionsTree[key]"
          :parent-permission="key"
          v-for="(permissions, key) in form.permissionsTree"
        />
      </div>
    </Card>
  </form>
</template>

<script setup lang="ts">
import { ArrowDownTrayIcon } from "@heroicons/vue/24/outline";
import { Button, Card, CardFooter } from "craftable-pro/Components";
import { InertiaForm } from "@inertiajs/vue3";
import { Role } from "craftable-pro/types/models";
import { RoleForm } from "craftable-pro/Pages/Roles/types";
import Permission from "craftable-pro/Pages/Roles/Permission.vue";

interface Props {
  form: InertiaForm<RoleForm>;
  submit: void;
  role: Role;
}

const props = defineProps<Props>();
</script>
