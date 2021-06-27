<!--
  -  This file is part of the MyChiaFarm project.
  -
  -    (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
  -
  -  For the full copyright and license information, please view the LICENSE
  -  file that was distributed with this source code.
  -->

<template>
    <input ref="input"
           class="px-1.5 ml-2 h-6 w-20 border outline-none bg-white rounded-sm align-top inline-block text-sm"
           :class="classes"
           :placeholder="placeholder"
           :type="'text'"
           :required="required"
           :list="list"
           :value="modelValue"
           @input="$emit('update:modelValue', $event.target.value)"
    />
</template>

<script>
export default {
    emits: ['update:modelValue'],

    props: {
        modelValue: [String, Number],
        valid: {type: Boolean, default: true},
        placeholder: {type: String, default: null},
        required: {type: Boolean, default: false},
        list: {type: String, default: null},
    },

    computed: {
        classes() {
            return this.valid ? 'border-blue-300 focus:border-blue-500' : 'border-red-300 focus:border-red-500 placeholder-red-700 focus:ring-red-700'
        }
    }
}
</script>
