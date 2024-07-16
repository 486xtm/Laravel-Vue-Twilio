<script setup>
import { ref, watch,  reactive, computed, onMounted  } from 'vue';
import Button from '@/Components/Button.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import SlideOver from '@/Components/SlideOver.vue';

const success = ref(false);
const props = defineProps({
    lead: Object,
    reasons: Object,
    show: Boolean, 
    page: String,
});

const form = useForm({
   reason: null,
   lead_id: null,
   page: props.page,
});

function storeProposal(errors){
    form.lead_id = props.lead.id;
    form.put('/lead-arquivar',{
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
    <SlideOver :showSlide="show" title="Arquivamento" @close="closeModal">
        <div v-if="!success" class="text-slate-700">
            <div class="bg-white">
                <div class="px-4 py-2 mt-3 mb-2">
                    <div class="bg-sky-50 p-5 rounded-md space-y-1">
                        <p class="font-semibold">{{ lead.name }}</p>
                    </div>
                </div>
                <form @submit.prevent="storeProposal">
                    <div class="px-5 space-y-4">
                        <div class="sm:col-span-3">
                            <InputLabel
                                for="Motivo"
                                class="block mb-2 text-sm font-mecold dium text-slate-900 dark:text-slate-300"
                                >Motivo
                            </InputLabel>
                            <select
                                v-model="form.reason"
                                name="reason"
                                class="border-slate-300 focus:border-sky-500 focus:ring-sky-500 rounded-md shadow-sm text-sm w-full"
                                >
                                <option :value="null">Selecione</option>
                                <option 
                                    v-for="item in reasons"
                                    :key="item.id" 
                                    :value="`${item.id}`">
                                    {{ item.name }}
                                </option>
                            </select>
                            <div
                                v-if="form.errors.reason"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.reason }}
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
            <h1 class="text-lg font-extralight">Motivo registrado com sucesso</h1>
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

