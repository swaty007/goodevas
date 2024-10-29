<template>
    <NavLink
        :href="route('dashboard')"
        :active="route().current('dashboard')"
    >
        Dashboard
    </NavLink>
    <NavLink
        :href="route('dashboard')"
        :active="route().current('dashboard')"
    >
        Residues in warehouses
    </NavLink>
    <NavLink
        :href="route('dashboard')"
        :active="route().current('dashboard')"
    >
        Goods
    </NavLink>
</template>
<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import NavLink from "@/Components/NavLink.vue";
</script>
