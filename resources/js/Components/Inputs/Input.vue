<!--
  -  This file is part of the MyChiaFarm project.
  -
  -    (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
  -
  -  For the full copyright and license information, please view the LICENSE
  -  file that was distributed with this source code.
  -->

<template>
    <component :is="component"
               v-model="proxyModel"
               :title="title"
               :inputType="inputType"
               :required="required"
               :options="options"
               :valid="valid"
               :errorMessage="errorMessage"
               :autocomplete="autocomplete"
    >
        <slot/>
    </component>
</template>

<script>
import empty from "@/Helpers/Lib/empty";
import InputString from "@/Components/Inputs/InputString";
import InputNumber from "@/Components/Inputs/InputNumber";
import InputCheckbox from "@/Components/Inputs/InputCheckbox";
import InputCheckboxGroup from "@/Components/Inputs/InputCheckboxGroup";
import InputSelect from "@/Components/Inputs/InputSelect";
import InputDropDown from "@/Components/Inputs/InputDropDown";
import InputCounter from "@/Components/Inputs/InputCounter";

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
        autocomplete: {type: String, default: null},
    },

    emits: ['update:modelValue', 'validation'],

    components: {
        InputString,
        InputNumber,
        InputCheckbox,
        InputSelect,
        InputCounter,
        InputDropDown,
        InputCheckboxGroup,
    },

    computed: {
        component: function () {
            switch (this.type) {
                case 'int':
                    return InputNumber;
                case 'bool':
                    return InputCheckbox;
                case 'booleans':
                    return InputCheckboxGroup;
                case 'select':
                    return InputSelect;
                case 'dropdown':
                    return InputDropDown;
                case 'counter':
                    return InputCounter;
                case 'string':
                default:
            }

            return InputString;
        },

        proxyModel: {
            get() {
                return this.modelValue;
            },

            set(val) {
                this.$emit("update:modelValue", val);
                this.validate(val);
            },
        },

        valid: function () {
            return !this.required || !this.empty(this.modelValue);
        }
    },

    watch: {
        modelValue(value) {
            this.validate(value);
        }
    },

    created() {
        this.validate(this.modelValue);
    },

    methods: {
        validate(val) {
            this.$emit("validation", !this.required || !this.empty(val));
        },

        empty: empty,
    }
}
</script>
