<template>
    <input type="checkbox" :value="value" v-model="proxyChecked"
           class="w-5 h-5 rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-white focus:ring-opacity-100">
</template>

<script>
export default {
    emits: ['update:checked'],

    props: {
        checked: {
            type: [Array, Boolean],
            default: false,
        },
        value: {
            default: null,
        },
    },

    computed: {
        proxyChecked: {
            get() {
                return this.checked;
            },

            set(val) {
                this.$emit("update:checked", val);
            },
        },
    },
}
</script>
