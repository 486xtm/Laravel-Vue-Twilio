<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center px-6 sm:pt-0 bg-slate-100">
        <div>
            <Link href="/">
                <!--<ApplicationLogo class=" fill-current text-slate-500" />-->
            </Link>
        </div>

        <div
            class="w-full sm:max-w-md mt-6 px-6 py-10 bg-white shadow-md overflow-hidden sm:rounded-lg"
        >
            <slot />
        </div>
    </div>
</template>
