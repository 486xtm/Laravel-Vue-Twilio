<script setup>
import {ref, watch, computed, onMounted,reactive } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue'
import { throttle, pickBy } from 'lodash';
import { router } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';
import Campaign from './Partials/Campaign.vue';
import Bot from './Partials/Bot.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'

const breadcrumb = ref({ page:{id: 1, name: 'Relatórios', url : "/relatorios"}});

const props = defineProps({
    filters: Object,
    reports: Object,
});

const filter = reactive({
    date: props.filters.date,
});

watch(filter,
    throttle( ()=>{
        let query = pickBy(filter);
        let queryRoute = route('reports', Object.keys(query).length ? query : {
            remember: 'forget',
        });
        
        router.get(
            queryRoute, 
            {},
            {
            preserveState:true,
            replace:true 
        });
    }, 500),
    {
        deep: true,
    }
);
</script>

<template>
    <Head title="Relatórios" />

    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto sm:px-4 lg:px-10">
            <breadcrumb :value="breadcrumb" class="pt-4"/>
            <div>
                <div class="max-w-7xl mx-auto">                    
                    <div class="flex justify-end">
                        <div>
                            <VueDatePicker 
                                v-model="filter.date" 
                                format="dd/MM/yyyy" 
                                locale="pt-br"
                                cancelText="cancelar" 
                                selectText="selecione" 
                                :enable-time-picker="false"
                                :partial-range="false"
                                max-range="31"
                                range 
                                utc>
                            </VueDatePicker> 
                        </div>                       
                    </div>
                    
                    <TabGroup :selectedIndex="selectedTab" @change="changeTab">
                        <TabList class=" border-b text-sm border-slate-200 rounded-sm sm:hidden md:block">
                            <!-- Use the `selected` state to conditionally style the selected tab. -->
                            <Tab as="template" v-slot="{ selected }">
                                <button
                                :class="{ 'border-b-2 border-sky-500 text-sky-500': selected, 'text-slate-500': !selected }"
                                class="px-6 py-2"
                                >
                                Bots
                                </button>
                            </Tab>
                            <Tab as="template" v-slot="{ selected }">
                                <button
                                :class="{ 'border-b-2 border-sky-500 text-sky-500': selected, 'text-slate-500': !selected }"
                                class="px-6 py-2"
                                >
                                Campanhas
                                </button>
                            </Tab>
                        </TabList>
                        <TabPanels>
                            <TabPanel class="py-4 ">
                                <div>
                                    <Bot :data="reports.bots"></Bot>
                                </div>
                            </TabPanel>
                        </TabPanels>
                        <TabPanels>
                            <TabPanel class="py-4 ">
                                <div>
                                    <Campaign :data="reports.forms"></Campaign>
                                </div>
                            </TabPanel>
                        </TabPanels>
                    </TabGroup>                    
                </div>                
            </div>
        </div>
    </AuthenticatedLayout>
</template>
