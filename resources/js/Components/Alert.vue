<script setup>
    import { Alert } from 'flowbite-vue';
    import { ref, computed} from 'vue';

    const props = defineProps({
        type:{
            type: String,
            default: "default",
        }
    });

</script>
<template>
    <Alert :type="type" closable class="mb-2"><slot></slot></Alert>
</template>