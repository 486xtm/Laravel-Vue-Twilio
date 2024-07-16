<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Accordion, AccordionPanel, AccordionHeader, AccordionContent } from 'flowbite-vue'
import Button from '@/Components/Button.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});

const form = useForm({
    name: null,
    phone: null,
    email: null,
    cidade: null,
});

function storeFunnelStep(){
    form.post('/lead/criar');
};


</script>
<style scoped>



</style>
<template>
    <Head title="Porto Brasil Consórcios" />

    <div class="bg-white relative lg:py-0 ">
        <!-- Main navigation container -->
        <nav
        class="flex-no-wrap relative flex w-full items-center justify-between py-2 shadow-md shadow-black/5 dark:bg-slate-600 dark:shadow-black/10 lg:flex-wrap lg:justify-start lg:py-4"
        data-te-navbar-ref>
            <div class="max-w-6xl mx-auto flex w-full flex-wrap items-center justify-between px-3">
                <!-- Hamburger button for mobile view -->
                <button
                class="block border-0 bg-transparent px-2 text-slate-500 hover:no-underline hover:shadow-none focus:no-underline focus:shadow-none focus:outline-none focus:ring-0 dark:text-slate-200 lg:hidden"
                type="button"
                data-te-collapse-init
                data-te-target="#navbarSupportedContent1"
                aria-controls="navbarSupportedContent1"
                aria-expanded="false"
                aria-label="Toggle navigation">
                <!-- Hamburger icon -->
                <span class="[&>svg]:w-7">
                    <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="currentColor"
                    class="h-7 w-7">
                    <path
                        fill-rule="evenodd"
                        d="M3 6.75A.75.75 0 013.75 6h16.5a.75.75 0 010 1.5H3.75A.75.75 0 013 6.75zM3 12a.75.75 0 01.75-.75h16.5a.75.75 0 010 1.5H3.75A.75.75 0 013 12zm0 5.25a.75.75 0 01.75-.75h16.5a.75.75 0 010 1.5H3.75a.75.75 0 01-.75-.75z"
                        clip-rule="evenodd" />
                    </svg>
                </span>
                </button>

                <!-- Collapsible navigation container -->
                <div
                    class="!visible hidden flex-grow basis-[100%] items-center lg:!flex lg:basis-auto"
                    id="navbarSupportedContent1"
                    data-te-collapse-item>
                    <!-- Logo -->
                    <a
                        class="mb-4 mr-10 mt-3 flex items-center text-slate-900 hover:text-slate-900 focus:text-slate-900 dark:text-slate-200 dark:hover:text-slate-400 dark:focus:text-slate-400 lg:mb-0 lg:mt-0"
                        href="#">
                        <ApplicationLogo
                            class="block h-9 w-auto fill-current text-slate-800"
                        />
                    </a>
                    <!-- Left navigation links -->
                    <ul
                        class="list-style-none space-x-6 mr-auto text-sm flex flex-col pl-0 lg:flex-row"
                        data-te-navbar-nav-ref>
                        <li class="mb-4 lg:mb-0 lg:pr-2" data-te-nav-item-ref>
                        <!-- Dashboard link -->
                        <a
                            class="text-slate-500 hover:text-slate-700 focus:text-slate-700 disabled:text-black/30 dark:text-slate-200 dark:hover:text-slate-300 dark:focus:text-slate-300 lg:px-2 [&.active]:text-black/90 dark:[&.active]:text-zinc-400"
                            href="#"
                            data-te-nav-link-ref
                            >Sobre Nós</a
                        >
                        </li>
                        <!-- Team link -->
                        <li class="mb-4 lg:mb-0 lg:pr-2" data-te-nav-item-ref>
                        <a
                            class="text-slate-500 hover:text-slate-700 focus:text-slate-700 disabled:text-black/30 dark:text-slate-200 dark:hover:text-slate-300 dark:focus:text-slate-300 lg:px-2 [&.active]:text-black/90 dark:[&.active]:text-slate-400"
                            href="#"
                            data-te-nav-link-ref
                            >Franquia</a
                        >
                        </li>
                        <!-- Projects link -->
                        <li class="mb-4 lg:mb-0 lg:pr-2" data-te-nav-item-ref>
                        <a
                            class="text-slate-500 hover:text-slate-700 focus:text-slate-700 disabled:text-black/30 dark:text-slate-200 dark:hover:text-slate-300 dark:focus:text-slate-300 lg:px-2 [&.active]:text-black/90 dark:[&.active]:text-slate-400"
                            href="#"
                            data-te-nav-link-ref
                            >Ajuda</a
                        >
                        </li>
                    </ul>
                </div>

                <!-- Right elements -->
                <div class="text-sm relative flex items-center">
                    <Link 
                        :href="route('login')"
                        class="space-x-1 flex text-slate-500 hover:text-slate-700 focus:text-slate-700 disabled:text-black/30 dark:text-slate-200 dark:hover:text-slate-300 dark:focus:text-slate-300 [&.active]:text-black/90 dark:[&.active]:text-slate-400"
                        >
                        <span class="[&>svg]:w-5">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                            </svg>
                        </span>
                        <span class="text-slate-500 hover:text-slate-700 ">Entrar</span>
                    </Link>
                </div>
            </div>
        </nav>
        
        <div class="flex flex-col items-center justify-between pt-0 pr-10 pb-0 pl-10 mt-0 mr-auto mb-0 ml-auto 
            xl:px-0 lg:flex-row">
            <div class="flex flex-col bg-mega items-center w-full pr-10 pb-10 pl-10 lg:py-24 lg:flex-row">
                <div class="w-full bg-cover relative max-w-md lg:max-w-2xl lg:w-7/12">
                    <div class="flex flex-col bg-white items-center justify-center w-full h-full relative lg:pr-10">
                        
                    </div>
                </div>
                <div class="w-full mt-0 mr-0 mb-0 ml-0 relative z-10 max-w-2xl lg:mt-0 lg:w-5/12 ">
                    <div class="flex flex-col items-start justify-start pt-10 pr-10 pb-10 pl-10 bg-white/90 shadow-2xl rounded-xl
                        relative z-10">
                    <p class="w-full text-4xl font-extralight text-center leading-snug">Simuleee seu consórcio</p>
                    <div class="w-full mt-2 mr-0 mb-0 ml-0 relative space-y-8">
                        <form @submit.prevent="storeFunnelStep">
                            <div class="pb-2">
                                <div class="mt-3 grid grid-cols-1">
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
                                        class="text-sm text-red-500 mt-2"
                                    >
                                        {{ form.errors.name }}
                                    </div>
                                </div>
                                <div class="mt-3 grid grid-cols-1">
                                    <TextInput
                                        textLabel="Whatsapp"
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
                                <div class="mt-3 grid grid-cols-1">
                                    <TextInput
                                        textLabel="E-mail"
                                        type="text"
                                        v-model="form.email"
                                        name="email"
                                        class="w-full"
                                        placeholder=""
                                        mask="(##)#####-####"
                                    />
                                    <div
                                        v-if="form.errors.email"
                                        class="text-sm text-red-500 mt-2"
                                    >
                                        {{ form.errors.email }}
                                    </div>
                                </div>
                            </div>
                            <div class="pt-6 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                                <div class="sm:col-span-4 space-x-2">
                                    <Button
                                        variant="primary"
                                        type="submit"
                                        class="font-semibold text-lg py-4"
                                        :disabled="form.processing"
                                        :class="{ 'opacity-25': form.processing }"
                                    >
                                        SOLICITAR SIMULAÇÃO
                                    </Button>
                                </div>
                            </div>                        
                        </form>
                        
                    </div>
                    </div>
                    <svg viewbox="0 0 91 91" class="absolute top-0 left-0 z-0 w-32 h-32 -mt-12 -ml-12 text-yellow-300
                        fill-current"><g stroke="none" strokewidth="1" fillrule="evenodd"><g fillrule="nonzero"><g><g><circle
                        cx="3.261" cy="3.445" r="2.72"/><circle cx="15.296" cy="3.445" r="2.719"/><circle cx="27.333" cy="3.445"
                        r="2.72"/><circle cx="39.369" cy="3.445" r="2.72"/><circle cx="51.405" cy="3.445" r="2.72"/><circle cx="63.441"
                        cy="3.445" r="2.72"/><circle cx="75.479" cy="3.445" r="2.72"/><circle cx="87.514" cy="3.445" r="2.719"/></g><g
                        transform="translate(0 12)"><circle cx="3.261" cy="3.525" r="2.72"/><circle cx="15.296" cy="3.525"
                        r="2.719"/><circle cx="27.333" cy="3.525" r="2.72"/><circle cx="39.369" cy="3.525" r="2.72"/><circle
                        cx="51.405" cy="3.525" r="2.72"/><circle cx="63.441" cy="3.525" r="2.72"/><circle cx="75.479" cy="3.525"
                        r="2.72"/><circle cx="87.514" cy="3.525" r="2.719"/></g><g transform="translate(0 24)"><circle cx="3.261"
                        cy="3.605" r="2.72"/><circle cx="15.296" cy="3.605" r="2.719"/><circle cx="27.333" cy="3.605" r="2.72"/><circle
                        cx="39.369" cy="3.605" r="2.72"/><circle cx="51.405" cy="3.605" r="2.72"/><circle cx="63.441" cy="3.605"
                        r="2.72"/><circle cx="75.479" cy="3.605" r="2.72"/><circle cx="87.514" cy="3.605" r="2.719"/></g><g
                        transform="translate(0 36)"><circle cx="3.261" cy="3.686" r="2.72"/><circle cx="15.296" cy="3.686"
                        r="2.719"/><circle cx="27.333" cy="3.686" r="2.72"/><circle cx="39.369" cy="3.686" r="2.72"/><circle
                        cx="51.405" cy="3.686" r="2.72"/><circle cx="63.441" cy="3.686" r="2.72"/><circle cx="75.479" cy="3.686"
                        r="2.72"/><circle cx="87.514" cy="3.686" r="2.719"/></g><g transform="translate(0 49)"><circle cx="3.261"
                        cy="2.767" r="2.72"/><circle cx="15.296" cy="2.767" r="2.719"/><circle cx="27.333" cy="2.767" r="2.72"/><circle
                        cx="39.369" cy="2.767" r="2.72"/><circle cx="51.405" cy="2.767" r="2.72"/><circle cx="63.441" cy="2.767"
                        r="2.72"/><circle cx="75.479" cy="2.767" r="2.72"/><circle cx="87.514" cy="2.767" r="2.719"/></g><g
                        transform="translate(0 61)"><circle cx="3.261" cy="2.846" r="2.72"/><circle cx="15.296" cy="2.846"
                        r="2.719"/><circle cx="27.333" cy="2.846" r="2.72"/><circle cx="39.369" cy="2.846" r="2.72"/><circle
                        cx="51.405" cy="2.846" r="2.72"/><circle cx="63.441" cy="2.846" r="2.72"/><circle cx="75.479" cy="2.846"
                        r="2.72"/><circle cx="87.514" cy="2.846" r="2.719"/></g><g transform="translate(0 73)"><circle cx="3.261"
                        cy="2.926" r="2.72"/><circle cx="15.296" cy="2.926" r="2.719"/><circle cx="27.333" cy="2.926" r="2.72"/><circle
                        cx="39.369" cy="2.926" r="2.72"/><circle cx="51.405" cy="2.926" r="2.72"/><circle cx="63.441" cy="2.926"
                        r="2.72"/><circle cx="75.479" cy="2.926" r="2.72"/><circle cx="87.514" cy="2.926" r="2.719"/></g><g
                        transform="translate(0 85)"><circle cx="3.261" cy="3.006" r="2.72"/><circle cx="15.296" cy="3.006"
                        r="2.719"/><circle cx="27.333" cy="3.006" r="2.72"/><circle cx="39.369" cy="3.006" r="2.72"/><circle
                        cx="51.405" cy="3.006" r="2.72"/><circle cx="63.441" cy="3.006" r="2.72"/><circle cx="75.479" cy="3.006"
                        r="2.72"/><circle cx="87.514" cy="3.006" r="2.719"/></g></g></g></g></svg>
                    <svg viewbox="0 0 91 91" class="absolute bottom-0 right-0 z-0 w-32 h-32 -mb-12 -mr-12 text-indigo-500
                        fill-current"><g stroke="none" strokewidth="1" fillrule="evenodd"><g fillrule="nonzero"><g><g><circle
                        cx="3.261" cy="3.445" r="2.72"/><circle cx="15.296" cy="3.445" r="2.719"/><circle cx="27.333" cy="3.445"
                        r="2.72"/><circle cx="39.369" cy="3.445" r="2.72"/><circle cx="51.405" cy="3.445" r="2.72"/><circle cx="63.441"
                        cy="3.445" r="2.72"/><circle cx="75.479" cy="3.445" r="2.72"/><circle cx="87.514" cy="3.445" r="2.719"/></g><g
                        transform="translate(0 12)"><circle cx="3.261" cy="3.525" r="2.72"/><circle cx="15.296" cy="3.525"
                        r="2.719"/><circle cx="27.333" cy="3.525" r="2.72"/><circle cx="39.369" cy="3.525" r="2.72"/><circle
                        cx="51.405" cy="3.525" r="2.72"/><circle cx="63.441" cy="3.525" r="2.72"/><circle cx="75.479" cy="3.525"
                        r="2.72"/><circle cx="87.514" cy="3.525" r="2.719"/></g><g transform="translate(0 24)"><circle cx="3.261"
                        cy="3.605" r="2.72"/><circle cx="15.296" cy="3.605" r="2.719"/><circle cx="27.333" cy="3.605" r="2.72"/><circle
                        cx="39.369" cy="3.605" r="2.72"/><circle cx="51.405" cy="3.605" r="2.72"/><circle cx="63.441" cy="3.605"
                        r="2.72"/><circle cx="75.479" cy="3.605" r="2.72"/><circle cx="87.514" cy="3.605" r="2.719"/></g><g
                        transform="translate(0 36)"><circle cx="3.261" cy="3.686" r="2.72"/><circle cx="15.296" cy="3.686"
                        r="2.719"/><circle cx="27.333" cy="3.686" r="2.72"/><circle cx="39.369" cy="3.686" r="2.72"/><circle
                        cx="51.405" cy="3.686" r="2.72"/><circle cx="63.441" cy="3.686" r="2.72"/><circle cx="75.479" cy="3.686"
                        r="2.72"/><circle cx="87.514" cy="3.686" r="2.719"/></g><g transform="translate(0 49)"><circle cx="3.261"
                        cy="2.767" r="2.72"/><circle cx="15.296" cy="2.767" r="2.719"/><circle cx="27.333" cy="2.767" r="2.72"/><circle
                        cx="39.369" cy="2.767" r="2.72"/><circle cx="51.405" cy="2.767" r="2.72"/><circle cx="63.441" cy="2.767"
                        r="2.72"/><circle cx="75.479" cy="2.767" r="2.72"/><circle cx="87.514" cy="2.767" r="2.719"/></g><g
                        transform="translate(0 61)"><circle cx="3.261" cy="2.846" r="2.72"/><circle cx="15.296" cy="2.846"
                        r="2.719"/><circle cx="27.333" cy="2.846" r="2.72"/><circle cx="39.369" cy="2.846" r="2.72"/><circle
                        cx="51.405" cy="2.846" r="2.72"/><circle cx="63.441" cy="2.846" r="2.72"/><circle cx="75.479" cy="2.846"
                        r="2.72"/><circle cx="87.514" cy="2.846" r="2.719"/></g><g transform="translate(0 73)"><circle cx="3.261"
                        cy="2.926" r="2.72"/><circle cx="15.296" cy="2.926" r="2.719"/><circle cx="27.333" cy="2.926" r="2.72"/><circle
                        cx="39.369" cy="2.926" r="2.72"/><circle cx="51.405" cy="2.926" r="2.72"/><circle cx="63.441" cy="2.926"
                        r="2.72"/><circle cx="75.479" cy="2.926" r="2.72"/><circle cx="87.514" cy="2.926" r="2.719"/></g><g
                        transform="translate(0 85)"><circle cx="3.261" cy="3.006" r="2.72"/><circle cx="15.296" cy="3.006"
                        r="2.719"/><circle cx="27.333" cy="3.006" r="2.72"/><circle cx="39.369" cy="3.006" r="2.72"/><circle
                        cx="51.405" cy="3.006" r="2.72"/><circle cx="63.441" cy="3.006" r="2.72"/><circle cx="75.479" cy="3.006"
                        r="2.72"/><circle cx="87.514" cy="3.006" r="2.719"/></g></g></g></g></svg>
                </div>
            </div>            
        </div>
    </div>
    <div class="">
        <section class="bg-sky-500">
            <div class="flex max-w-6xl mx-auto justify-between align-middle py-10">
                <h1 class="text-white text-3xl font-bold">Faça uma simulação e ganhe uma consultoria grátis!</h1>
                <Link 
                    href="#"
                    class="bg-[#61ce70] rounded-lg px-10 py-4 text-white font-bold "
                    >
                    Simule no Whatsapp
                </Link>
            </div>
        </section>
        <section class="my-16 max-w-6xl mx-auto">
            <div class="flex flex-row">
                <div class="basis-1/2 px-2 py-1">
                    <img src="../../assets/sobre-nos.webp" class="rounded-md" alt="Sobre nós" />
                </div>
                <div class="basis-1/2 px-4 text-slate-700">
                    <h1 class="text-4xl pb-4 text-sky-500 font-extralight">Sobre nós</h1>
                    <p class="text-sm py-1 leading-5">A PORTO BRASILCONSORCIOS nasceu da expertise de dois sócios, com o objetivo de ajudar pessoas a realizarem seus SONHOS por meio do melhor tipo de Consórcio existente hoje no mercado.</p>
                    <p class="text-sm py-1 leading-5">São mais de 30 anos de experiência no ramo e nosso diferencial é oferecer aos nossos clientes e parceiros uma OPORTUNIDADE ÚNICA em ser capaz de adquirir seu próprio imóvel, automóvel ou sistema de placas solares. Independente do seu sonho, nossa missão é realizá-lo!</p>
                    <p class="text-sm py-1 leading-5">Contamos com uma equipe especializada para atendimento personalizado, oferecemos consultoria financeira, proporcionando auxílio aos nossos clientes em todas as etapas do planejamento financeiro, a fim de concretizar seus objetivos!</p>
                    <p class="text-sm py-1 leading-5">Chegou a hora de realizar o seu sonho. Conte conosco para te levar até a sua conquista!</p>
                </div>
            </div>
        </section>
        <section class="bg-sky-100 my-16 text-slate-900">
            <div class="max-w-6xl mx-auto">
                <h1 class="font-extralight text-4xl text-center text-slate-700 py-8">Tipos de Consórcio</h1>
                <div class="grid grid-cols-4 gap-8 h-80 justify-between">
                    <div class="">
                        <img src="../../assets/img/consorcio-imovel.jpg" class="w-full rounded-md"/>
                        <h2 class="text-slate-700 font-semibold text-center text-lg pt-4">Imóvel</h2>
                        <p class="font-light text-center px-4">Terreno, construção, reforma ou aquisição de imóvel  e quitação de financiamento</p>
                    </div>
                    <div>
                        <img src="../../assets/img/consorcio-veiculo.jpg" class="w-full rounded-md"/>
                        <h2 class="text-slate-700 font-semibold text-center text-lg pt-4">Auto</h2>
                        <p class="font-light text-center px-4">Carros, motos e náuticos</p>
                    </div>
                    <div>                    
                        <img src="../../assets/img/consorcio-caminhao.jpg" class="w-full rounded-md"/>
                        <h2 class="text-slate-700 font-semibold text-center text-lg pt-4">Imóvel</h2>
                        <p class="font-light text-center px-4">Caminhões, ônibus, vans, implementos agrícolas, tratores e carros de luxo</p>
                    </div>
                    <div>
                        <img src="../../assets/img/consorcio-solar.jpg" class="w-full rounded-md"/>
                        <h2 class="text-slate-700 font-semibold text-center text-lg pt-4">Sustentável</h2>
                        <p class="font-light text-center px-4">Placa solar</p>
                    </div>
                </div>
            </div>
        </section>
        <section class="max-w-6xl mx-auto my-16 text-slate-900">
            <div class="flex flex-row">
                <div class="basis-1/2">
                    <h2 class="text-4xl text-sky-500 font-extralight">Nossos Diferenciais</h2>
                    <ul class="mt-5 space-y-4">
                        <li class="flex space-x-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-sky-500">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                            <span>Menor fundo de reserva do mercado;</span>
                        </li>
                        <li class="flex space-x-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-sky-500">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                            <span>Excedente de contemplações mensais;</span>
                        </li>
                        <li class="flex space-x-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-sky-500">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                            <span>Análise de crédito livre de papéis;</span>
                        </li>
                        <li class="flex space-x-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-sky-500">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                            <span>As parcelas são sem juros;</span>
                        </li>
                        <li class="flex space-x-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-sky-500">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                            <span>As parcelas são sem juros;</span>
                        </li>
                        <li class="flex space-x-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-sky-500">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                            <span>Planos que encaixam no seu bolso;</span>
                        </li>
                        <li class="flex space-x-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-sky-500">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                            <span>Facilidade de contratação, sem burocracia.</span>
                        </li>
                    </ul>
                </div>
                <div class="basis-1/2 px-4">
                    <img src="../../assets/img/familia.png" class="rounded-md" alt="Sobre nós" />
                </div>
            </div>
        </section>
        <section class="max-w-6xl mx-auto my-16 ">
            <h2 class="text-4xl text-sky-500 text-center font-extralight py-10">Perguntas Frequentes</h2>
            <div class="flex w-full justify-center">
                <div class="max-w-3xl">
                    <Accordion flush>
                        <accordion-panel>
                        <accordion-header>
                            <h2 class="font-semibold">1 - COMO O CONSÓRCIO FUNCIONA?</h2>
                        </accordion-header>
                        <accordion-content>
                            <div>
                                <p class="mb-2 text-slate-900 dark:text-slate-900 text-sm leading-6">Consórcio é a reunião de um grupo de pessoas que tem um objetivo em comum: adquirir um bem ou serviço. O grupo é administrado pela Porto Seguro Administradora de Consórcios Ltda. Todos os meses, todos os integrantes contribuem com um valor determinado, formando uma poupança comum a todos. Até o fim do período estipulado em contrato, todos têm acesso à sua carta de crédito e podem adquirir o bem desejado! A contemplação é feita por meio de sorteios ou lances que são realizados, periodicamente, nas assembléias. É possível planejar diversos tipos de aquisição, pois existem cartas de crédito para bens móveis, como: carros, motos e caminhões, como também para imóveis, sendo: casas, apartamentos e até prédios comerciais.</p>
                            </div>
                        </accordion-content>
                        </accordion-panel>
                        <accordion-panel>
                        <accordion-header>
                            <h2 class="font-semibold">2 - COMO FAÇO O PAGAMENTO DAS PARCELAS?</h2>  
                        </accordion-header>
                        <accordion-content>
                            <div>
                                <p class="mb-2 text-slate-900 dark:text-slate-900 text-sm leading-6">A forma de pagamento da primeira parcela pode ser feita por: boleto, boleto pré- datado ou débito automático. As demais parcelas podem ser pagas via boleto ou débito em conta.</p>
                            </div>
                        </accordion-content>
                        </accordion-panel>
                        <accordion-panel>
                            <accordion-header>
                                <h2 class="font-semibold">3 - QUAL É A CHANCE DE SER CONTEMPLADO ATÉ O FIM DO GRUPO?</h2>
                            </accordion-header>
                            <accordion-content>
                                <div>
                                    <p class="mb-2 text-slate-900 dark:text-slate-900 text-sm leading-6">Todos os participantes têm as mesmas chances de serem contemplados! Existem 2 formas para isso acontecer: sorteio e lance vencedor. Caso você não seja contemplado dessa forma, ao final da vigência do grupo, todos os consorciados que não receberam sua carta de crédito serão contemplados.</p>
                                </div>
                            </accordion-content>
                        </accordion-panel>
                        <accordion-panel>
                            <accordion-header>
                                <h2 class="font-semibold">4 - EXISTE LIMITE DE IDADE PARA CONTRATAR UM CONSÓRCIO?</h2>
                            </accordion-header>
                            <accordion-content>
                                <div>
                                    <p class="mb-2 text-slate-900 dark:text-slate-900 text-sm leading-6">Para automóvel, o consórcio está disponível até aos 69 anos. Já para imóvel, 64 anos. Quem está acima dessa faixa de idade, pode solicitar à outra pessoa que contrate em seu lugar. Já no caso de Pessoa Jurídica, não existe limite de idade para contratação.</p>
                                </div>
                            </accordion-content>
                        </accordion-panel>
                        <accordion-panel>
                            <accordion-header>
                                <h2 class="font-semibold">5 - QUAL A DIFERENÇA ENTRE UM CONSÓRCIO E UM FINANCIAMENTO?</h2>
                            </accordion-header>
                            <accordion-content>
                                <div>
                                    <p class="mb-2 text-slate-900 dark:text-slate-900 text-sm leading-6">Consórcio é uma opção de compra programada, em que um grupo de pessoas com o mesmo interesse entra em um fundo, no caso, administrado pela Porto Seguro Consórcio. Não existe a necessidade de investir em uma entrada e você paga somente a taxa administrativa. Já no financiamento, é necessário oferecer um investimento alto de entrada e você sofre com os juros abusivos.</p>
                                </div>
                            </accordion-content>
                        </accordion-panel>
                        <accordion-panel>
                            <accordion-header>
                                <h2 class="font-semibold">6 - AS PARCELAS DO CONSÓRCIO SOFREM REAJUSTE?</h2>
                            </accordion-header>
                            <accordion-content>
                                <div>
                                    <p class="mb-2 text-slate-900 dark:text-slate-900 text-sm leading-6">O reajuste do consórcio é feito pelo INCC – Índice Nacional de Construção Civil em ajuste anual. Ao longo do tempo, os imóveis e os veículos sofrem variações de preços que são provocadas pela inflação ou por outros fatores de mercado. O reajuste do consórcio é, basicamente, a correção do valor da carta de crédito do consorciado. Esse reajuste é utilizado para que o montante obtido ao fim dos pagamentos não desvalorize com o passar dos anos e o cliente não perca poder de compra. A correção é feita baseada na porcentagem do valor do bem!</p>
                                </div>
                            </accordion-content>
                        </accordion-panel>
                    </Accordion>
                </div>
            </div>            
        </section>
        <!-- Footer container -->
        <footer
            class="mt-4 text-center text-slate-600 dark:bg-slate-600 dark:text-slate-200 lg:text-left">
            <div
                class="max-w-6xl mx-auto  flex items-center justify-center border-b-2 border-slate-200 p-6 dark:border-slate-500 lg:justify-between">
                <div class="mr-12 text-sm hidden lg:block">
                    <span>Conecte-se conosco nas redes sociais:</span>
                </div>
                <!-- Social network icons container -->
                <div class="flex justify-end">
                    <a href="#!" class="mr-6 text-sky-500 dark:text-slate-200">
                        <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4"
                        fill="currentColor"
                        viewBox="0 0 24 24">
                        <path
                            d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z" />
                        </svg>
                    </a>
                    <a href="#!" class="mr-6 text-sky-500 dark:text-slate-200">
                        <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4"
                        fill="currentColor"
                        viewBox="0 0 24 24">
                        <path
                            d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                        </svg>
                    </a>
                    <a href="#!" class="mr-6 text-sky-500 dark:text-slate-200">
                        <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4"
                        fill="currentColor"
                        viewBox="0 0 24 24">
                        <path
                            d="M4.98 3.5c0 1.381-1.11 2.5-2.48 2.5s-2.48-1.119-2.48-2.5c0-1.38 1.11-2.5 2.48-2.5s2.48 1.12 2.48 2.5zm.02 4.5h-5v16h5v-16zm7.982 0h-4.968v16h4.969v-8.399c0-4.67 6.029-5.052 6.029 0v8.399h4.988v-10.131c0-7.88-8.922-7.593-11.018-3.714v-2.155z" />
                        </svg>
                    </a>                
                </div>
            </div>

            <!-- Main container div: holds the entire content of the footer, including four sections (Tailwind Elements, Products, Useful links, and Contact), with responsive styling and appropriate padding/margins. -->
            <div class="max-w-6xl mx-auto py-10 text-center md:text-left">
                <div class="grid-1 grid gap-8 md:grid-cols-2 lg:grid-cols-4">
                <!-- Tailwind Elements section -->
                <div class="">
                    <h6
                    class="mb-4 flex items-center text-sky-500 justify-center font-semibold uppercase md:justify-start">
                    <ApplicationLogo
                            class="block mr-2 h-6 w-auto fill-current text-slate-800"
                        />
                    Porto Brasil
                    </h6>
                    <p class="text-sm">
                    Here you can use rows and columns to organize your footer
                    content. Lorem ipsum dolor sit amet, consectetur adipisicing
                    elit.
                    </p>
                </div>
                <!-- Products section -->
                <div class="">
                    <h6
                        class="mb-4 flex justify-center font-semibold text-sky-500 uppercase md:justify-start">
                        Produtos
                    </h6>
                    <p class="mb-4 text-sm">
                        <a href="#!" class="text-slate-600 dark:text-slate-200"
                            >Consórcios</a
                        >
                    </p>
                    <p class="mb-4 text-sm">
                        <a href="#!" class="text-slate-600 dark:text-slate-200"
                            >Seguros</a
                        >
                    </p>
                </div>
                <!-- Useful links section -->
                <div class="">
                    <h6
                    class="mb-4 flex justify-center text-sky-500 font-semibold uppercase md:justify-start">
                    Links Úteis
                    </h6>
                    <p class="mb-4 text-sm">
                        <a href="#!" class="text-slate-600 dark:text-slate-200"
                            >Pricing</a
                        >
                    </p>
                    <p class="mb-4 text-sm">
                        <a href="#!" class="text-slate-600 dark:text-slate-200"
                            >Settings</a
                        >
                    </p>
                    <p class="mb-4 text-sm">
                        <a href="#!" class="text-slate-600 dark:text-slate-200"
                            >Orders</a
                        >
                    </p>
                    <p class="text-sm">
                        <a href="#!" class="text-slate-600 dark:text-slate-200"
                            >Help</a
                        >
                    </p>
                </div>
                <!-- Contact section -->
                <div>
                    <h6
                    class="mb-4 flex text-sky-500 justify-center font-semibold uppercase md:justify-start">
                    Fale conosco
                    </h6>
                    <p class="mb-4 flex text-sm items-center justify-center md:justify-start">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="currentColor"
                            class="mr-3 h-8 w-8">
                            <path
                            d="M11.47 3.84a.75.75 0 011.06 0l8.69 8.69a.75.75 0 101.06-1.06l-8.689-8.69a2.25 2.25 0 00-3.182 0l-8.69 8.69a.75.75 0 001.061 1.06l8.69-8.69z" />
                            <path
                            d="M12 5.432l8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 01-.75-.75v-4.5a.75.75 0 00-.75-.75h-3a.75.75 0 00-.75.75V21a.75.75 0 01-.75.75H5.625a1.875 1.875 0 01-1.875-1.875v-6.198a2.29 2.29 0 00.091-.086L12 5.43z" />
                        </svg>
                        Av. São João, 2405 – 16º Andar Jardim Esplanada
                    </p>
                    <p class="mb-4 text-sm flex items-center justify-center md:justify-start">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="currentColor"
                            class="mr-3 h-5 w-5">
                            <path
                            d="M1.5 8.67v8.58a3 3 0 003 3h15a3 3 0 003-3V8.67l-8.928 5.493a3 3 0 01-3.144 0L1.5 8.67z" />
                            <path
                            d="M22.5 6.908V6.75a3 3 0 00-3-3h-15a3 3 0 00-3 3v.158l9.714 5.978a1.5 1.5 0 001.572 0L22.5 6.908z" />
                        </svg>
                        beto@portobrasilconsorcios.com.br
                        </p>
                    <p class="mb-4 text-sm flex items-center justify-center md:justify-start">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="currentColor"
                            class="mr-3 h-5 w-5">
                            <path
                            fill-rule="evenodd"
                            d="M1.5 4.5a3 3 0 013-3h1.372c.86 0 1.61.586 1.819 1.42l1.105 4.423a1.875 1.875 0 01-.694 1.955l-1.293.97c-.135.101-.164.249-.126.352a11.285 11.285 0 006.697 6.697c.103.038.25.009.352-.126l.97-1.293a1.875 1.875 0 011.955-.694l4.423 1.105c.834.209 1.42.959 1.42 1.82V19.5a3 3 0 01-3 3h-2.25C8.552 22.5 1.5 15.448 1.5 6.75V4.5z"
                            clip-rule="evenodd" />
                        </svg>
                        (12) 3500-1627
                    </p>
                    <p class="flex items-center text-sm justify-center md:justify-start">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="currentColor"
                            class="mr-3 h-5 w-5">
                            <path
                            fill-rule="evenodd"
                            d="M1.5 4.5a3 3 0 013-3h1.372c.86 0 1.61.586 1.819 1.42l1.105 4.423a1.875 1.875 0 01-.694 1.955l-1.293.97c-.135.101-.164.249-.126.352a11.285 11.285 0 006.697 6.697c.103.038.25.009.352-.126l.97-1.293a1.875 1.875 0 011.955-.694l4.423 1.105c.834.209 1.42.959 1.42 1.82V19.5a3 3 0 01-3 3h-2.25C8.552 22.5 1.5 15.448 1.5 6.75V4.5z"
                            clip-rule="evenodd" />
                        </svg>
                        (12) 99670-0015
                    </p>
                </div>
                </div>
            </div>
        </footer><br>
    </div>
    <!--Copyright section-->
    <div class="bg-sky-500 text-white text-sm p-4 text-center dark:bg-slate-700">
        <span>© 2023 Copyright:</span>
        <a
        class="font-semibold dark:text-slate-400"
        href="https://tailwind-elements.com/"
        > Porto Brasil Consórcios</a
        >
    </div>
</template>

<style>

html {
    font-family: Proxima Nova, system-ui, sans-serif;
}

.bg-mega {
    background-image: url("../../assets/img/banner.webp");
}

.bg-about-us {
    background-image: url("");
}
@media (prefers-color-scheme: dark) {
    .dark\:bg-dots-lighter {
        background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(255,255,255,0.07)'/%3E%3C/svg%3E");
    }
}
</style>
