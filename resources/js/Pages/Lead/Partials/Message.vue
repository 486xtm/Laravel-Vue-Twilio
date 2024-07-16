<script setup>
import moment from "moment";
import { ref, watch,  computed, onMounted  } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import Button from '@/Components/Button.vue';
import Link from '@/Components/Link.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css';

const props = defineProps({
    lead: Object,
    messages: Object,
    readyMsgs: {
        type: Object,
        default: () => ({}),
    }  
});

const readyMsg = ref('null');
const msg = ref('');
const messageID = ref(props.lead.id);
const idMsg = ref(0);
const ready = ref('');
const chk = ref(['1']);
const toolbarOptions = ['bold', 'italic'];

const form = useForm({
    message: null,
    lead_id: null,
});

function storeMessage(){
    
    if(chk.value[0] === '1'){
        console.log(form.message);

        var regex = /<p><br><\/p>/g;
        var msg = form.message.replace(regex, '%0a');

        var regex = /<p>/g;
        var msg = msg.replace(regex, '');

        var regex = /&nbsp;/g;
        var msg = msg.replace(regex, ' ');

        regex = /<\/p>/g;
        msg = msg.replace(regex, '%0a');
        
        msg = msg.replace(/<strong>/g, '*').replace(/<\/strong>/g, '*');
        msg = msg.replace(/<em>/g, '_').replace(/<\/em>/g, '_');
        
        regex = /<br\s*[\/]?>/gi;
        msg = msg.replace(regex, '%0a');
        console.log(msg);

        regex = /(<([^>]+)>)/ig;
        msg = msg.replace(regex, '');
        console.log(msg);

        var url = 'https://web.whatsapp.com/send?phone=+55'+props.lead.celular.replace(/^(\+)|\D/g,'')+'&text='+msg;
        window.open(url, '_blank', 'noreferrer');
    }
    form.lead_id = messageID.value;
    form.post('/lead-message', form);
    form.message = " ";
}

function onChange(e) {
    console.log(e.target.value);
    ready.value = props.readyMsgs.filter((x) => x.id === parseInt(e.target.value));
    msg.value = ready.value[0].message;
    msg.value = msg.value.replace('[NOME_CONTATO]', props.lead.name);
    msg.value = msg.value.replace('[NOME_VENDEDOR]', ready.value[0].name);
    form.message = '';
    form.message = msg.value;
    readyMsg.value = null;
}

</script>
<template>
    <form @submit.prevent="storeMessage">
        <div id="div-msg" class="mt-5 px-6 py-6 lg:h-72  sm:max-h-80 overflow-x-auto rounded-md border border-slate-100">
            <ul class="space-y-5">
                <li 
                    v-for="msg in messages" 
                    :key="msg.id"
                    class="p-4 text-slate-900 text-xs whitespace-break-spaces rounded-lg shadow-sm md:w-2/3 sm:w-full"
                    :class="msg.origen === 2 ? 'bg-green-200 float-left' : 'bg-sky-200 float-right'">
                    <div v-html="msg.message"></div>
                    <p v-if="msg.user_name" class="pt-2 text-right text-xxs text-slate-500">{{ moment(msg.created_at).format("DD/MM/YY HH:mm")}} por {{msg.user_name}} ({{ msg.channel_msg}})</p>
                    <div
                        v-if="msg.bot === 1 && msg.origen === 1">
                        <p  class="pt-2 text-right text-xxs text-slate-500">{{ moment(msg.created_at).format("DD/MM/YY HH:mm")}} por Bot ({{ msg.channel_msg}})</p>
                    </div>
                    <div
                        v-if="msg.bot === 1 && msg.origen === 2">
                        <p  class="pt-2 text-right text-xxs text-slate-500">{{ moment(msg.created_at).format("DD/MM/YY HH:mm")}} por {{ lead.name }} ({{ msg.channel_msg}})</p>
                    </div>
                </li>
            </ul>
            <div v-if="!messages.length" class="flex text-sm text-slate-600 pt-2">
                🕛 Nenhuma mensagem enviada até o momento
            </div>
        </div>
        <div class="mt-4 grid grid-cols-3 gap-4 pt-4">
            <div class="col-span-3">
                <div class="flex space-x-1">
                    <InputLabel
                        for="User"
                        class="block mb-2 text-sm font-mecold dium text-slate-900 dark:text-slate-300"
                    >Mensagens Prontas 
                    </InputLabel>
                     <Link value="/messagem-rapida" class="pt-0.5" title="Configure suas Mensagens">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </Link>
                </div>
                <select
                    v-model="readyMsg"
                    name="readyMsg"
                    class="border-slate-300 focus:border-sky-500 focus:ring-sky-500 rounded-md shadow-sm text-sm w-full"
                    @change="onChange"                                     
                    >
                    <option :value="null">Selecione</option>
                    <option 
                        v-for="msg in readyMsgs"
                        :key="msg.id" 
                        :value="`${msg.id}`">
                        {{ msg.title }}
                    </option>
                </select>
            </div>
        </div>
        <div class="mt-4 grid grid-cols-1">
            <InputLabel
                for="Mensagem"
                class="block mb-2 text-sm font-mecold dium text-slate-900 dark:text-slate-300"
                >Mensagem
            </InputLabel>
           <QuillEditor v-model:content="form.message" contentType="html" ref="myQuillEditor" theme="snow" class="bg-white" :toolbar="toolbarOptions" />
            <div
                v-if="form.errors.message"
                class="text-sm text-red-500 mt-2"
            >
                {{ form.errors.message }}
            </div>
        </div>                     
        <div class="my-6 grid grid-cols-1 gap-x-6">
            <div class="md:flex space-x-2 sm:mb-20">
                <Button
                    variant="primary"
                    type="submit"
                    class="sm:w-full md:w-auto"
                    :disabled="form.processing"
                    :class="{ 'opacity-25': form.processing }"
                >
                    RESPONDER
                </Button>
                <div class="flex text-xs space-x-1 w-full">
                    <div class="flex md:pt-2 sm:pt-3 md:justify-end w-full">
                        <span>Encaminhar por: {{ checked  }}</span>
                        <div class="space-x-1 mx-2">
                            <input type="checkbox" id="whatsapp" value="1" v-model="chk" class="w-4 h-4 text-sky-600 bg-gray-100 border-gray-300 rounded focus:ring-sky-500 dark:focus:ring-sky-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            <label for="default-checkbox" class="ml-2 font-medium text-slate-900 dark:text-gray-300">Whatsapp</label>
                        </div>
                        <div class="space-x-1 mx-2">
                            <input type="checkbox" id="email" value="2" v-model="chk" class="w-4 h-4 text-sky-600 bg-gray-100 border-gray-300 rounded focus:ring-sky-500 dark:focus:ring-sky-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            <label for="default-checkbox" class="ml-2 font-medium text-slate-900 dark:text-gray-300">Email</label>
                        </div>
                    </div>
                </div>
            </div>
            
        </div> 

        
    </form> 
</template>

