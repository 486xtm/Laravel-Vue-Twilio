
<script setup>
    import { computed } from "vue";

    const props = defineProps({
        variant:{
            type: String,
            default: "default",
        }
    });

    const baseClass = computed(() => {   
        return props.variant === "primary" ? "px-4 py-2.5 bg-sky-500 hover:bg-sky-400 text-white " : 
            props.variant === "danger" ? "px-4 py-2.5 bg-red-600 hover:bg-red-500 text-white": 
            props.variant === "default" ? "px-4 py-2.5 bg-slate-200 hover:bg-slate-100 text-slate-500": 
            props.variant === "secundary" ? "px-4 py-2.5 bg-white border-slate-500 hover:border-slate-700 border text-slate-600": 
            props.variant === "basic" ? " text-slate-600 hover:text-sky-500": 
            props.variant === "success" ? "px-4 py-2.5 bg-green-400 hover:bg-green-300 text-white" : 
            props.variant === "inline" ? "hover:text-slate-500 text-sky-500" : "";
    });
</script> 

<template>
    <button :class="`${baseClass} shadow-smpx-6 sm:text-xxs md:text-xs font-light rounded-md items-center z-10`">
        <slot />
    </button>
</template>