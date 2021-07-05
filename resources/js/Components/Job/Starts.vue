<!--
  -  This file is part of the MyChiaFarm project.
  -
  -    (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
  -
  -  For the full copyright and license information, please view the LICENSE
  -  file that was distributed with this source code.
  -->

<template>
    <div class="px-6 my-4" :class="{'my-1.5':!edit_mode}">
        <p class="text-gray-800 dark:text-gray-300" v-if="!hasStarts" :class="{'text-sm':!edit_mode}">No workers starts defined</p>
        <datalist :id="'starts-events-list-'+job_id" v-if="hasStarts && edit_mode">
            <option v-for="option in events_list">{{ option }}</option>
        </datalist>
        <div v-if="hasStarts" v-for="(start, key) in modelValue" :key="key">
            <div class="p-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600" v-if="edit_mode">
                <span class="h-6 align-top inline-block text-gray-800 dark:text-gray-300" style="line-height: 1.5rem">Start</span>
                <inline-input :required="true" :placeholder="'number of'"
                              :valid="isValid(key, 'number_of_workers')"
                              :modelValue="start['number_of_workers']"
                              @update:modelValue="val => updateStart(key, 'number_of_workers', val)"
                />
                <span class="h-6 align-top ml-2 inline-block text-gray-800 dark:text-gray-300" style="line-height: 1.5rem">worker(s) on</span>
                <inline-input :required="true" :placeholder="'event name'" class="w-28"
                              :valid="isValid(key, 'event_name')"
                              :modelValue="start['event_name']"
                              @update:modelValue="val => updateStart(key, 'event_name', val)"
                              :list="'starts-events-list-'+job_id"
                />
                <icon-close :class="'inline float-right m-1'" @click="removeStart(key)"/>
            </div>
            <div class="text-sm my-1.5 text-gray-800 dark:text-gray-300" v-if="!edit_mode">
                <span>Start </span>
                <span class="font-bold ml-1">{{ start['number_of_workers'] }}</span>
                <span class="ml-1">worker(s) on</span>
                <span class="font-bold ml-1">{{ start['event_name'] }}</span>
            </div>
        </div>
        <div class="my-4" v-if="edit_mode">
            <span class="text-sm cursor-pointer text-blue-700 dark:text-blue-500 underline" @click="addStart">add worker start</span>
        </div>
    </div>
</template>

<script>
import InlineInput from "@/Components/Inputs/InlineInput";
import IconClose from "@/Components/Icons/Close";
import clone from "@/Helpers/Lib/clone";
import empty from "@/Helpers/Lib/empty";

export default {
    components: {
        IconClose,
        InlineInput,
    },

    emits: ['update:modelValue', 'validation'],

    props: {
        edit_mode: Boolean,
        modelValue: {type: Array, default: () => ([])},
        events_list: {type: Array, default: () => ([])},
        job_id: {type: Number, default: 0}
    },

    computed: {
        hasStarts() {
            return this.modelValue.length > 0;
        }
    },

    methods: {
        isValid(index, attribute) {
            let valid = typeof this.modelValue[index][attribute] !== 'undefined' && !empty(this.modelValue[index][attribute])
            if (attribute === 'number_of_workers') {
                valid = valid && !isNaN(this.modelValue[index][attribute]);
            }
            return valid;
        },

        addStart() {
            let value = clone(this.modelValue);
            value.push({});
            this.$emit('update:modelValue', value);
            this.validate(value);
        },

        updateStart(index, attribute, val) {
            let value = clone(this.modelValue);
            value[index][attribute] = val;
            this.$emit('update:modelValue', value);
            this.validate(value);
        },

        removeStart(index) {
            let value = clone(this.modelValue);
            value.splice(index, 1);
            this.$emit('update:modelValue', value);
            this.validate(value);
        },

        validate(value) {
            let valid = !Object.keys(value).some(key => {
                return typeof value[key]['number_of_workers'] === 'undefined' || empty(value[key]['number_of_workers']) || isNaN(value[key]['number_of_workers']) ||
                    typeof value[key]['event_name'] === 'undefined' || empty(value[key]['event_name']);
            });
            this.$emit('validation', valid);
        },
    },
}
</script>
