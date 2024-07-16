<script setup>
import { ref, computed  } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Link from '@/Components/Link.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';

const breadcrumb = ref({ page:{id: 1, name: 'Configurações', url : "/configuracao"}});

const menus = ref({    
    link_14:{
        id: '13',
        name: 'Bot',
        link: '/bot',  
        permission: 'forms',      
        description: 'Administre os bots do sistema'
    },
    link_1:{
        id: '1',
        name: 'Canais',
        link: '/canal',
        permission: 'channel',
        description: 'Administre os canais de atendimentos dos leads'
    },
    link_8:{
        id: '8',
        name: 'Cidade',
        link: '/cidade',  
        permission: 'city',      
        description: 'Administre o cadastro de cidades do sistema'
    },
    link_12:{
        id: '11',
        name: 'Campanhas',
        link: '/campanha',  
        permission: 'campaigns',      
        description: 'Administre as campanhas de marketing'
    },
    link_10:{
        id: '1',
        name: 'Empresas',
        link: '/empresa',
        permission: 'companys',
        description: 'Administre as empresas do sistema'
    },    
    link_7:{
        id: '7',
        name: 'Estado',
        link: '/estado',        
        permission: 'state',
        description: 'Administre o cadastro de estados do sistema'
    },
    link_5:{
        id: '5',
        name: 'Etapas de Funil',
        link: '/etapa-de-funil',   
        permission: 'funnel',
        description: 'Administre as etapas de funíl dos leads'
    },
    link_13:{
        id: '12',
        name: 'Formulários',
        link: '/formulario',  
        permission: 'forms',      
        description: 'Administre os formulários do facebook'
    },
    link_4:{
        id: '4',
        name: 'Mensagens Rápidas',
        link: '/messagem-rapida', 
        permission: 'message',
        description: 'Administre as mensagens enviadas aos leads'
    },   
    link_6:{
        id: '6',
        name: 'Notificações',
        link: '/notificacao',
        permission: 'notification',        
        description: 'Administre as notificações enviadas aos usuários'
    },
    link_2:{
        id: '2',
        name: 'Origem',
        link: '/origem',
        permission: 'origem',
        description: 'Administre as origem de atendimentos dos leads'
    },
    link_3:{
        id: '3',
        name: 'Produtos',
        link: '/produto',    
        permission: 'product',    
        description: 'Administre os produtos ofertados aos leads'
    },
    link_11:{
        id: '10',
        name: 'Usuários',
        link: '/usuario',  
        permission: 'users',      
        description: 'Administre os usuários do sistema'
    },
    link_15:{
        id: '15',
        name: 'Status na Fila',
        link: '/fila/status',  
        permission: 'fila-status',      
        description: 'Administre status dos usuários'
    }
}); 

</script>

<template>
    <Head title="Configurações" />

    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto sm:px-4 lg:px-10">
            <breadcrumb :value="breadcrumb" class="pt-4 pb-6"/>
            <div>
                <div class="max-w-7xl mx-auto">
                    <div class="grid sm:grid-cols-1 md:grid-cols-4 gap-6 h-28 ">
                        <template 
                            v-for="menu in menus" 
                            :key="menu.id"
                            >
                            <div v-if="$page.props.user.permissions.includes(menu.permission)"
                                class="bg-white shadow-md rounded-md">
                                <div  class="p-4 border-b border-slate-200">
                                    <h1 class="text-sky-500 text-md font-semibold"  >{{ menu.name }}</h1>
                                    <p class="text-slate-600 text-xs pb-3 leading-6 tracking-tight">{{ menu.description }}</p>
                                </div>
                                <div class="justify-end flex p-4">
                                    <Link :href="menu.link" class="text-sky-500 text-sm hover:underline" >Ver Configurações</Link>
                                </div>    
                            </div>

                        </template>
                    </div>
                </div>
                
            </div>
        </div>
        
        
    </AuthenticatedLayout>
</template>
