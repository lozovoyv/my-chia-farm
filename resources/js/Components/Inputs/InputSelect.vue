<!--
  -  This file is part of the MyChiaFarm project.
  -
  -    (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
  -
  -  For the full copyright and license information, please view the LICENSE
  -  file that was distributed with this source code.
  -->

<template>
    <div class="px-6 my-4">
        <div class="block font-medium text-sm text-gray-700 dark:text-gray-300">
            <span v-if="title">{{ title }}</span>
            <div
                class="text-base h-10 border border-gray-300 dark:border-gray-500 focus:border-indigo-300 px-3 py-2 rounded-md shadow-sm mt-1 block w-full relative bg-white dark:text-gray-300 dark:bg-gray-700">
                <div @click="open = ! open" class="cursor-pointer">
                <span class="h-6 inline-block w-full rounded-md">{{ modelValue }}
                    <svg class="float-right h-6 w-5" xmlns="http://www.w3.org/2000/svg"
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
                         class="absolute z-50 mt-2 rounded-md shadow-lg origin-top-left left-0 w-full"
                         style="display: none;"
                         @click="open = false">
                        <div class="rounded-md ring-1 ring-black ring-opacity-5 py-1 bg-white dark:bg-gray-700">
                            <p class="px-4 py-1 cursor-pointer hover:bg-green-300 dark:hover:bg-green-800" v-for="(value, key) in options"
                               :key="key" @click="$emit('update:modelValue', value)">{{ value }}</p>
                        </div>
                    </div>
                </transition>
            </div>
            <span v-if="!valid" class="text-red-700 dark:text-red-400 mr-1 mt-1 block">{{ errorMessage }}</span>
        </div>
    </div>
</template>

<script>
import {onMounted, onUnmounted, ref} from "vue";

export default {
    name: "Select",

    props: ['modelValue', 'title', 'inputType', 'required', 'options', 'valid', 'errorMessage'],

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
}
</script>

