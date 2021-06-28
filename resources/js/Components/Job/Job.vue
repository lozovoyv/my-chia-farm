<!--
  -  This file is part of the MyChiaFarm project.
  -
  -    (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
  -
  -  For the full copyright and license information, please view the LICENSE
  -  file that was distributed with this source code.
  -->

<template>
    <m-c-f-big-card v-if="!deleted">
        <template #header>
            <h3 v-if="title" class="font-semibold text-xl text-gray-800 leading-tight inline">{{ title }}</h3>
            <span v-if="!creating && !editing" class="ml-2 text-blue-700 text-lg underline cursor-pointer"
                  @click="editing = true">edit</span>
            <icon-close v-if="!creating" :class="'inline float-right m-1'" @click="remove"/>
        </template>

        <div v-if="editing">
            <!--title-->
            <m-c-f-input :type="'string'" :title="'Job title'" :required="true" v-model="title"
                         @validation="state => setValidation('title', state)"/>
            <!--plots_to_do-->
            <m-c-f-input :type="'int'" :title="'Disable job after number of plots done (0 for infinite)'"
                         v-model="plots_to_do"/>
            <!--plots_done-->
            <m-c-f-input :type="'counter'" :title="'Plots done'" v-model="plots_done"/>

            <!-- PLOTTER ARGUMENTS -->
            <m-c-f-section :title="'Plotter arguments'">
                <!--plotter_alias-->
                <m-c-f-input :title="'Plotter'" :type="'dropdown'" :options="plotter_names" :required="true"
                             v-model="plotter_alias" @validation="state => setValidation('plotter_alias', state)"
                             :error-message="'Select plotter to configure it'"/>
                <div v-if="plotter_alias">
                    <!--executable-->
                    <m-c-f-input-default
                        :type="'string'" :title="'Executable'" :required="true"
                        v-model="executable"
                        @validation="state => setValidation('executable', state)"
                        v-model:use_default="use_default_executable"
                        :default_value="plotter['executable']"
                        :default_caption="'Use default executable'"
                    />

                    <!--attributes-->
                    <!--use_globals_for-->
                    <m-c-f-input-default
                        v-for="(argument, key) in plotter['arguments']" :key="key"
                        :type="argument['type']" :title="argument['title']" :required="argument['required']"
                        :options="argument['options']"
                        :modelValue="getArgument(key)"
                        @update:modelValue="values.arguments[key] = $event"
                        @validation="state => setValidation('arguments.'+key, state)"
                        :use_default="getGlobalsFor(key)"
                        @update:use_default="values.use_globals_for[key] = $event"
                        :default_value="plotter['arguments'][key]['default']"
                        :default_caption="'Use default '+argument['title'].toLocaleLowerCase()"
                    />
                </div>
            </m-c-f-section>

            <!-- ADVANCED SECTION -->
            <m-c-f-section :title="'Advanced'" v-if="plotter_alias">
                <!--cpu_affinity_enable-->
                <m-c-f-input :type="'bool'" :title="'Enable CPU affinity'" v-model="cpu_affinity_enable"/>
                <!--cpus-->
                <m-c-f-input v-if="cpu_affinity_enable" :type="'booleans'" :title="'Pin CPU cores to plotting process'"
                             v-model="cpus" :options="cpus_array"/>
                <!--pre_command_enabled-->
                <m-c-f-input :type="'bool'" :title="'Enable custom pre-process command'" v-model="pre_command_enabled"/>
                <!--pre_command-->
                <m-c-f-input v-if="pre_command_enabled" :type="'string'" :title="'Pre-process command'"
                             v-model="pre_command"/>
                <!--post_command_enabled-->
                <m-c-f-input :type="'bool'" :title="'Enable custom post-process command'"
                             v-model="post_command_enabled"/>
                <!--post_command-->
                <m-c-f-input v-if="post_command_enabled" :type="'string'"
                             :title="'Post-process command (%plot% tag will be replaced with absolute plot filename)'"
                             v-model="post_command"/>
            </m-c-f-section>

            <!-- REPLOTTING SECTION -->
            <m-c-f-section :title="'Replotting'" v-if="plotter_alias">
                <!--remove_oldest-->
                <m-c-f-input :type="'bool'" :title="'Enable replotting'" v-model="remove_oldest"/>
                <!--removing_stop_ts-->
                <m-c-f-input v-if="remove_oldest" :type="'string'" v-model="removing_stop_ts"
                             :title="'Remove one plot from destination directory on each worker start if plot timestamp is less than entered date'"/>
            </m-c-f-section>

            <!-- EVENTS SECTION -->
            <m-c-f-section :title="'Events'" v-if="plotter_alias">
                <!--disable_events_emitting-->
                <m-c-f-input :type="'bool'" :title="'Disable events emitting'" v-model="disable_events_emitting"/>
                <!--events-->
                <job-events v-if="!disable_events_emitting" v-model="events" :edit_mode="editing" :job_id="id"
                            :events_list="events_list" :plotter_events="plotter_events"
                            @validation="state => setValidation('events', state)"
                ></job-events>
            </m-c-f-section>

            <!-- STARTS SECTION -->
            <m-c-f-section :title="'Workers'" v-if="plotter_alias">
                <!--disable_workers_start-->
                <m-c-f-input :type="'bool'" :title="'Disable workers starting (only manual starting would be allowed)'"
                             v-model="disable_workers_start"/>
                <!--max_workers-->
                <m-c-f-input v-if="!disable_workers_start" :type="'int'"
                             :title="'How many workers can do this job at the same time (0 for infinite)'"
                             v-model="max_workers"/>
                <!--starts-->
                <job-starts v-if="!disable_workers_start" v-model="starts" :edit_mode="editing"
                            :events_list="events_list"
                            :job_id="id"
                            @validation="state => setValidation('starts', state)"></job-starts>

                <!--save_worker_log-->
                <m-c-f-input :type="'bool'" :title="'Save logs of workers'" v-model="save_worker_log"/>
                <!--number_of_worker_logs-->
                <m-c-f-input v-if="save_worker_log" :type="'int'"
                             :title="'How many logs of workers can be saved (0 for infinite)'"
                             v-model="number_of_worker_logs"/>
            </m-c-f-section>

            <m-c-f-buttons :align="'right'">
                <m-c-f-button :caption="'Save'" :theme="'green'" @click="save" :disabled="!updatable"></m-c-f-button>
                <m-c-f-button :caption="'Cancel'" :theme="'white'" @click="discard"></m-c-f-button>
            </m-c-f-buttons>
        </div>
        <div v-if="!editing" class="my-4">
            <m-c-f-line>Plots done: <b>{{ plots_done }}</b> of <b>{{ plots_to_do === 0 ? 'infinite' : plots_to_do }}</b>
            </m-c-f-line>
            <m-c-f-line>Plotter: <b>{{ d_plotter_name }}</b></m-c-f-line>
            <m-c-f-line class="text-gray-700">{{ d_plotter_executable }} <span v-html="d_plotter_arguments"></span>
            </m-c-f-line>
            <m-c-f-line v-if="cpu_affinity_enable">Pinned cores: <b>{{ cpus.join(', ') }}</b></m-c-f-line>
            <m-c-f-line v-else>CPU affinity disabled</m-c-f-line>
            <m-c-f-line v-if="pre_command_enabled">Pre-process command: <b>{{ pre_command }}</b></m-c-f-line>
            <m-c-f-line v-if="post_command_enabled">Post-process command: <b>{{ post_command }}</b></m-c-f-line>
            <m-c-f-line v-if="remove_oldest">Replotting up to <b>{{ removing_stop_ts }}</b></m-c-f-line>
            <m-c-f-line v-if="disable_events_emitting">Events disabled</m-c-f-line>
            <job-events v-if="!disable_events_emitting" v-model="events" :plotter_events="plotter_events"
                        :edit_mode="false"></job-events>
            <m-c-f-line v-if="disable_workers_start">Workers disabled (manual start only)</m-c-f-line>
            <m-c-f-line v-if="!disable_workers_start">Maximum workers at the same time:
                <b>{{ max_workers === 0 ? 'infinite' : max_workers }}</b>
            </m-c-f-line>
            <job-starts v-if="!disable_workers_start" v-model="starts" :edit_mode="false"></job-starts>
            <m-c-f-line v-if="!save_worker_log">Worker logs will not be saved.</m-c-f-line>
            <m-c-f-line v-if="save_worker_log">Save logs for
                <b>{{ number_of_worker_logs === 0 ? 'all' : number_of_worker_logs }}</b> worker(s)
            </m-c-f-line>
        </div>
    </m-c-f-big-card>
</template>

<script>
// mixins
import clone from "@/Helpers/clone";
import changed from "@/Helpers/changed";
import revert from "@/Helpers/revert";
import validation from "@/Helpers/validation";
import JobAttributes from "@/Components/Job/JobAttributes";

// components
import MCFBigCard from "@/Components/Wrappers/BigCard";
import MCFSection from "@/Components/Wrappers/Section";
import MCFButtons from '@/Components/Wrappers/Buttons';
import MCFButton from '@/Components/Buttons/Button';
import MCFInput from '@/Components/Inputs/Input';
import MCFInputDefault from '@/Components/Inputs/InputDefault';
import JobStarts from '@/Components/Job/Starts';
import JobEvents from '@/Components/Job/Events';
import IconClose from '@/Components/Icons/Close';
import MCFLine from '@/Components/Wrappers/Line';
import empty from "@/Helpers/Lib/empty";

export default {
    name: "Job",

    mixins: [JobAttributes, clone, validation, changed, revert],

    components: {
        MCFBigCard,
        MCFSection,
        MCFButtons,
        MCFButton,
        MCFInput,
        MCFInputDefault,
        JobStarts,
        JobEvents,
        IconClose,
        MCFLine,
    },

    emits: ['created', 'updated', 'discarded', 'removed'],

    props: {
        key: Number,
        creating: {type: Boolean, default: false},
        deleted: {type: Boolean, default: false},
        job_original: {type: Object, default: () => ({})},
        now: String,
        cpu_count: Number,
        plotters: Object,
        event_names: Array,
    },

    computed: {
        // for attributes see JobAttributes mixin
        // Helpers
        id() {
            return typeof this.job_original['id'] !== 'undefined' ? Number(this.job_original['id']) : null
        },
        plotter_names() {
            let names = {};
            Object.keys(this.plotters).map(key => {
                names[key] = this.plotters[key]['name'];
            });
            return names;
        },
        d_plotter_name() {
            return typeof this.plotter_names[this.plotter_alias] !== 'undefined' ? this.plotter_names[this.plotter_alias] : 'NOT SET'
        },
        d_plotter_executable() {
            if (this.use_default_executable) {
                return this.plotter !== null ? this.plotter['executable'] : 'NOT SET';
            }
            return this.executable
        },
        d_plotter_arguments() {
            if (this.plotter === null) return 'Plotter not specified';
            let args = '';
            Object.keys(this.plotter['arguments']).map(key => {
                let arg;
                if (this.values.use_globals_for[key]) {
                    arg = this.plotter['arguments'][key]['default'];
                } else {
                    arg = this.values['arguments'][key];
                }
                if (arg !== null) {
                    if (typeof arg === 'boolean') {
                        args += ' <span class="font-bold text-blue-800">' + key + '</span>';
                    } else {
                        args += ' <span class="font-bold text-blue-800">' + key + '</span> <span>' + arg + '</span>';
                    }
                }
            });
            return args;
        },
        plotter() {
            if (this.plotter_alias === null || typeof this.plotters[this.plotter_alias] === 'undefined') return null;
            return this.plotters[this.plotter_alias];
        },
        plotter_events() {
            if (this.plotter_alias === null || typeof this.plotters[this.plotter_alias] === 'undefined' || typeof this.plotters[this.plotter_alias]['events'] === 'undefined') return [];
            return this.plotters[this.plotter_alias]['events'];
        },
        cpus_array() {
            return [...Array(this.cpu_count).keys()].map(i => 'CPU' + i);
        },
        // State helpers
        editing: {
            get() {
                return this.creating || this.editing_mode;
            },
            set(val) {
                this.editing_mode = val;
            }
        },
        updatable() {
            return this.changed()
                && this.isValid()
                && this.processing === false;
        },
        events_list() {
            // merge external events list with internal
            let events = this.clone(this.event_names);
            if (this.events.length > 0) {
                this.events.map(event => {
                    if (typeof event['name'] !== 'undefined' && !empty(event['name'])) {
                        events.push(event['name'])
                    }
                });
            }
            return events;
        },
    },

    created() {
        if (!this.creating) {
            this.values = this.clone(this.job_original);
            this.old = this.clone(this.job_original);
            this.plotter_default_executable = (typeof this.job_original['executable'] === 'undefined' || this.job_original['executable'] === null || this.job_original['executable'] === '');
        }
    },

    data: () => ({
        values: {},
        old: {},
        validation: {},

        editing_mode: false,
        processing: false,
    }),

    methods: {
        save() {
            if (!this.updatable) return;

            this.processing = true;

            const url = this.creating ? `/api/job/create` : '/api/job/update';

            let job = this.clone(this.values);

            if (this.id !== null) job['id'] = this.id;

            axios.post(url, job)
                .then((response) => {

                    const received = response.data.data;
                    const message = response.data.message;
                    this.$toast.success(message, 5000);

                    if (this.creating) {
                        // send created event
                        this.$emit('created', this.clone(received));
                        this.editing = false;
                    } else {
                        // update values from request
                        this.cloneTo('values', received);
                        // clone values to old
                        this.cloneTo('old', received);
                        // send updated event
                        this.$emit('updated', this.clone(received));
                        this.editing = false;
                    }
                })
                .catch((error) => {
                    const message = error.response.data['message'];
                    this.$toast.error(message);
                })
                .finally(() => {
                    this.processing = false;
                });

        },

        discard() {
            if (this.creating) {
                this.$emit('discarded');
                return;
            }
            this.revert();
            this.editing = false;
        },

        remove() {
            if (this.id === null || this.processing) return;

            if (!confirm('Are you sure want to remove job ID:' + this.id + ' "' + this.title + '"? All running workers on this job would be dismissed.')) {
                return;
            }

            this.processing = true;

            axios.post('/api/job/remove', {id: this.id})
                .then((response) => {
                    const message = response.data.message;
                    this.$toast.success(message, 5000);
                    this.$emit('removed');
                })
                .catch((error) => {
                    const message = error.response.data['message'];
                    this.$toast.error(message);
                })
                .finally(() => {
                    this.processing = false;
                });
        },

        getArgument(key) {
            if (typeof this.values['arguments'] === 'undefined') this.values['arguments'] = {};
            if (typeof this.values['arguments'][key] === 'undefined') this.values['arguments'][key] = null;
            return this.values['arguments'][key];
        },

        getGlobalsFor(key) {
            if (typeof this.values['use_globals_for'] === 'undefined') this.values['use_globals_for'] = {};
            if (typeof this.values['use_globals_for'][key] === 'undefined') this.values['use_globals_for'][key] = true;
            return this.values['use_globals_for'][key];
        },
    },
}
</script>
