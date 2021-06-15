<template>
    <div class="px-6 my-4">
        <div class="block font-medium text-sm text-gray-700">
            <span v-if="label">{{ label }}</span>
            <div class="pt-2">
                <label class="items-center m-2" v-for="(caption, value) in values"
                       :class="[inline ? 'inline-flex' : 'flex']">
                    <checkbox :value="value" :checked="modelValue.indexOf(value) >= 0" @change="changed"></checkbox>
                    <span class="ml-2 text-sm text-gray-600">{{ caption }}</span>
                </label>
            </div>
        </div>
    </div>
</template>

<script>
import Checkbox from "@/Components/Checkbox";

export default {
    components: {
        Checkbox,
    },

    emits: ['update:modelValue'],

    props: {
        modelValue: Array,
        label: String,
        values: {
            type: Array,
            default: () => ([])
        },
        inline: {
            type: Boolean,
            default: false
        }
    },

    data: () => ({}),

    methods: {
        changed(event) {
            const val = Number(event.target.value);
            let values = Array.from(this.modelValue);
            const ind = values.indexOf(val);
            if (event.target.checked) {
                if (ind === -1) {
                    values.push(val);
                }
            } else {
                if (ind >= 0) {
                    values.splice(ind, 1);
                }
            }
            values.sort((a, b) => a - b);
            this.$emit('update:modelValue', values);
        }
    }
}
</script>
