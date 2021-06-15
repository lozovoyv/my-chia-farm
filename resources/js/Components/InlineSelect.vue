<template>
    <div class="text-base h-6 align-top border border-blue-300 focus:border-blue-700 ml-2 px-1.5 rounded-sm relative bg-white inline-block">
        <div @click="open = ! open" class="cursor-pointer">
                <span class="h-6 inline-block rounded-md">
                    <span class="text-sm inline-block h-6 align-middle" :class="{'text-blue-700': !hasValue}">{{ hasValue ? modelValue : placeholder }}</span>
                    <svg class="float-right h-6 w-5 inline-block align-top" xmlns="http://www.w3.org/2000/svg"
                         viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                              d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                              clip-rule="evenodd"/>
                    </svg>
                </span>
        </div>

        <!-- Full Screen Dropdown Overlay -->
        <div v-show="open" class="fixed inset-0 z-40" @click="open = false">
        </div>

        <transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="transform opacity-0 scale-95"
            enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95">
            <div v-show="open"
                 class="absolute z-50 rounded-md shadow-lg origin-top-left left-0"
                 style="display: none;"
                 @click="open = false">
                <div class="rounded-md ring-1 ring-black ring-opacity-5 py-1 bg-white">
                    <p class="px-4 py-1 cursor-pointer hover:bg-green-300 whitespace-nowrap" v-for="(value, key) in values"
                       :key="key" @click="$emit('update:modelValue', value, key)">{{ value }}</p>
                </div>
            </div>
        </transition>
    </div>
</template>

<script>
import BreezeDropdown from "@/Components/Dropdown";
import {onMounted, onUnmounted, ref} from "vue";

export default {
    name: "Select",

    components: {
        BreezeDropdown,
    },

    props: ['modelValue', 'values', 'placeholder'],

    emits: ['update:modelValue'],

    setup() {
        let open = ref(false)

        const closeOnEscape = (e) => {
            if (open.value && e.keyCode === 27) {
                open.value = false
            }
        }

        onMounted(() => document.addEventListener('keydown', closeOnEscape))
        onUnmounted(() => document.removeEventListener('keydown', closeOnEscape))

        return {
            open,
        }
    },

    computed: {
      hasValue: function () {
          return this.modelValue !== null && this.modelValue !== '';
      }
    },
}
</script>

