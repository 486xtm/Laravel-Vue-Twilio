<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import Button from '@/Components/Button.vue';
import ButtonLink from '@/Components/ButtonLink.vue';
import TextInput from '@/Components/TextInput.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css';

const breadcrumb = ref({ page:{id: 1, name: 'Configurações', url : "/configuracao"}, link:{id: 1, name: 'Notificações', url : "/notificacao"}});
const form = useForm({
   title: null,
   message: null
});

function storeNotification(){
    form.post('/notificacao');
};

</script>
<template>
    <Head title="Canal" />
 
    <AuthenticatedLayout>
        <div class="pt-4 max-w-7xl mx-auto sm:px-4 lg:px-10">
            <breadcrumb :value="breadcrumb" type="back" class="pb-5"/>
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form @submit.prevent="storeNotification">
                        <div class="border-b border-slate-900/10 pb-12">
                            <h2 class="text-base font-semibold leading-7 text-sky-900">Informações da Notificação</h2>
                            <div class="mt-3 grid grid-cols-1">
                                <TextInput
                                    textLabel="Título"
                                    type="text"
                                    v-model="form.title"
                                    name="title"
                                    class="w-full"
                                    placeholder=""
                                />
                                <div
                                    v-if="form.errors.title"
                                    class="text-sm text-red-500 mt-2"
                                >
                                    {{ form.errors.title }}
                                </div>
                            </div>
                            <div class="mt-3 grid grid-cols-1">
                                <Inputlabel
                                    for="Mensagem"
                                    class="block mb-2 text-sm font-mecold dium text-slate-900 dark:text-slate-300"
                                    >Mensagem
                                </Inputlabel>
                                <QuillEditor v-model:content="form.message" contentType="html" ref="myQuillEditor" theme="snow" class="bg-white" toolbar="essential" />
                                <div
                                    v-if="form.errors.message"
                                    class="text-sm text-red-500 mt-2"
                                >
                                    {{ form.errors.message }}
                                </div>
                            </div>
                        </div>
                        <div class="pt-5 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
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
                                    value="/notificacao">VOLTAR
                                </ButtonLink>                                
                            </div>
                        </div>                        
                    </form>
                </div>                
            </div>
        </div>
    </AuthenticatedLayout>
</template>
