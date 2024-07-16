<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Button from '@/Components/Button.vue';
import ButtonLink from '@/Components/ButtonLink.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Produtos" />

    <AuthenticatedLayout>
        <div class="pt-5 max-w-7xl mx-auto sm:px-4 lg:px-6">
            <breadcrumb class="pb-5"/>
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form @submit.prevent="storeProduct">
                            <div class="border-b border-slate-900/10 pb-12">
                                <h2 class="text-base font-semibold leading-7 text-sky-900">Informações do Usuário</h2>
                                <div class="mt-3 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                                    <div class="sm:col-span-4">
                                        <TextInput
                                            textLabel="Nome"
                                            type="text"
                                            v-model="form.name"
                                            name="name"
                                            class="w-full"
                                            placeholder=""
                                        />
                                        <div
                                            v-if="form.errors.name"
                                            class="text-sm text-red-600"
                                        >
                                            {{ form.errors.name }}
                                        </div>
                                    </div>
                                    <div class="sm:col-span-4">
                                    <TextInput
                                            textLabel="Email"
                                            type="text"
                                            v-model="form.email"
                                            name="email"
                                            class="w-full"
                                            placeholder=""
                                        />
                                        <div
                                            v-if="form.errors.email"
                                            class="text-sm text-red-600"
                                        >
                                            {{ form.errors.email }}
                                        </div>
                                    </div>
                                    <div class="sm:col-span-1">
                                        <TextInput
                                            textLabel="Telefone"
                                            type="text"
                                            v-model="form.telefone"
                                            name="telefone"
                                            class="w-full"
                                            placeholder=""
                                        />
                                        <div
                                            v-if="form.errors.telefone"
                                            class="text-sm text-red-600"
                                        >
                                            {{ form.errors.telefone }}
                                        </div>
                                    </div>
                                    <div class="sm:col-span-1">
                                        <TextInput
                                            textLabel="Celular"
                                            type="text"
                                            v-model="form.celular"
                                            name="celular"
                                            class="w-full"
                                            placeholder=""
                                        />
                                        <div
                                            v-if="form.errors.celular"
                                            class="text-sm text-red-600"
                                        >
                                            {{ form.errors.celular }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="pt-6 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                                <div class="sm:col-span-4 space-x-2">
                                    <Button
                                        styleType="primary"
                                        type="submit"
                                        class=""
                                        :disabled="form.processing"
                                        :class="{ 'opacity-25': form.processing }"
                                    >
                                        SALVAR
                                    </Button>
                                    <ButtonLink 
                                        styleType="default"
                                        class=""
                                        value="/canal">VOLTAR
                                    </ButtonLink>                                   
                                </div>
                            </div>                        
                        </form>
                    </div>                
                </div>               
            </div>
        </div>
    </AuthenticatedLayout>
</template>