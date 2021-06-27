<!--
  -  This file is part of the MyChiaFarm project.
  -
  -    (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
  -
  -  For the full copyright and license information, please view the LICENSE
  -  file that was distributed with this source code.
  -->

<template>
    <div
        class="text-base h-6 align-top border ml-2 px-1.5 rounded-sm relative bg-white inline-block"
        :class="classes"
    >
        <div @click="open = ! open" class="cursor-pointer">
                <span class="h-6 inline-block rounded-md">
                    <span class="text-sm inline-block h-6 align-middle"
                          :class="{'text-blue-700': !hasValue && valid, 'text-red-700':!valid}">{{
                            displayValue
                        }}</span>
                    <icon-drop-down :class="'float-right h-6 w-5 align-top'"/>
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
                    <p class="px-4 py-1 cursor-pointer hover:bg-green-300 whitespace-nowrap"
                       v-for="(value, key) in values"
                       :key="key" @click="$emit('select', key)">{{ value }}</p>
                </div>
            </div>
        </transition>
    </div>
</template>

<script>
import {onMounted, onUnmounted, ref} from "vue";
import IconDropDown from "@/Components/Icons/DropDown";

export default {
    components: {
        IconDropDown,
    },

    props: {
        selected: {type: [String, Number], default: null},
        values: {type: Object, default: () => ({})},
        placeholder: {type: String, default: null},
        valid: {type: Boolean, default: true},
    },

    emits: ['select'],

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
            return typeof this.values[this.selected] !== 'undefined';
        },

        displayValue() {
            return this.hasValue ? this.values[this.selected] : this.placeholder;
        },

        classes() {
            return this.valid ?
                (this.open ? 'border-blue-500' : 'border-blue-300')
                : (this.open ? 'border-red-500' : 'border-red-300')
        },
    },
}
</script>

