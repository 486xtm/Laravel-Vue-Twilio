<script setup>
import { ref, onMounted } from 'vue'
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { XMarkIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    showSlide:{
        type: Boolean,
        default: false
    },
    title:{
        type: String,
        default: ''
    },
})

const emit = defineEmits(['close']);

function closeModal() {
    emit('close');
}

</script>
<template>
    <TransitionRoot as="template" :show="showSlide">
        <Dialog as="div" class="relative z-10"  @click="closeModal()">
        
            <TransitionChild as="template" enter="ease-in-out duration-500" enter-from="opacity-0" enter-to="opacity-100" leave="ease-in-out duration-500" leave-from="opacity-100" leave-to="opacity-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
            </TransitionChild>

            <div class="fixed inset-0 overflow-hidden">
                <div class="absolute inset-0 overflow-hidden">
                <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                    <TransitionChild as="template" enter="transform transition ease-in-out duration-500 sm:duration-700" enter-from="translate-x-full" enter-to="translate-x-0" leave="transform transition ease-in-out duration-500 sm:duration-700" leave-from="translate-x-0" leave-to="translate-x-full">
                    <DialogPanel class="pointer-events-auto relative w-screen max-w-md">
                        <TransitionChild as="template" enter="ease-in-out duration-500" enter-from="opacity-0" enter-to="opacity-100" leave="ease-in-out duration-500" leave-from="opacity-100" leave-to="opacity-0">
                        <div class="absolute left-0 top-0 -ml-8 flex pr-2 pt-4 sm:-ml-10 sm:pr-4">
                            <button type="button" class="rounded-md text-gray-300 hover:text-white focus:outline-none focus:ring-2 focus:ring-white" @click="closeModal()">
                            <span class="sr-only">Close panel</span>
                            <XMarkIcon class="h-6 w-6" aria-hidden="true" />
                            </button>
                        </div>
                        </TransitionChild>
                        <div class="flex h-full flex-col overflow-y-scroll bg-white py-0 shadow-xl">
                            <div class="px-0">
                                <DialogTitle class="bg-sky-900 px-3 py-4 text-base font-medium text-white">{{ title }}</DialogTitle>
                            </div>
                            <div class="relative flex-1" >
                                <!-- Your content -->
                                <slot v-if="showSlide" />
                            </div>
                        </div>
                    </DialogPanel>
                    </TransitionChild>
                </div>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>

