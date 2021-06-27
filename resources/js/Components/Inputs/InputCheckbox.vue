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
        <label>
            <input type="checkbox" :value="modelValue" v-model="proxyChecked"
                   class="w-5 h-5 rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-white focus:ring-opacity-100">
            <span class="ml-2 text-sm text-gray-600" v-if="title">{{ title }}</span><slot/>
            <span v-if="!valid" class="text-red-700 mr-1 mt-1 block">{{ errorMessage }}</span>
        </label>
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
}
</script>
