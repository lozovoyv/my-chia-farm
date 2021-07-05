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
        <label class="cursor-pointer">
            <input type="checkbox" :value="modelValue" v-model="proxyChecked"
                   class="w-5 h-5 cursor-pointer rounded border-gray-300 text-green-600 dark:text-green-800 shadow-sm focus:border-gray-400 dark:focus:border-gray-300 focus:ring-offset-0 focus:ring-0 bg-transparent dark:border-gray-500">
            <span class="ml-2 text-sm text-gray-600 dark:text-gray-300" v-if="title">{{ title }}</span><slot/>
            <span v-if="!valid" class="text-red-700 dark:text-red-400 mr-1 mt-1 block">{{ errorMessage }}</span>
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
