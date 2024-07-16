<script setup>
import { ref, watch,  computed, onMounted  } from 'vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Actions from './Partials/BotActions.vue';
import { router } from '@inertiajs/vue3';
import Alert from '@/Components/Alert.vue';

let term = ref('');
const breadcrumb = ref({ page:{id: 1, name: 'Configurações', url : "/configuracao"}, link:{id: 1, name: 'Bots', url : "/bot"}});

const props = defineProps({
    bots: Object
});

</script>

<template>
    <Head title="Bots" />

    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto sm:px-4 lg:px-10">
            <breadcrumb :value="breadcrumb" class="pt-4 pb-6"/>
            
            <div class="pb-6">
                <Alert v-if="$page.props.flash.message" type="info" @close="onClose()">
                    {{ $page.props.flash.message }}
                </Alert>
            </div>

            <div class="grid grid-cols-2 space-x-2">
                <div v-for="bot in bots" :key="bot.id" class="bg-white px-4 py-6 text-sm text-slate-600 shadow-md">
                    <div class="flex justify-between mb-4">
                        <div class="flex space-x-2 w-full">
                            <h1 class="text-sky-900 font-semibold">{{ bot.name }}</h1>
                            <span class="bg-green-400 text-xxs text-white px-2 py-0.5 rounded-md h-5">whatsapp</span>
                        </div>
                        <div>
                            <Actions :bot="bot"></Actions>
                        </div>
                    </div>
                    <div class="flex justify-between">
                        <span v-if="bot.status_id === 1" class="text-green-400 font-semibold">ONLINE</span>
                        <span v-if="bot.status_id === 2" class="text-slate-500 font-semibold">OFFLINE</span>
                        <div class="flex justify-end space-x-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                            </svg>
                            <p class="text-right">
                                {{bot.phone}}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div><br><br>
    </AuthenticatedLayout>
</template>
