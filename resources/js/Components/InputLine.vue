<template>
    <div class="px-6 my-4">
        <label class="block font-medium text-sm text-gray-700">
            <span v-if="label">{{ label }}</span>
            <input
                class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                :value="modelValue"
                :placeholder="placeholder"
                @input="$emit('update:modelValue', $event.target.value)"
                :type="type"
                ref="input">
        </label>
    </div>
</template>

<script>
export default {
    props: ['modelValue', 'label', 'type', 'placeholder'],

    emits: ['update:modelValue'],

    methods: {
        focus() {
            this.$refs.input.focus()
        }
    }
}
</script>

