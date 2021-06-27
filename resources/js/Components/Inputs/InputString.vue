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
        <label class="block font-medium text-sm text-gray-700">
            <span v-if="title"><span v-if="required" class="text-red-700 mr-1">*</span>{{ title }}</span>
            <input
                class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                :value="modelValue"
                @input="update"
                :type="type"
                :required="required"
                ref="input">
            <span v-if="!valid" class="text-red-700 mr-1 mt-1 block">{{ errorMessage }}</span>
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
            this.$emit('update:modelValue', String(event.target.value));
        },
    }
}
</script>

