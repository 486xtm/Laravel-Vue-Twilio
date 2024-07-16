<script setup>
import { onMounted, ref } from 'vue';

const props = defineProps({
    textLabel:{
        type: String,
        default: ""
    },
    modelValue: {
        type: String,
        default: "",
    },
    placeholder: {
        type: String,
        default: "",
    },
    type: {
        type: String,
        default: "",
    },
    mask: {
        type: String,
        default: "",
    },
    disabled: {
        type: Boolean,
        default: false,
    }
});

defineEmits(['update:modelValue']);

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <label
        v-if="textLabel"
        :for="textLabel"
        class="block mb-2 text-sm font-mecold dium text-slate-600 dark:text-slate-300"
        >{{ textLabel }}
    </label>
    <div v-if="mask === '' ">
        <input
            class="border border-slate-300 px-1.5 py-1.5 rounded-md focus:outline-none focus:border-sky-500 focus:border-2 focus:ring-sky-500 w-full text-sm text-slate-700 placeholder:text-slate-400" 
            :type="type"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            ref="input"
            :placeholder="placeholder"
            :disabled="disabled"
        />
    </div>
    <div v-else>
        <input
            class="border border-slate-300 px-1.5 py-2 rounded-md focus:outline-none focus:border-sky-500 focus:border-2 focus:ring-sky-500 w-full text-sm text-slate-700 placeholder:text-slate-400" 
            :type="type"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            ref="input"
            :placeholder="placeholder"
            v-mask="mask"
        />
    </div>
</template>