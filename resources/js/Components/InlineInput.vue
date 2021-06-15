<template>
    <span class="h-6 align-top ml-2">
        <input class="rounded-sm w-full border border-blue-300 focus:border-blue-700 px-1.5 outline-none rounded-sm bg-white h-6 placeholder-blue-700"
               :value="modelValue"
               :placeholder="placeholder"
               @input="emit" ref="input"
               :list="list"
        >
    </span>
</template>

<script>
export default {
    props: ['modelValue', 'placeholder', 'list'],

    emits: ['update:modelValue'],

    methods: {
        focus() {
            this.$refs.input.focus()
        },

        emit(event){
            this.$emit('update:modelValue', event.target.value);
        },
    }
}
</script>

