<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    value: {
        type: String,
    }
});
</script>

<template>
    <Link 
        :href="value" 
        class="text-sky-500 hover:text-sky-400 rounded-m z-10">
        <slot />
    </Link>
</template>
