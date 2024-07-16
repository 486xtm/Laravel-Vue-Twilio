<script setup>
import { ref  } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue'
import { Head, useForm } from '@inertiajs/vue3';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import ButtonLink from '@/Components/ButtonLink.vue';
import Button from '@/Components/Button.vue';
import SlideOver from '@/Components/SlideOver.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css';

const breadcrumb = ref({ page:{id: 1, name: 'Configurações', url : "/configuracao"}, link:{id: 1, name: 'Bots', url : "/bot"}});

const props = defineProps({
    bot: Object,
    workflows: Object,
});

const selectedTab = ref(0);
const workflowTab = ref(false);
const success = ref(false);

const form = useForm({
   name: props.bot.name,
   phone: props.bot.phone,
   token: props.bot.token,
   sid: props.bot.sid,
   welcome_msg: props.bot.welcome_msg,
   option_msg: props.bot.options_msg,
   default_msg: props.bot.default_msg,
   invalid_answer: props.bot.invalid_answer,   
});

const formWorkFlow = useForm({
   id: null,
   name: null,
   message: null,   
   conditional: null,   
   parent: null,   
   type: null,   
});

const formMessage = useForm({
   name: null,
   message: null,
   stage: null,
   type: null,
   parent: null,
});

function updateBot(){
    form.put(route("bot.update", props.bot.id));
};

function changeTab(index) {
  selectedTab.value = index
}

function closeWorkflow(){
    workflowTab.value = false;
}

function openWorkflow(obj){
    success.value = false;
    formWorkFlow.id = obj.id;
    formWorkFlow.name = obj.name;
    formWorkFlow.message = obj.message;
    formWorkFlow.conditional = obj.conditional;
    formWorkFlow.parent = obj.parent;

    workflowTab.value = true;
}

function updateWorkflow(){
    formWorkFlow.put(route("botworkflow.update", formWorkFlow.id),{
        onSuccess: () => successForm(),
    });    
}

function successForm(){
    success.value = true;
    formWorkFlow.reset();
}

</script>

<template>
    <Head title="Bot" />
    <AuthenticatedLayout>            
        <div class="pt-5 max-w-7xl mx-auto sm:px-4 lg:px-6">
            <breadcrumb :value="breadcrumb" class="pb-5"/>
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form @submit.prevent="updateBot">
                        <div class="border-b border-slate-900/10 pb-12">
                            <TabGroup :selectedIndex="selectedTab" @change="changeTab">
                                <TabList class=" border-b text-sm border-slate-200 rounded-sm sm:hidden md:block">
                                    <!-- Use the `selected` state to conditionally style the selected tab. -->
                                    <Tab as="template" v-slot="{ selected }">
                                        <button
                                        :class="{ 'border-b-2 border-sky-500 text-sky-500': selected, 'text-slate-500': !selected }"
                                        class="px-6 py-2"
                                        >
                                        Geral
                                        </button>
                                    </Tab>
                                    <Tab as="template" v-slot="{ selected }">
                                        <button
                                        :class="{ 'border-b-2 border-sky-500 text-sky-500': selected, 'text-slate-500': !selected }"
                                        class="px-6 py-2"
                                        >
                                        Mensagens
                                        </button>
                                    </Tab>
                                    <Tab as="template" v-slot="{ selected }">
                                        <button
                                        :class="{ 'border-b-2 border-sky-500 text-sky-500': selected, 'text-slate-500': !selected }"
                                        class="px-6 py-2"
                                        >
                                        Integração
                                        </button>
                                    </Tab>
                                    <Tab as="template" v-slot="{ selected }">
                                        <button
                                        :class="{ 'border-b-2 border-sky-500 text-sky-500': selected, 'text-slate-500': !selected }"
                                        class="px-6 py-2"
                                        >
                                        Árvore de Decisão
                                        </button>
                                    </Tab>
                                    <!-- ... -->
                                </TabList>
                                <TabPanels>
                                    <TabPanel class="py-4">
                                        <div class="mt-3 grid grid-cols-1">
                                            <TextInput
                                                textLabel="Atendente Virtual"
                                                type="text"
                                                v-model="form.name"
                                                name="name"
                                                class="w-full"
                                                placeholder=""
                                            />
                                            <div
                                                v-if="form.errors.name"
                                                class="text-sm text-red-500 mt-2"
                                            >
                                                {{ form.errors.name }}
                                            </div>
                                        </div>
                                        <div class="mt-3 grid grid-cols-1">
                                            <TextInput
                                                textLabel="Telefone"
                                                type="text"
                                                v-model="form.phone"
                                                name="phone"
                                                class="w-full"
                                                placeholder=""
                                            />
                                            <div
                                                v-if="form.errors.phone"
                                                class="text-sm text-red-500 mt-2"
                                            >
                                                {{ form.errors.phone }}
                                            </div>
                                        </div>
                                    </TabPanel>
                                    <TabPanel class="py-4">
                                        <div class="mt-2 grid grid-cols-1">
                                            <Inputlabel
                                                for="Mensagem"
                                                class="block mb-2 text-sm font-mecold dium text-slate-900 dark:text-slate-300"
                                                >Mensagem Padrão
                                            </Inputlabel>
                                            <textarea
                                                v-model="form.default_msg"
                                                name="mensagem"
                                                class="border-slate-300 focus:border-sky-500 focus:ring-sky-500 rounded-md shadow-sm text-sm w-full"
                                                rows="3" 
                                                >
                                            </textarea>
                                        </div>
                                        <div class="mt-8 grid grid-cols-1">
                                            <Inputlabel
                                                for="Mensagem"
                                                class="block mb-2 text-sm font-mecold dium text-slate-900 dark:text-slate-300"
                                                >Mensagem de Resposta Inválida
                                            </Inputlabel>
                                            <textarea
                                                v-model="form.invalid_answer"
                                                name="mensagem"
                                                class="border-slate-300 focus:border-sky-500 focus:ring-sky-500 rounded-md shadow-sm text-sm w-full"
                                                rows="3" 
                                                >
                                            </textarea>
                                        </div>
                                    </TabPanel>
                                    <TabPanel class="py-4"> 
                                        <div class="mt-3 grid grid-cols-1">
                                            <TextInput
                                                textLabel="Twilio Token"
                                                type="text"
                                                v-model="form.token"
                                                name="token"
                                                class="w-full"
                                                placeholder=""
                                            />
                                            <div
                                                v-if="form.errors.token"
                                                class="text-sm text-red-500 mt-2"
                                            >
                                                {{ form.errors.token }}
                                            </div>
                                        </div>
                                        <div class="mt-3 grid grid-cols-1">
                                            <TextInput
                                                textLabel="Twilio SID"
                                                type="text"
                                                v-model="form.sid"
                                                name="sid"
                                                class="w-full"
                                                placeholder=""
                                            />
                                            <div
                                                v-if="form.errors.sid"
                                                class="text-sm text-red-500 mt-2"
                                            >
                                                {{ form.errors.sid }}
                                            </div>
                                        </div>
                                    </TabPanel>
                                    <TabPanel class="py-4">  
                                        <div v-if="1 === 0" class="mt-3 mb-10 grid gap-x-6 gap-y-4 grid-cols-5">
                                            <div>
                                                <TextInput
                                                    textLabel="Nome"
                                                    type="text"
                                                    v-model="formMessage.name"
                                                    name="name"
                                                    class="w-full"
                                                    placeholder=""                                                    
                                                />
                                                <div
                                                    v-if="formMessage.errors.name"
                                                    class="text-sm text-red-500 mt-2"
                                                >
                                                    {{ formMessage.errors.name }}
                                                </div>
                                            </div>   
                                            <div>
                                                <Inputlabel
                                                    for="stage"
                                                    class="block mb-2 text-sm font-mecold dium text-slate-900 dark:text-slate-300"
                                                    >Estágio
                                                </Inputlabel>
                                                <select
                                                    v-model="formMessage.stage"
                                                    name="stage"
                                                    class="border-gray-300 focus:border-blue-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm w-full"                                                    
                                                    >
                                                    <option :value="null">Selecione</option>
                                                    <option value="1">1</option>
                                                    <option value="2">2</option>
                                                    <option value="3">3</option>
                                                    <option value="4">4</option>
                                                </select>
                                                <div
                                                    v-if="formMessage.errors.stage"
                                                    class="text-sm text-red-600"
                                                >
                                                    {{ formMessage.errors.stage }}
                                                </div>
                                            </div>  
                                            <div>
                                                <Inputlabel
                                                    for="stage"
                                                    class="block mb-2 text-sm font-mecold dium text-slate-900 dark:text-slate-300"
                                                    >Tipo
                                                </Inputlabel>
                                                <select
                                                    v-model="formMessage.type"
                                                    name="type"
                                                    class="border-gray-300 focus:border-blue-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm w-full"                                                    
                                                    >
                                                    <option :value="null">Selecione</option>
                                                    <option value="1">Opção</option>
                                                    <option value="2">Pergunta</option>                                                    
                                                </select>
                                                <div
                                                    v-if="formMessage.errors.type"
                                                    class="text-sm text-red-600"
                                                >
                                                    {{ formMessage.errors.type }}
                                                </div>
                                            </div>   
                                            <div>
                                                <Inputlabel
                                                    for="parent"
                                                    class="block mb-2 text-sm font-mecold dium text-slate-900 dark:text-slate-300"
                                                    >Pai
                                                </Inputlabel>
                                                <select
                                                    v-model="formMessage.parent"
                                                    name="parent"
                                                    class="border-gray-300 focus:border-blue-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm w-full"                                                    
                                                    >
                                                    <option :value="null">Selecione</option>
                                                    <option value="1">Welcome</option>
                                                    <option value="2">Final</option>                                                    
                                                </select>
                                                <div
                                                    v-if="formMessage.errors.parent"
                                                    class="text-sm text-red-600"
                                                >
                                                    {{ formMessage.errors.parent }}
                                                </div>
                                            </div>                                                  
                                            <div class="pt-7">
                                                <Button
                                                    variant="default"
                                                    type="button"
                                                    class="h-10"
                                                    @click="calc"                                                        
                                                >
                                                    CRIAR
                                                </Button>
                                            </div>                                                               
                                        </div>
                                        <div>
                                            <h3 class="font-semibold text-sm text-slate-800 py-2">Fluxos:</h3>
                                        </div>                                        
                                        
                                        <div class="space-y-10 text-sm border-gray-4 border-2 bg-slate-50 p-10">
                                            <div v-for="workflow in workflows" :key="workflow.id">
                                                <div class="mt-3 grid-cols-1 flex justify-center">                                            
                                                    <div class="justify-center flex bg-sky-500 text-white rounded-md shadow-gray-300 shadow-md">
                                                        <div class="flex justify-between space-x-2 py-2 px-2">
                                                            <span class="px-2 py-2">{{ workflow.name}}</span>
                                                            <Button
                                                                variant="inline"
                                                                type="button"
                                                                class="text-white"
                                                                @click="openWorkflow(workflow)"
                                                                title="Editar">    
                                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                                                </svg>
                                                            </Button>
                                                        </div>
                                                    </div>           
                                                </div>
                                                <div class="grid-cols-1 flex justify-center space-x-2">      
                                                    <div v-for="children in workflow.children" :key="children.id">
                                                        <div class="justify-center flex bg-sky-700 py-2 px-2 text-white rounded-md mt-10">
                                                            <div class="flex justify-between space-x-2 ">
                                                                <span class="px-2 py-2">{{ children.name}}</span>
                                                                <Button
                                                                    variant="inline"
                                                                    type="button"
                                                                    class="text-white"
                                                                    @click="openWorkflow(children)"
                                                                    title="Editar">    
                                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                                                    </svg>
                                                                </Button>
                                                            </div>
                                                        </div>                                            
                                                    </div>                                         
                                                </div>
                                            </div>
                                        </div>                                        
                                    </TabPanel>
                                </TabPanels>
                            </TabGroup>
                        </div>
                        <div class="pt-6 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                            <div class="sm:col-span-4 space-x-2">
                                <Button
                                    variant="primary"
                                    type="submit"
                                    class=""
                                    :disabled="form.processing"
                                    :class="{ 'opacity-25': form.processing }"
                                >
                                    SALVAR
                                </Button>
                                <ButtonLink 
                                    variant="default"
                                    class=""
                                    value="/bot">VOLTAR
                                </ButtonLink>                            
                            </div>
                        </div>                        
                    </form>
                </div>             
            </div>
            <div class="flex justify-end mt-2">
            </div>
        </div>
        <SlideOver :showSlide="workflowTab" title="Edição do Fluxo" @close="closeWorkflow()">
            <div>
                <div v-if="!success" class="bg-white">
                    <form @submit.prevent="updateWorkflow">
                        <div class="mt-5 px-5 space-y-3">
                            <div class="mt-1 grid grid-cols-1 space-x-2">
                                <div>
                                    <TextInput
                                        textLabel="Nome"
                                        type="text" 
                                        v-model="formWorkFlow.name"
                                        name="name"
                                        class="w-full"
                                        placeholder=""
                                    />
                                    <div
                                        v-if="formWorkFlow.errors.name"
                                        class="text-sm text-red-500 mt-2"
                                    >
                                        {{ formWorkFlow.errors.name }}
                                    </div>
                                </div>                                
                            </div>             
                            <div class="sm:col-span-6">
                                <Inputlabel
                                    for="Observação"
                                    class="block mb-2 text-sm font-mecold dium text-slate-900 dark:text-slate-300"
                                    >Mensagem
                                </Inputlabel>
                                <textarea
                                    v-model="formWorkFlow.message"
                                    name="message"
                                    class="border-slate-300 focus:border-sky-500 focus:ring-sky-500 rounded-md shadow-sm text-sm w-full"
                                    rows="8" 
                                    >
                                </textarea>
                                <div
                                    v-if="formWorkFlow.errors.message"
                                    class="text-sm text-red-600"
                                >
                                    {{ formWorkFlow.errors.message }}
                                </div>
                            </div>
                            <div class="mt-1 grid grid-cols-1 space-x-2">
                                <div>
                                    <TextInput
                                        textLabel="Regra Condicional"
                                        type="text" 
                                        v-model="formWorkFlow.conditional"
                                        name="conditional"
                                        class="w-full"
                                        placeholder=""
                                    />
                                    <div
                                        v-if="formWorkFlow.errors.conditional"
                                        class="text-sm text-red-500 mt-2"
                                    >
                                        {{ formWorkFlow.errors.conditional }}
                                    </div>
                                </div>                                
                            </div>   
                        </div>
                        <div class="mt-6 grid grid-cols-1 gap-x-6 px-5">
                            <div class="sm:col-span-4 space-x-2">
                                <Button
                                    variant="primary"
                                    type="submit"
                                    :disabled="form.processing"
                                    :class="{ 'opacity-25': form.processing }"
                                >
                                    SALVAR
                                </Button>                         
                            </div>
                        </div> 
                    </form>
                </div>                  
            </div>
            <div v-if="success" class="text-center mt-10 h-screen align-middle">
                <h1 class="text-lg font-extralight">Fluxo atualizado com sucesso</h1>
                <div class="mt-6 grid grid-cols-1 gap-x-6 px-5">
                    <div class="sm:col-span-4 space-x-2">
                        <Button
                            variant="primary"
                            type="button"
                            @click="closeWorkflow"
                        >
                            FECHAR
                        </Button>                         
                    </div>
                </div> 
            </div>
        </Slideover>
    </AuthenticatedLayout>
</template>
