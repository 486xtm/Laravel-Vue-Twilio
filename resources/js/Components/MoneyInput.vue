<script setup>
import { onMounted, ref } from 'vue';

const val = ref(props.modelValue);

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

const props = defineProps({
    textLabel:{
        type: String,
        default: ""
    },
    name: {
        type: String,
        default: ""
    },
    modelValue: {
        type: String,
        default: ""
    },
    placeholder: {
        type: String,
        default: "",
    },
});

</script>

<template>
    <label
        v-if="textLabel"
        :for="textLabel"
        class="block mb-2 text-sm font-mecold dium text-slate-600 dark:text-slate-300"
        >{{ textLabel }}
    </label>
    <input
        :name="name"
        class="border border-slate-300 px-1.5 py-2 rounded-md focus:outline-none focus:border-sky-500 focus:border-2 focus:ring-sky-500 w-full text-sm text-slate-700 placeholder:text-slate-400" 
        v-model.lazy="val"
        v-money3="config"
        :placeholder="placeholder"
    />
</template>