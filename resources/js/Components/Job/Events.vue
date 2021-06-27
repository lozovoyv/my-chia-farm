<!--
  -  This file is part of the MyChiaFarm project.
  -
  -    (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
  -
  -  For the full copyright and license information, please view the LICENSE
  -  file that was distributed with this source code.
  -->

<template>
    <div class="px-6 my-4" :class="{'my-1.5 text-sm':!edit_mode}">
        <p v-if="!hasEvents">No events defined</p>

        <datalist :id="'events-events-list-' + job_id" v-if="hasEvents && edit_mode">
            <option v-for="option in events_list">{{ option }}</option>
        </datalist>

        <event v-if="hasEvents" v-for="(event, key) in modelValue" :key="key"
               :value="event"
               :edit_mode="edit_mode"
               :plotter_events="plotter_events"
               :events_list="'events-events-list-' + job_id"
               @update="(value, valid) => updateEvent(key, value, valid)"
               @remove="() => removeEvent(key)"
        />

        <div class="my-4" v-if="edit_mode">
            <span class="text-sm cursor-pointer text-blue-600 underline" @click="addEvent">add event</span>
        </div>
    </div>
</template>

<script>
import Event from "@/Components/Job/Event";
import InlineInput from "@/Components/Inputs/InlineInput";
import IconClose from "@/Components/Icons/Close";
import validation from "@/Helpers/validation";
import clone from "@/Helpers/Lib/clone";

export default {
    components: {
        IconClose,
        InlineInput,
        Event,
    },

    mixins: [validation],

    emits: ['update:modelValue', 'validation'],

    props: {
        edit_mode: Boolean,
        modelValue: {type: Array, default: () => ([])},
        events_list: {type: Array, default: () => ([])},
        plotter_events: {type: Object, default: () => ([])},
        job_id: {type: Number, default: 0}
    },

    computed: {
        hasEvents() {
            return this.modelValue.length > 0;
        },
    },

    data: () => ({
        validation: [],
    }),

    methods: {
        addEvent() {
            let value = clone(this.modelValue);
            value.push({});
            this.$emit('update:modelValue', value);
            this.validate();
        },

        updateEvent(index, value, valid) {
            let val = clone(this.modelValue);
            val[index] = value;
            this.$emit('update:modelValue', val);
            this.validation[index] = valid;
            this.validate();
        },

        removeEvent(index) {
            let value = clone(this.modelValue);
            value.splice(index, 1);
            this.$emit('update:modelValue', value);
            this.validation.splice(index, 1);
            this.validate();
        },

        validate() {
            this.$emit('validation', this.isValid());
        },
    },
}
</script>
