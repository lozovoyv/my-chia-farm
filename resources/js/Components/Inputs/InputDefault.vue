<!--
  -  This file is part of the MyChiaFarm project.
  -
  -    (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
  -
  -  For the full copyright and license information, please view the LICENSE
  -  file that was distributed with this source code.
  -->

<template>
    <div>
        <m-c-f-input :type="'bool'" v-model="proxyUseDefault" :title="default_caption">
            <span class="text-sm text-gray-900 dark:text-gray-300" :class="{'text-red-700 dark:text-red-400':!defaultValid}" v-if="proxyUseDefault">: {{
                    defaultValid ? (empty(default_value) ? 'not set' : default_value) : errorDefaultMessage
                }}</span>
        </m-c-f-input>
        <m-c-f-input
            v-if="!proxyUseDefault"
            :type="type"
            :required="required"
            :title="title"
            :options="options"
            :inputType="inputType"
            :errorMessage="errorMessage"
            v-model="proxyModel"
            @validation="validation"
        />
    </div>
</template>

<script>
import empty from "@/Helpers/Lib/empty";
import MCFInput from "@/Components/Inputs/Input";

export default {
    name: "Input",

    props: {
        modelValue: [String, Number, Boolean, Array, Object],
        type: {type: String, default: 'string'},
        required: {type: Boolean, default: false},
        title: {type: String, default: null},
        options: {type: [Array, Object], default: () => ([])},
        inputType: {type: String, default: null},
        errorMessage: {type: String, default: 'This field must be filled'},

        default_caption: {type: String, default: 'Use default'},
        default_value: [String, Number, Boolean, Array, Object],
        use_default: {type: Boolean, default: false},
        errorDefaultMessage: {type: String, default: 'not set'},
    },

    emits: ['update:modelValue', 'update:use_default', 'validation'],

    components: {
        MCFInput,
    },

    computed: {
        proxyModel: {
            get() {
                return this.modelValue;
            },

            set(val) {
                this.$emit("update:modelValue", val);
            },
        },
        proxyUseDefault: {
            get() {
                return this.use_default;
            },

            set(val) {
                this.$emit("update:use_default", val);
            },
        },
        defaultValid: function () {
            return !this.required || !this.empty(this.default_value);
        },
    },

    watch: {
        use_default() {
            this.validation();
        },
        default_value() {
            this.validation();
        },
    },

    created() {
        if (this.use_default) this.validation();
    },

    methods: {
        validation(state) {
            if (!this.use_default) {
                this.$emit("validation", state);
            } else {
                this.$emit("validation", this.defaultValid);
            }
        },

        empty: empty,
    }
}
</script>
