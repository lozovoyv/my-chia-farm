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
        <div class="block font-medium text-sm text-gray-700">
            <span v-if="title">{{ title }}</span>
            <div class="pt-2">
                <label class="items-center m-2 inline-flex" v-for="(caption, value) in options">
                    <input type="checkbox" :value="value" :checked="modelValue.indexOf(value) >= 0" @change="changed"
                           class="w-5 h-5 rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-white focus:ring-opacity-100">
                    <span class="ml-2 text-sm text-gray-600">{{ caption }}</span>
                </label>
            </div>
            <span v-if="!valid" class="text-red-700 mr-1 mt-1 block">{{ errorMessage }}</span>
        </div>
    </div>
</template>

<script>
export default {
    emits: ['update:modelValue'],

    props: ['modelValue', 'title', 'inputType', 'required', 'options', 'valid', 'errorMessage'],

    computed: {
        proxyChecked: {
            get() {
                return this.modelValue;
            },

            set(val) {
                this.$emit("update:modelValue", val);
            },
        },
    },

    methods: {
        changed(event) {
            const val = Number(event.target.value);
            let values = Array.from(this.modelValue);
            const ind = values.indexOf(val);
            if (event.target.checked) {
                if (ind === -1) {
                    values.push(val);
                }
            } else {
                if (ind >= 0) {
                    values.splice(ind, 1);
                }
            }
            values.sort((a, b) => a - b);
            this.$emit('update:modelValue', values);
        }
    },
}
</script>
