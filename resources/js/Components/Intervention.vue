<script setup>
import { onMounted, ref } from 'vue';
import { Modal } from 'flowbite-vue'
import ButtonLink from '@/Components/ButtonLink.vue';

defineProps({
    show: {
        type: Boolean,
        Default: false
    },
});

</script>

<template>
    <Modal :size="size" v-if="show" @close="closeModal">
        <template #header>
            <div class="flex items-center text-lg">
            Primeiro Acesso
            </div>
        </template>
        <template #body>
            <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
            Bem-vindo ao CRM da Porto Brasil Consórcios.
            </p>
            <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400 mt-4">
            Seu cadastro foi realizado com successo em até 24h seu acesso ao sistema estará liberado.
            </p>
            <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400 mt-4">
            Obrigado,<br>Equipe de Atendimento
            </p>
        </template>
        <template #footer>
            <div class="flex justify-end">
                <ButtonLink variant="primary" :href="route('logout')" method="post" as="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    Sair
                </ButtonLink>
            </div>
        </template>
    </Modal>
</template>
