<template>
    <div class="px-6 my-4">
        <label class="flex items-center">
            <input type="checkbox" :value="value" v-model="proxyChecked"
                   class="w-5 h-5 rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-white focus:ring-opacity-100">
            <span class="ml-2 text-sm text-gray-600" v-if="label">{{ label }}</span>
        </label>
    </div>
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
        label: {
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
