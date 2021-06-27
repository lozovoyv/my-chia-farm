<!--
  -  This file is part of the MyChiaFarm project.
  -
  -    (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
  -
  -  For the full copyright and license information, please view the LICENSE
  -  file that was distributed with this source code.
  -->

<template>
    <label class="px-1.5 ml-2 h-6 outline-none align-top inline-block">
        <input type="checkbox" :value="modelValue" v-model="proxyChecked"
               class="w-5 h-5 box-border align-top top-px relative rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-white focus:ring-opacity-100">
        <span class="ml-2 text-sm" v-if="title">{{ title }}</span>
    </label>
</template>

<script>
export default {
    emits: ['update:modelValue'],

    props: ['modelValue', 'title'],

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
