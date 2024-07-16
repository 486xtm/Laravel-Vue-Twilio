<script setup>
import { computed, onMounted } from "vue";
import Button from '@/Components/Button.vue';

const props = defineProps({
    text:{
        type: String,
        default: "default",
    },
    show:{
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(['close']);

function onClose(){
    emit('close');
    alert(props.show);
}   

onMounted(() => {
    console.log(props.show);
});

</script>

<template>
    <div v-if="show">
        <h1>{{ text }}</h1>
        <Button styleType="primary" type="button" @click="onClose()">Fechar</Button>
    </div>
</template>