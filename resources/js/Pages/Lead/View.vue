<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, watch,  computed, onMounted  } from 'vue';
import moment from "moment";
import Message from './Partials/Message.vue';
import Contact from './Partials/Contact.vue';
import Activity from './Partials/Activity.vue';
import Schedule from './Partials/Schedule.vue';
import Note from './Partials/Note.vue';
import Proposal from './Partials/Proposal.vue';
import Archive from './Partials/Archive.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Button from '@/Components/Button.vue';
import TextInput from '@/Components/TextInput.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import { Tabs, Tab } from 'flowbite-vue';

const breadcrumb = ref({ page:{id: 1, name: 'Leads', url : "/lead"}});

const props = defineProps({
    leads: Object,
    leadLogs: Object,
    leadMessages: Object,
    leadNotes: Object,
    leadSchedules: Object,
    funnelSteps: Object,
    proposalTypes: Object,
    products: Object,
    reasons: Object,
    role: String,

    readyMsgs: {
        type: Object,
        default: () => ({}),
    },
    users: Object,
});

const activeTab = ref('first');
const proposalShow = ref(false);
const archiveShow = ref(false);
const leadProposal = ref([{}]);
let content = ref('contato');

const form = useForm({
   nome: null,
   email: null,
   telefone: null,
   celular: null,
   origem: null,
   canal: null,
   user: null,
   product: null,
   mensagem: null,
});

function changeContent(p){
    content.value = p;
}

function openProposal(lead){
    leadProposal.value = lead;
    proposalShow.value = true;
}

function openArchive(lead){
    leadProposal.value = lead;
    archiveShow.value = true;
}

function closeSlide(){
    proposalShow.value = false;
}

</script>

<template>
    <Head title="Leads" />

    <AuthenticatedLayout>
        <template v-for="lead in leads" 
            :key="lead.id">
                <div class="max-w-7xl mx-auto sm:px-4 lg:px-10 text-slate-700">
                    <breadcrumb :value="breadcrumb" type="back" class="pt-4 md:pb-6 sm:pb-2"/>

                    <div class="grid sm:grid-cols-1 md:grid-cols-4 md:gap-4 sm:space-y-2 md:space-y-0">
                        
                        <!-- MOBILE VERSION -->
                        <div class="bg-white px-6 py-6 md:hidden sm:block sm:h-screen">
                            <div v-if="content === 'contato'">
                                <Contact :lead="lead" :funnelSteps="funnelSteps" :role="props.role" :users="users" @openProposal="openProposal(lead)"></Contact>
                            </div>
                            <div v-if="content === 'atividade'">
                                <p class="text-xs text-slate-600 font-semibold">Atividades</p>
                                <activity :logs="leadLogs"></activity>
                            </div>
                            <div v-if="content === 'mensagem'">
                                <p class="text-xs text-slate-600 font-semibold">Mensagens</p>
                                <Message :lead="lead" :messages="leadMessages" :readyMsgs="readyMsgs"></Message>
                            </div>
                            <div v-if="content === 'notas'">
                                <p class="text-xs text-slate-600 font-semibold">Notas</p>
                                <Note :lead="lead" :notes="leadNotes"></Note>
                            </div>
                            <div v-if="content === 'agendamento'">
                                <p class="text-xs text-slate-600 font-semibold">Agendamentos</p>
                                <Schedule :lead="lead" :schedules="leadSchedules"></Schedule>
                            </div>
                        </div>

                        <!-- DADOS DO CONTATO -->
                        <div class="p-4 overflow-hidden bg-white shadow-sm sm:hidden md:block">
                            <Contact :lead="lead" :funnelSteps="funnelSteps" :role="role" :users="users" @openProposal="openProposal(lead)" @openArchive="openArchive(lead)"></Contact>
                        </div>
                        <!-- CONTEÚDO -->
                        <div class="col-span-3 bg-white shadow-sm pt-4 md:px-4 sm:hidden md:block">
                            <tabs  variant="underline" v-model="activeTab" class="px-5"> 
                                <!-- ATIVIDADES -->
                                <tab name="first" title="Atividades">
                                    <activity :logs="leadLogs"></activity>
                                </tab>
                                <!-- MENSAGENS -->
                                <tab name="third" title="Mensagens">
                                    <Message :lead="lead" :messages="leadMessages" :readyMsgs="readyMsgs"></Message>
                                </tab>
                                <!-- NOTAS -->
                                <tab name="second" title="Notas">
                                    <Note :lead="lead" :notes="leadNotes"></Note>
                                </tab>
                                <!--<tab name="fourth" title="Tarefas">
                                    Tarefas
                                </tab>-->
                                <!-- AGENDAMENTOS -->
                                <tab name="fifth" title="Agendamentos">
                                    <Schedule :lead="lead" :schedules="leadSchedules"></Schedule>
                                </tab>
                            </tabs>
                        </div>
                            
                        <div class="fixed bottom-0 left-0 z-50 w-full h-16 bg-white border-t border-gray-200 dark:bg-gray-700 dark:border-gray-600 sm:block md:hidden">
                            <div class="grid h-full max-w-lg grid-cols-5 mx-auto font-medium pr-4">
                                <button type="button" class="inline-flex flex-col items-center justify-center px-4 hover:bg-gray-50 dark:hover:bg-gray-800 group" @click="changeContent('contato')">
                                    <svg class="w-5 h-5 mb-2 text-gray-500 dark:text-gray-400 group-hover:text-sky-600 dark:group-hover:text-sky-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                      <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.376 3.376 0 016.338 0z" />
                                    </svg>
                                    <span class="text-xxs text-gray-500 dark:text-gray-400 group-hover:text-sky-600 dark:group-hover:text-sky-500">Contato</span>
                                </button>
                                <button type="button" class="inline-flex flex-col items-center justify-center px-4 hover:bg-gray-50 dark:hover:bg-gray-800 group" @click="changeContent('atividade')">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mb-2 text-gray-500 dark:text-gray-400 group-hover:text-sky-600 dark:group-hover:text-sky-500">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                                    </svg>
                                    <span class="text-xxs text-gray-500 dark:text-gray-400 group-hover:text-sky-600 dark:group-hover:text-sky-500">Atividades</span>
                                </button>
                                <button type="button" class="inline-flex flex-col items-center justify-center px-4 hover:bg-gray-50 dark:hover:bg-gray-800 group" @click="changeContent('mensagem')">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mb-2 text-gray-500 dark:text-gray-400 group-hover:text-sky-600 dark:group-hover:text-sky-500">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                    </svg>
                                    <span class="text-xxs text-gray-500 dark:text-gray-400 group-hover:text-sky-600 dark:group-hover:text-sky-500">Mensagens</span>
                                </button>
                                <button type="button" class="inline-flex flex-col items-center justify-center px-4 hover:bg-gray-50 dark:hover:bg-gray-800 group" @click="changeContent('notas')">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mb-2 text-gray-500 dark:text-gray-400 group-hover:text-sky-600 dark:group-hover:text-sky-500">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                                    </svg>
                                    <span class="text-xxs text-gray-500 dark:text-gray-400 group-hover:text-sky-600 dark:group-hover:text-sky-500">Notas</span>
                                </button>
                                <button type="button" class="inline-flex flex-col items-center justify-center px-4 hover:bg-gray-50 dark:hover:bg-gray-800 group" @click="changeContent('agendamento')">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mb-2 text-gray-500 dark:text-gray-400 group-hover:text-sky-600 dark:group-hover:text-sky-500">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z" />
                                    </svg>

                                    <span class="text-xxs text-gray-500 dark:text-gray-400 group-hover:text-sky-600 dark:group-hover:text-sky-500">Agendamentos</span>
                                </button>
                            </div>
                        </div>
                    </div>                    
                </div><br><br>
                <Proposal :show="proposalShow" page="/proposta" :types="proposalTypes" :products="products" :lead="leadProposal" @closeSlide="closeSlide()" @closeProposal="closeSlide()" ></Proposal>
                <Archive :show="archiveShow" page="/proposta" :reasons="reasons" :lead="leadProposal" @closeSlide="closeSlide()" @closeProposal="closeSlide()" ></Archive>
        </template>
    </AuthenticatedLayout>
</template>
