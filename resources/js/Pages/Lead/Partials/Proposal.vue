<script setup>
import { ref, watch,  reactive, computed, onMounted  } from 'vue';
import moment from "moment";
import Button from '@/Components/Button.vue';
import ButtonLink from '@/Components/ButtonLink.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';
import SlideOver from '@/Components/SlideOver.vue';
import MoneyInput from '@/Components/MoneyInput.vue';

const success = ref(false);

const props = defineProps({
    lead: Object,
    show: Boolean, 
    types: Object,
    products: Object,
    page: String,
});

const config = {
    thousands: '.',
    decimal: ',',
    precision: 2,
    disableNegative: false,
    disabled: false,
    allowBlank: false,
    minimumNumberOfCharacters: 0,
    shouldRound: false,
    focusOnRight: false,
    modelModifiers: {
        number: true,
    },
};

const form = useForm({
   group: null,
   quota: null,
   proposal_type: null,
   product: null,
   price: null,
   comission: null,
   observation: null,
   lead_id: null,
   page: props.page,
});

function storeProposal(errors){
    form.lead_id = props.lead.id;
    form.post('/proposta',{
        onSuccess: () => successForm(),
    });
};

function successForm(){
    success.value = true;
    form.reset();
}

const emit = defineEmits(['closeSlide']);

function closeModal() {
    emit('closeSlide');
    success.value = false;
}

</script>
<template>
    <SlideOver :showSlide="show" title="Nova Proposta" @close="closeModal">
        <div v-if="!success" class="text-slate-700">
            <div class="bg-white">
                <div class="px-4 py-2 mt-2">
                    <div class="bg-sky-50 p-5 rounded-md space-y-1">
                        <p class="font-semibold md:text-sm sm:text-xs">{{ lead.name }}</p>
                        <p class="text-xs"><b>Produto:</b> {{ lead.product }}</p>                        
                    </div>
                </div>
                <form @submit.prevent="storeProposal">
                    <div class="px-5 space-y-3">
                        <div class="mt-1 grid grid-cols-2 space-x-2">
                            <div>
                                <TextInput
                                    textLabel="Grupo"
                                    type="text" 
                                    v-model="form.group"
                                    name="group"
                                    class="w-full"
                                    placeholder=""
                                />
                                <div
                                    v-if="form.errors.group"
                                    class="text-sm text-red-500 mt-2"
                                >
                                    {{ form.errors.group }}
                                </div>
                            </div>
                            <div>
                                <TextInput
                                    textLabel="Cota"
                                    type="text"
                                    v-model="form.quota"
                                    name="quota"
                                    class="w-full"
                                    placeholder=""
                                />
                                <div
                                    v-if="form.errors.quota"
                                    class="text-sm text-red-500 mt-2"
                                >
                                    {{ form.errors.quota }}
                                </div>
                            </div>
                        </div>
                        <div class="mt-1 grid grid-cols-2 space-x-2">
                            <div>
                                <InputLabel
                                    for="Tipos"
                                    class="block mb-2 text-sm font-mecold dium text-slate-900 dark:text-slate-300"
                                    >Tipo
                                </InputLabel>
                                <select
                                    v-model="form.proposal_type"
                                    name="proposal_type"
                                    class="border-slate-300 focus:border-sky-500 focus:ring-sky-500 rounded-md shadow-sm text-sm w-full"
                                    >
                                    <option :value="null">Selecione</option>
                                    <option 
                                        v-for="item in types"
                                        :key="item.id" 
                                        :value="`${item.id}`">
                                        {{ item.name }}
                                    </option>
                                </select>
                                <div
                                    v-if="form.errors.proposal_type"
                                    class="text-sm text-red-600"
                                >
                                    {{ form.errors.proposal_type }}
                                </div>
                            </div>
                            <div>
                                <InputLabel
                                    for="Tipos"
                                    class="block mb-2 text-sm font-mecold dium text-slate-900 dark:text-slate-300"
                                    >Produto
                                </InputLabel>                            
                                <select
                                    v-model="form.product"
                                    name="product"
                                    class="border-slate-300 focus:border-sky-500 focus:ring-sky-500 rounded-md shadow-sm text-sm w-full"
                                    >
                                    <option :value="null">Selecione</option>
                                    <option 
                                        v-for="item in products"
                                        :key="item.id" 
                                        :value="`${item.value}`">
                                        {{ item.label }}
                                    </option>
                                </select>
                                <div
                                    v-if="form.errors.product"
                                    class="text-sm text-red-600"
                                >
                                    {{ form.errors.product }}
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 grid grid-cols-1">
                            <div>
                                <label
                                    for="textLabel"
                                    class="block mb-2 text-sm font-mecold dium text-slate-600 dark:text-slate-300"
                                    >Valor
                                </label>
                                <input
                                    class="border border-slate-300 px-1.5 py-2 rounded-md focus:outline-none focus:border-sky-500 focus:border-2 focus:ring-sky-500 w-full text-sm text-slate-700 placeholder:text-slate-400" 
                                    v-model.lazy="form.price"
                                    name="price"
                                    v-money3="config"
                                    placeholder="0,00"
                                />
                                <div
                                    v-if="form.errors.price"
                                    class="text-sm text-red-500 mt-2"
                                >
                                    {{ form.errors.price }}
                                </div>
                            </div>
                        </div>
                        <div class="sm:col-span-6">
                            <Inputlabel
                                for="Observação"
                                class="block mb-2 text-sm font-mecold dium text-slate-900 dark:text-slate-300"
                                >Observação
                            </Inputlabel>
                            <textarea
                                v-model="form.observation"
                                name="observation"
                                class="border-slate-300 focus:border-sky-500 focus:ring-sky-500 rounded-md shadow-sm text-sm w-full"
                                rows="2" 
                                >
                            </textarea>
                            <div
                                v-if="form.errors.observation"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.observation }}
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
            <h1 class="text-lg font-extralight">Proposta cadastrada com sucesso</h1>
            <div class="mt-6 grid grid-cols-1 gap-x-6 px-5">
                <div class="sm:col-span-4 space-x-2">
                    <Button
                        variant="primary"
                        type="button"
                        @click="closeModal"
                    >
                        FECHAR
                    </Button>                         
                </div>
            </div> 
        </div>
    </SlideOver>
</template>

