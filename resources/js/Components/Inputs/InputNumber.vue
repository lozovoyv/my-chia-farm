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
        <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">
            <span v-if="title"><span v-if="required" class="text-red-700 dark:text-red-400 mr-1">*</span>{{ title }}</span>
            <input
                class="border-gray-300 dark:bg-gray-700 dark:border-gray-500 border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                :value="modelValue"
                @input="update"
                :type="'number'"
                :required="required"
                ref="input">
            <span v-if="!valid" class="text-red-700 dark:text-red-400 mr-1 mt-1 block">{{ errorMessage }}</span>
        </label>
    </div>
</template>

<script>
export default {
    props: ['modelValue', 'title', 'inputType', 'required', 'options', 'valid', 'errorMessage'],

    emits: ['update:modelValue'],

    computed: {
        type: function () {
            return this.inputType === null ? 'text' : this.inputType;
        }
    },

    methods: {
        focus() {
            this.$refs.input.focus()
        },

        update(event) {
            this.$emit('update:modelValue', Number(event.target.value));
        },
    }
}
</script>

