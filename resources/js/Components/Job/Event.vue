<!--
  -  This file is part of the MyChiaFarm project.
  -
  -    (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
  -
  -  For the full copyright and license information, please view the LICENSE
  -  file that was distributed with this source code.
  -->

<template>
    <div class="p-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-300" v-if="edit_mode">
        <span class="h-6 align-top ml-2 inline-block text-sm" style="line-height: 1.5rem">Fire</span>
        <!-- Event name -->
        <inline-input :required="true" :placeholder="'event name'" class="inline-block w-28" :valid="!!name"
                      v-model="name" :list="events_list"/>
        <span class="h-6 align-top ml-2 inline-block text-sm" style="line-height: 1.5rem">on</span>
        <!-- Event stage -->
        <inline-drop-down :values="stages_list" :placeholder="'stage'" :selected="stage"
                          @select="val => {this.stage = val}" :valid="!!stage"/>

        <!-- Plotting stage and condition -->
        <inline-drop-down v-if="stage === 'PST'" :values="plotter_events" :placeholder="'plotting stage'"
                          :selected="plotting_stage" @select="val => {this.plotting_stage = val}"
                          :valid="!!plotting_stage"/>
        <inline-drop-down v-if="stage === 'PST' && plotting_stage" :values="{start:'start',finish:'finish'}"
                          :placeholder="'condition'" :selected="plotting_stage_cond"
                          @select="val => {this.plotting_stage_cond = val}" :valid="!!plotting_stage_cond"/>

        <!-- Plotting progress -->
        <span v-if="stage === 'PPR'" class="h-6 align-top ml-2 inline-block text-sm"
              style="line-height: 1.5rem">is</span>
        <inline-input v-if="stage === 'PPR'" :required="true" :placeholder="'N'" class="inline-block w-12"
                      :valid="!!plotting_progress && !isNaN(plotting_progress)" v-model="plotting_progress"/>
        <span v-if="stage === 'PPR'" class="h-6 align-top ml-2 inline-block text-sm"
              style="line-height: 1.5rem">%</span>

        <!-- Delay -->
        <inline-checkbox :title="'with delay'" v-model="with_delay"/>
        <inline-input v-if="with_delay" :required="true" :placeholder="'N'" class="inline-block w-16"
                      :valid="!!delay && !isNaN(delay)" v-model="delay"/>
        <span v-if="with_delay" class="h-6 align-top ml-2 inline-block text-sm"
              style="line-height: 1.5rem">seconds</span>

        <icon-close :class="'inline float-right m-1'" @click="$emit('remove')"/>
    </div>
    <div class="my-1.5 text-sm text-gray-800 dark:text-gray-300" v-else>
        <span>Fire <b>{{ name }}</b> on <b>{{ stages_list[stage] }}</b> </span>
        <span v-if="stage === 'PST'" class="ml-1 lowercase">- <b>{{
                plotter_events[plotting_stage]
            }}</b> {{ plotting_stage_cond }}</span>
        <span v-if="stage === 'PPR'" class="ml-1"> <b> {{ plotting_progress }}%</b></span>
        <span v-if="with_delay"> with delay <b>{{ delay }}</b> seconds</span>
    </div>
</template>

<script>
import IconClose from '@/Components/Icons/Close';
import InlineDropDown from "@/Components/Inputs/InlineDropDown";
import InlineInput from "@/Components/Inputs/InlineInput";
import InlineCheckbox from "@/Components/Inputs/InlineCheckbox";
import clone from "@/Helpers/Lib/clone";
import empty from "@/Helpers/Lib/empty";

const event_stages = {
    PST: 'plotting stage',
    PPR: 'plotting progress',
    WBS: 'pre-process command started',
    WBE: 'pre-process command finished',
    WPS: 'plotter process started',
    WPE: 'plotter process finished',
    WAS: 'post-process command started',
    WAE: 'post-process command finished',
    WE: 'worker end',
    JE: 'job end',
}

export default {
    components: {
        InlineDropDown,
        InlineInput,
        IconClose,
        InlineCheckbox,
    },

    emits: ['update', 'remove'],

    props: {
        value: {type: Object, default: () => ({})},
        edit_mode: Boolean,
        plotter_events: {type: Object, default: () => ([])},
        events_list: {type: String, default: null},
    },

    computed: {
        name: {
            get() {
                return this.getValue('name');
            },
            set(val) {
                this.setValue('name', val);
            }
        },

        stage: {
            get() {
                return this.getValue('stage');
            },
            set(val) {
                this.setValue('stage', val);
            }
        },

        plotting_stage: {
            get() {
                return this.getValue('p_stage');
            },
            set(val) {
                this.setValue('p_stage', val);
            }
        },

        plotting_stage_cond: {
            get() {
                return this.getValue('p_stage_cond');
            },
            set(val) {
                this.setValue('p_stage_cond', val);
            }
        },

        plotting_progress: {
            get() {
                return this.getValue('p_progress');
            },
            set(val) {
                this.setValue('p_progress', val);
            }
        },

        with_delay: {
            get() {
                return this.getValue('with_delay');
            },
            set(val) {
                this.setValue('with_delay', val);
            }
        },

        delay: {
            get() {
                return this.getValue('delay');
            },
            set(val) {
                this.setValue('delay', val);
            }
        },

        stages_list() {
            return event_stages;
        },
    },

    created() {
        let value = clone(this.value);
        this.$emit('update', value, this.valid(value));
    },

    methods: {
        getValue(key) {
            return typeof this.value[key] === 'undefined' ? null : this.value[key];
        },

        valid(value) {
            const nameValid = typeof value['name'] !== 'undefined' && !empty(value['name']);
            const stageValid = typeof value['stage'] !== 'undefined' && !empty(value['stage']);

            const notPST = typeof value['stage'] === 'undefined' || value['stage'] !== 'PST';
            const PSTValid = notPST || typeof value['p_stage'] !== 'undefined' && typeof value['p_stage_cond'] !== 'undefined' && !empty(value['p_stage']) && !empty(value['p_stage_cond']);

            const notPPR = typeof value['stage'] === 'undefined' || value['stage'] !== 'PPR';
            const PPRValid = notPPR || typeof value['p_progress'] !== 'undefined' && !empty(value['p_progress']) && !isNaN(value['p_progress']);

            const delayDisabled = typeof value['with_delay'] === 'undefined' || value['with_delay'] === false || value['with_delay'] === null;
            const delayValid = delayDisabled || typeof value['delay'] !== 'undefined' && !empty(value['delay']) && !isNaN(value['delay']);

            return nameValid && stageValid && delayValid && PSTValid && PPRValid;
        },

        setValue(key, val) {
            let value = clone(this.value);
            value[key] = val;
            this.$emit('update', value, this.valid(value));
        },
    },
}
</script>
