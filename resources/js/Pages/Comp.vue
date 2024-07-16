<script setup>

import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import ButtonLink from '@/Components/ButtonLink.vue';
import TextInput from '@/Components/TextInput.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import Alert from '@/Components/Alert.vue';
import Button from '@/Components/Button.vue';
import Novo from '@/Components/Novo.vue';
import Modal from '@/Components/Modal.vue';
import SlideOver from '@/Components/SlideOver.vue';
import Multiselect from '@vueform/multiselect';  
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue'

import { QuillEditor } from '@vueup/vue-quill';
import '@vueup/vue-quill/dist/vue-quill.snow.css';

const options = ref(['batman', 'robin', 'joker']);

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

//ALERT
const showAlert = ref(true)

function onClose(){
    this.showAlert = false;
}

function closeNovo(){
    x.value = false;
}


function openNovo(){
    x.value = true;
}

function closeModal(){
    x.value = false;
}

function openSlide(){
    y.value = true;
}

function closeSlide(){
    y.value = false;
}

function closeAgenda(){
    a.value = false;
}

</script>
<style src="@vueform/multiselect/themes/default.css"></style>
<template>
    <AuthenticatedLayout>
        <div class="container mx-auto font-sans py-4">
            <h1 class="text-lg text-sky-500 font-bold tracking-tight drop-shadow-sm">Porto Brasil Consórcios</h1>
            
            <!--<Novo :show="x" :text="y" @close="closeNovo()"></Novo>-->
           
            <TabGroup>
                <TabList>
                <!-- Use the `selected` state to conditionally style the selected tab. -->
                <Tab as="template" v-slot="{ selected }">
                    <button
                    :class="{ 'bg-blue-500 text-white': selected, 'bg-white text-black': !selected }"
                    >
                    Tab 1
                    </button>
                </Tab>
                <Tab as="template" v-slot="{ selected }">
                    <button
                    :class="{ 'bg-blue-500 text-white': selected, 'bg-white text-black': !selected }"
                    >
                    Tab 3
                    </button>
                </Tab>
                <Tab as="template" v-slot="{ selected }">
                    <button
                    :class="{ 'bg-blue-500 text-white': selected, 'bg-white text-black': !selected }"
                    >
                    Tab 3
                    </button>
                </Tab>
                <!-- ... -->
                </TabList>
                <TabPanels>
                    <TabPanel>Content 1</TabPanel>
                    <TabPanel>Content 2</TabPanel>
                    <TabPanel>Content 3</TabPanel>
                <!-- ... -->
                </TabPanels>
            </TabGroup>

            <div class="pt-5 space-x-2">
                <Button variant="primary">Novo</Button>
                <Button variant="danger">Cancelar</Button>
                <Button variant="secundary">Novo</Button>
                <Button variant="success">Confirmar</Button>
            </div>
            
            <div class="pt-5 space-x-8" >
                <Link href="/play" class="text-sky-500 text-sm hover:underline" >link</Link>
                <ButtonLink href="/play">link</ButtonLink>
            </div>

            <div class="pt-5 space-y-2" >
                <h1 class="text-sky-900 text-lg font-semibold" >Titulo</h1>
                <p class="text-slate-600  text-sm leading-6 tracking-tight text-justify">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
            </div>

            <Modal :showModal="x" @close="closeModal()">
                <div class="bg-white shadow-md p-6 rounded-md">
                    <h1 class="text-sky-500 text-lg font-semibold"  >Titulo</h1>
                    <p class="text-slate-600 text-sm leading-6 tracking-tight">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
                </div>
            </Modal>

            <SlideOver :showSlide="y" @close="closeSlide()">
                <div>
                    <h1 class="text-sky-500 text-lg font-semibold"  >Titulo</h1>
                    <p class="text-slate-600 text-sm leading-6 tracking-tight">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
                </div>
            </SlideOver>

            <SlideOver :showSlide="a" title="Informações do lead" @close="closeAgenda()">
                <div>
                    <div class="bg-white">
                        <form @submit.prevent="storeCidade">
                            <div class="p-5 py-5 space-y-5">
                                <div class="grid grid-cols-3 gap-4">
                                    <div class="col-span-3">
                                        <TextInput
                                            textLabel="Nome"
                                            type="text"
                                            v-model="form.celular"
                                            name="nome"
                                            xlabel="Nome" 
                                            placeholder=""
                                        />
                                        <div
                                            v-if="form.errors.celular"
                                            class="text-sm text-red-600"                                    >
                                            {{ form.errors.celular }}
                                        </div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-3 gap-4">
                                    <div class="col-span-3">
                                        <TextInput
                                            textLabel="Celular"
                                            type="text"
                                            v-model="form.celular"
                                            name="nome"
                                            xlabel="Nome" 
                                            placeholder=""
                                        />
                                        <div
                                            v-if="form.errors.celular"
                                            class="text-sm text-red-600"                                    >
                                            {{ form.errors.celular }}
                                        </div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-3 gap-4">
                                    <div class="col-span-3">
                                        <TextInput
                                            textLabel="Email"
                                            type="text"
                                            v-model="form.celular"
                                            name="nome"
                                            xlabel="Nome" 
                                            placeholder=""
                                        />
                                        <div
                                            v-if="form.errors.celular"
                                            class="text-sm text-red-600"                                    >
                                            {{ form.errors.celular }}
                                        </div>
                                    </div>
                                </div>                            
                            </div>
                            <div class="p-5 border-t border-slate-900/10 grid grid-cols-1 gap-x-6 gap-y-8">
                                <div class="col-span-1 space-x-3">
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
                                        variant="secundary"
                                        class=""
                                        value="/canal">CANCELAR
                                    </ButtonLink>
                                </div>
                            </div> 
                        </form>
                    </div>  
                </div>
            </SlideOver>


            <div class="pt-5 space-x-2">
                <Button variant="primary" @click="openNovo()">Modal</Button>
                <Button variant="success" @click="openSlide()">SlideOver</Button>
                <Button variant="danger" @click="a = true">Agenda</Button>
            </div>
                
            
            <div class="pt-5 space-x-8 mb-5" >
                <span class="text-sky-900 text-lg font-semibold" >Titulo</span>
                <span class="text-sky-800 text-lg font-semibold" >Titulo</span>
                <span class="text-sky-700 text-lg font-semibold" >Titulo</span>
            </div>

            <div class="flex space-x-4 bg-white h-44 items-center justify-between px-4 py-4">
                <div class="flex-grow h-36 bg-sky-200">1</div>
                <div class="w-32 h-36 bg-sky-500">2</div>
                <div class="w-32 h-36 bg-sky-500">3</div>
            </div>

            <div class="grid grid-cols-3 gap-4 h-96 mt-5 text-slate-600 ">
                <div class="bg-slate-200">01</div>
                <div class="bg-slate-200">02</div>
                <div class="bg-slate-200">03</div>
                <div class="col-span-2 bg-slate-200">04</div>
                <div class="bg-slate-200">05</div>
                <div class="bg-slate-200">06</div>
                <div class="col-span-2 bg-slate-200">07</div>
            </div>

            <div class="grid grid-cols-2 gap-4 h-96 mt-5 ">
                <div class="bg-white shadow-md p-6 rounded-md">
                    <h1 class="text-sky-500 text-lg font-semibold"  >Titulo</h1>
                    <p class="text-slate-600 text-sm leading-6 tracking-tight">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
                </div>
                <div class="bg-white shadow-md p-6 rounded-md">
                    <h1 class="text-sky-500 text-lg font-semibold" >Titulo</h1>
                <p class="text-slate-600 text-sm leading-6 tracking-tight">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
                </div>
            </div>

            <div class="flex items-center justify-between mt-10 py-4">
                <div class="flex-grow border border-slate-200">
                    <table class="border-collapse text-sm text-slate-900 w-full table-auto">
                        <thead class="text-white bg-slate-500">
                            <tr class="border-b border-slate-200 text-left">
                                <th class="py-2 px-2">State</th>
                                <th class="py-2 px-2">City</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            <tr>
                                <td class="py-2 px-2 border-b border-slate-100">Indiana</td>
                                <td class="py-2 px-2 border-b border-slate-100">Indianapolis</td>
                            </tr>
                            <tr>
                                <td class="py-2 px-2 border-b border-slate-100">Ohio</td>
                                <td class="py-2 px-2 border-b border-slate-100">Columbus</td>
                            </tr>
                            <tr>
                                <td class="py-2 px-2">Michigan</td>
                                <td class="py-2 px-2">Detroit</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <Alert type="success" @close="onClose()" v-if="showAlert">
                Teste
            </Alert>

            <div class="bg-white shadow-sm sm:rounded-lg mt-3 text-slate-600">
                <div class="p-6 bg-white border-b border-slate-200">
                    <form @submit.prevent="storeCidade">
                        <div class="border-b border-slate-900/10 pb-12">
                            <h2 class="text-base font-semibold leading-7 text-sky-900">Informações do lead</h2>
                            <p class="mt-1 text-sm leading-6 text-slate-600">Use a permanent address where you can receive mail.</p>

                            <div class="mt-7 grid grid-cols-3 gap-4">
                                <div class="col-span-3">
                                    <TextInput
                                        type="text"
                                        v-model="form.celular"
                                        name="nome"
                                        xlabel="Nome" 
                                        placeholder=""
                                    />
                                    <div
                                        v-if="form.errors.celular"
                                        class="text-sm text-red-600"                                    >
                                        {{ form.errors.celular }}
                                    </div>
                                </div>
                            </div>
                            <div class="mt-7 grid grid-cols-3 gap-4">
                                <div class="col-span-3">
                                    <Multiselect
                                        v-model="value"
                                        :options="options"
                                        mode="tags"
                                        placeholder="Selecione as origens"
                                    />
                                    <div
                                        v-if="form.errors.origem"
                                        class="text-sm text-red-600"                                    >
                                        {{ form.errors.origem }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="pt-6 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                            <div class="sm:col-span-4 space-x-3">
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
                                    variant="secundary"
                                    class=""
                                    value="/canal">VOLTAR
                                </ButtonLink>
                            </div>
                        </div> 
                    </form>
                </div>
            </div>

            <div class="mt-10">
                <QuillEditor theme="snow" class="bg-white" toolbar="essential" />
            </div>

        </div>
    </AuthenticatedLayout>
</template>