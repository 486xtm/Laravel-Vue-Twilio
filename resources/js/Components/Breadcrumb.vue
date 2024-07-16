<script setup>

defineProps({
    value: {
        type: Object,        
    },    
    type: {
        type: String,        
        default: 'dashboard'
    },    
});
</script>

<template>
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 pb-2 md:space-x-3">
            <li v-if="type === 'dashboard'" class="inline-flex items-center">
                <a href="/dashboard" class="inline-flex items-center text-xs font-medium text-sky-500 hover:text-sky-400 dark:text-slate-400 dark:hover:text-white">
                    <svg aria-hidden="true" class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                    Dashboard
                </a>
            </li>
            <li v-else class="inline-flex items-center">
                <a href="javascript:history.back()" class="inline-flex items-center text-xs font-medium text-sky-500 hover:text-sky-400 dark:text-slate-400 dark:hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15m0 0l6.75 6.75M4.5 12l6.75-6.75" />
                    </svg>
                    Voltar
                </a>
            </li>            
            <li v-for="link in value"
                :key="link.id">
                <div class="flex items-center"
                >
                    <svg aria-hidden="true" class="w-4 h-4 text-slate-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <a :href="link.url" class="ml-1 text-xs font-medium text-slate-700 hover:text-sky-500 md:ml-2 dark:text-slate-400 dark:hover:text-white">
                        {{ link.name }}</a>
                </div>
            </li>
        </ol>
    </nav>
</template>