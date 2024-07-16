<script setup>
import { onMounted, ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import Button from '@/Components/Button.vue';
import Intervention from '@/Components/Intervention.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import SlideOver from '@/Components/SlideOver.vue';
import { Link, router } from '@inertiajs/vue3';
import moment from "moment";
import axios from 'axios';

const showingNavigationDropdown = ref(false);
const notificationShow = ref(false);
const notifications = ref([]);
const companies = ref([]);
const count = ref(0);
const company = ref(0);
const menu = ref(1);
const props = defineProps({
    notifications: Object
})

function getCompaniesList(){
    axios.get("/empresa/listar")
    .then((res) => {
        companies.value = res.data;
    })
    .catch((error) => {
            console.log(error);
    });
}
async function getNewLeads(){
    await axios.get("/leads-contador")
    .then((res) => {
        count.value = res.data;       
    })
    .catch((error) => {
            console.log(error);
    });
}

async function setCompany(e){
    const response = await axios.get('/usuario/setcompany',{
        params: {
          id: e.target.value,
        }
    });    
    company.value = e.target.value;
    location.reload();
}

async function getCompany(){
    const response = await axios.get('/usuario/getcompany');    
    company.value = response.data;
}

function getNotification(){
    axios.get("/notificacao/show")
    .then((res) => {
        notifications.value = res.data;
    })
    .catch((error) => {
            console.log(error);
    });
}

function closeNotification(){
    notificationShow.value = false;
}

function settingsMenu(){
    if(menu.value === 1)
        menu.value = 0;
    else
        menu.value = 1;
}

onMounted(async ()=>{
    getCompaniesList();
    getCompany();
    getNewLeads();
    getNotification();
});
</script>
<template>
    <div>
        <div class="min-h-screen bg-slate-100">
            <Intervention :show="$page.props.auth.user.company_id === null"></Intervention>
        
            <nav class="bg-white border-b border-slate-100 shadow">
                <!-- Primary Navigation Menu -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-10">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <Link :href="route('dashboard')">
                                    <ApplicationLogo
                                        class="block h-9 w-auto fill-current text-slate-800"
                                    />
                                </Link>
                            </div>
                            
                            <!-- Navigation Links -->
                            <div class="hidden sm:items-center space-x-6 sm:-my-px sm:ml-10 sm:flex" v-if="$page.props.auth.user.roles[0]">
                                <NavLink :href="route('dashboard')" :active="route().current('dashboard')" class="pb-2 sm:hidden md:flex">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-1">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5m.75-9l3-3 2.148 2.148A12.061 12.061 0 0116.5 7.605" />
                                    </svg>
                                    Dashboard
                                </NavLink>
                                <NavLink :href="route('funnel.index')" :active="route().current('funnel.index')" class="pb-2 sm:hidden md:flex">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-1">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z" />
                                    </svg>
                                    Funíl
                                </NavLink>
                                <NavLink :href="route('lead.index')" :active="route().current('lead.index')" class="pb-2 sm:hidden md:flex">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-1">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 01-.825-.242m9.345-8.334a2.126 2.126 0 00-.476-.095 48.64 48.64 0 00-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0011.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155" />
                                    </svg>
                                    Leads<div v-if="count > 0" class="bg-red-500 text-xxs px-1 py-0.5 h-4 text-center rounded-md bottom-2 relative text-white ml-1"><span class="relative bottom-1 px-1">{{  count }}</span></div>

                                </NavLink>
                                <NavLink :href="route('proposal.index')" :active="route().current('proposal.index')" class="pb-2 sm:hidden md:flex">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-1">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                    </svg>
                                    Proposta Comercial
                                </NavLink>
                                <div class="ml-3 relative sm:hidden md:flex" v-if="$page.props.auth.user.roles[0].id === 1">
                                    <Dropdown align="left" width="48">
                                        <template #trigger>
                                            <span class="inline-flex rounded-md">
                                                <button
                                                    type="button"
                                                    class="inline-flex items-center px-3 py-0 border border-transparent text-xs leading-4 font-medium rounded-md text-slate-800 bg-white hover:text-slate-700 focus:outline-none transition ease-in-out duration-150"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-1">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    Financeiro

                                                    <svg
                                                        class="ml-2 -mr-0.5 h-4 w-4"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 20 20"
                                                        fill="currentColor"
                                                    >
                                                        <path
                                                            fill-rule="evenodd"
                                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                            clip-rule="evenodd"
                                                        />
                                                    </svg>
                                                </button>
                                            </span>
                                        </template>
                                        <template #content>
                                            <DropdownLink :href="route('receivable.index')"> Contas a Receber </DropdownLink>
                                            <DropdownLink :href="route('payments.index')"> Conciliação de Pagamentos </DropdownLink>                                        
                                        </template>
                                    </Dropdown>
                                </div>
                                <NavLink :href="route('reports')" :active="route().current('reports')" class="pb-2 sm:hidden md:flex" v-if="$page.props.auth.user.roles[0].id === 1 || $page.props.auth.user.roles[0].id === 3">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-1">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                                    </svg>
                                    Relatórios
                                </NavLink>
                            </div>
                        </div>
                        <div class="md:flex sm:hidden sm:items-center sm:ml-6" v-if="$page.props.auth.user.roles[0]">
                            <!-- Settings Dropdown -->
                            
                            <Button variant="basic" type="button" class="text-slate-500" @click="openNotification()">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                                </svg>
                            </Button>
                            <div class="ml-3 relative">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-xs leading-4 font-medium rounded-md text-slate-500 bg-white hover:text-slate-700 focus:outline-none transition ease-in-out duration-150"
                                            >
                                                {{ $page.props.auth.user.name }}

                                                <svg
                                                    class="ml-2 -mr-0.5 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink :href="route('profile.edit')"> Meu Perfil </DropdownLink>
                                        <DropdownLink :href="route('settings')"> Configurações </DropdownLink>
                                        <DropdownLink :href="route('userqueue.index')"> Distribuição dos Leads </DropdownLink>                                        
                                        <DropdownLink :href="route('logout')" method="post" as="button">
                                            Sair
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                            
                            <div v-if="$page.props.auth.user.roles[0].id === 1 || $page.props.auth.user.roles[0].id === 3" class="sm:hidden md:flex">
                                <select
                                    v-model="company"
                                    name="product"
                                    class="border-slate-300 focus:border-sky-500 focus:ring-sky-500 rounded-md shadow-sm text-xs h-8 p-0 px-2 text-slate-500 w-40"
                                    @change="setCompany"
                                    >
                                    <option value="2">Porto Brasil Consórcios</option>
                                    <option 
                                        v-for="item in companies"
                                        :key="item.id" 
                                        :value="`${item.id}`">
                                        {{ item.name }}
                                    </option>
                                </select>  
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-mr-2 flex items-center md:hidden " v-if="$page.props.auth.user.roles[0]">
                            <button
                                @click="showingNavigationDropdown = !showingNavigationDropdown"
                                class="inline-flex items-center justify-center p-2 rounded-md text-slate-400 hover:text-slate-500 hover:bg-slate-100 focus:outline-none focus:bg-slate-100 focus:text-slate-500 transition duration-150 ease-in-out"
                            >
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex': !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex': showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
                    class="md:hidden"
                    v-if="$page.props.auth.user.roles[0]"
                >   
                    <div class="pt-2 pb-3 space-y-1">
                        <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                            Dashboard
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('funnel.index')" :active="route().current('funnel.index')">
                            Funíl
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('lead.index')" :active="route().current('lead.index')">
                            Leads 
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('proposal.index')" :active="route().current('proposal.index')">
                            Proposta Comercial
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('receivable.index')" :active="route().current('receivable.index')" v-if="$page.props.auth.user.roles[0].id === 1">
                            Contas a Receber
                        </ResponsiveNavLink>                        
                        <ResponsiveNavLink :href="route('payments.index')" :active="route().current('payments.index')" v-if="$page.props.auth.user.roles[0].id === 1">
                            Conciliação de Pagamentos
                        </ResponsiveNavLink>                        
                    </div>
                    
                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-slate-200">
                        <div class="px-4">
                            <div class="font-medium text-base text-slate-800">
                                <button type="button" @click="settingsMenu" class="flex"> 
                                    {{ $page.props.auth.user.name }}
                                
                                    <svg
                                        class="ml-2 -mr-0.5 h-4 w-4 mt-1"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                </button>
                                
                            </div>
                            <div class="font-medium text-xs text-slate-500">{{ $page.props.auth.user.email }}</div>
                        </div>                        
                        <div :class="`${menu === 1 ? 'hidden': 'block'}`" class="mt-3 space-y-1 border-t border-b">
                            <ResponsiveNavLink :href="route('profile.edit')"> Meu Perfil </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('settings')"> Configurações </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('userqueue.index')"> Distribuição dos Leads </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('logout')" method="post" as="button">
                                Sair
                            </ResponsiveNavLink>
                        </div>
                        <div v-if="$page.props.auth.user.roles[0].id === 1 || $page.props.auth.user.roles[0].id === 3" class="sm:flex md:hidden pb-4">
                            <select
                                v-model="company"
                                name="product"
                                class="border-slate-300 focus:border-sky-500 focus:ring-sky-500 rounded-md shadow-sm text-xs w-full mx-4 mt-4 h-8 p-0 px-2 text-slate-500"
                                @change="setCompany"
                                >
                                <option value="2">Porto Brasil Consórcios</option>
                                <option 
                                    v-for="item in companies"
                                    :key="item.id" 
                                    :value="`${item.id}`">
                                    {{ item.name }}
                                </option>               
                            </select>  
                        </div>
                    </div>
                </div>
            </nav>

            <SlideOver :showSlide="notificationShow" title="Notificações" @close="closeNotification()">
                <div class="p-4">
                    <ul class="space-y-3">
                        <li 
                            v-for="notification in notifications" 
                            :key="notification.id"
                            class="px-3 py-1 text-xs whitespace-break-spaces border-b border-slate-200">
                            <div class="py-2 text-sm font-semibold">{{ notification.title }}</div>
                            <div class="pb-2 mt-2 text-xs leading-5">{{ notification.description }}</div>
                            <p class="my-2 text-xs text-slate-400 text-right w-full">{{ moment(notification.created_at).format("DD/MM/YYYY HH:MM") }}</p>
                        </li>
                    </ul>
                    <div class="text-slate-400 text-sm" v-if="notifications.length === 0">Nenhuma notificação cadastrada</div>
                </div>
            </SlideOver>
            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
