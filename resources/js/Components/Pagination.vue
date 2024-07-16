<script setup>
    import { computed } from "vue";
    import { Link } from '@inertiajs/vue3';

    const props = defineProps({
        links: Array
    });
    
</script> 
<template>
    <div v-if="links.length > 3" class="flex justify-end mr-3">
        <template
            v-for="(link,p) in links"
            :key="p">
            <Link 
                v-if="link.url"
                :href="link.url"
                v-html="link.label"        
                class="px-2 mx-0.5 text-xs border border-slate-100 rounded-sm py-1 text-slate-600"
                :class="{'text-sky-500': !link.url, 'bg-sky-500 text-white': link.active }"
                
            />
            <span v-else 
                v-html="link.label" 
                class="px-2 mx-0.5 text-xs border border-slate-100 py-1"
                :class="link.url ? '': 'text-slate-400'">
            </span>
        </template>
        
    </div>
</template>