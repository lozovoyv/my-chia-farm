<template>
    <div class="my-4" v-if="!deleted">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="shadow-sm sm:rounded-lg py-0.5" :class="[new_job ? 'bg-yellow-50' : 'bg-white']">
                <div class="p-6 border-b border-gray-200 relative">
                    <span class="text-gray-700 mr-3">ID:{{ new_job ? 'new' : id }}</span> <span
                    class="font-bold">{{ values.title }}</span>
                    <span v-if="values.disable" class="text-sm ml-3 text-gray-700 italic">(disabled)</span>
                    <span v-if="!edit_mode" class="float-right cursor-pointer mx-2 text-red-700" @click="deleteJob">delete</span>
                    <span v-if="!edit_mode" class="float-right cursor-pointer mx-2 text-blue-700"
                          @click="editJob">edit</span>
                </div>
                <div class="p-4 text-right" v-if="edit_mode">
                    <span v-if="edit_mode" class="cursor-pointer mx-2 text-blue-700"
                          @click="cancelEditing">cancel</span>
                    <span v-if="edit_mode" class="cursor-pointer mx-2 text-green-700" @click="saveJob">save</span>
                </div>
                <!-- General -->
                <breeze-checkbox-line v-if="edit_mode" :label="'Disable this job'" v-model:checked="values.disable"/>
                <breeze-input-line v-if="edit_mode" :label="'Job title'" :type="'text'" v-model="values.title"/>
                <breeze-checkbox-line v-if="edit_mode" :label="'Use global keys settings'"
                                      v-model:checked="values.use_global_keys"/>
                <breeze-input-line v-if="!values.use_global_keys && edit_mode" :label="'Farmer public key'"
                                   :type="'text'" v-model="values.farmer_public_key"/>
                <breeze-info-line v-if="values.use_global_keys || !edit_mode"
                                  :label="'Farmer public key' + (!!values.use_global_keys ? ' (from globals)' : '')"
                                  :value="farmerKey"/>
                <breeze-input-line v-if="!values.use_global_keys && edit_mode" :label="'Public key of pool'"
                                   :type="'text'" v-model="values.pool_public_key"/>
                <breeze-info-line v-if="values.use_global_keys || !edit_mode"
                                  :label="'Public key of pool' + (!!values.use_global_keys ? ' (from globals)' : '')"
                                  :value="poolKey"/>
                <div class="border-b border-gray-200"></div>

                <!-- Plot counts -->
                <breeze-input-line v-if="edit_mode" :label="'Disable job after number of plots done (0 for infinite)'"
                                   :type="'text'" v-model="values.number_of_plots"/>
                <breeze-info-line v-if="!edit_mode" :label="'Disable job after'"
                                  :value="(values.number_of_plots === 0 ? 'never' : values.number_of_plots)"><span
                    v-if="values.number_of_plots !== 0">plots</span></breeze-info-line>
                <breeze-info-line :label="'Plots done'" :value="values.plots_done"><span
                    class="cursor-pointer text-blue-700 ml-2" v-if="edit_mode" @click="resetPlotsDone">reset</span>
                </breeze-info-line>

                <div class="border-b border-gray-200"></div>
                <!-- Plot parameters -->
                <div class="overflow-auto" :class="{'cursor-pointer': !edit_mode}" @click="shortModeToggle">
                    <svg class="float-right h-10 w-10 m-1.5 transform" xmlns="http://www.w3.org/2000/svg"
                         viewBox="0 0 20 20" fill="currentColor" v-if="!edit_mode" :class="{'rotate-180': !short_mode}">
                        <path fill-rule="evenodd"
                              d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                              clip-rule="evenodd"/>
                    </svg>
                    <div v-if="edit_mode || !short_mode">
                        <breeze-checkbox-line v-if="edit_mode" :label="'Use global plot size settings'"
                                              v-model:checked="values.use_global_plot_size"/>
                        <breeze-select v-if="edit_mode && !values.use_global_plot_size" :label="'Plot size'"
                                       :values="[32,33,34,35]" v-model="values.plot_size"/>
                        <breeze-info-line v-if="values.use_global_plot_size || !edit_mode"
                                          :label="'Plot size' + (!!values.use_global_plot_size ? ' (from globals)' : '')"
                                          :value="plotSize"/>

                        <breeze-checkbox-line v-if="edit_mode" :label="'Use global buffer settings'"
                                              v-model:checked="values.use_global_buffer"/>
                        <breeze-input-line v-if="edit_mode && !values.use_global_buffer"
                                           :label="'Megabytes for sort/plot buffer'" :type="'text'"
                                           v-model="values.buffer"/>
                        <breeze-info-line v-if="values.use_global_buffer || !edit_mode"
                                          :label="'Buffer size' + (!!values.use_global_buffer ? ' (from globals)' : '')"
                                          :value="buffer"/>

                        <breeze-checkbox-line v-if="edit_mode" :label="'Use global threads settings'"
                                              v-model:checked="values.use_global_threads"/>
                        <breeze-input-line v-if="edit_mode && !values.use_global_threads"
                                           :label="'Number of threads to use'" :type="'text'" v-model="values.threads"/>
                        <breeze-info-line v-if="values.use_global_threads || !edit_mode"
                                          :label="'Number of threads' + (!!values.use_global_threads ? ' (from globals)' : '')"
                                          :value="threads"/>

                        <breeze-checkbox-line v-if="edit_mode" :label="'Use global buckets settings'"
                                              v-model:checked="values.use_global_buckets"/>
                        <breeze-select v-if="edit_mode && !values.use_global_buckets" :label="'Number of buckets'"
                                       :values="[16,32,64,128]" v-model="values.buckets"/>
                        <breeze-info-line v-if="values.use_global_buckets || !edit_mode"
                                          :label="'Number of buckets' + (!!values.use_global_buckets ? ' (from globals)' : '')"
                                          :value="buckets"/>

                        <breeze-checkbox-line v-if="edit_mode" :label="'Use global temporary directory settings'"
                                              v-model:checked="values.use_global_tmp_dir"/>
                        <breeze-input-line v-if="edit_mode && !values.use_global_tmp_dir"
                                           :label="'Temporary directory for plotting files'" :type="'text'"
                                           v-model="values.tmp_dir"/>
                        <breeze-info-line v-if="values.use_global_tmp_dir || !edit_mode"
                                          :label="'Temporary directory' + (!!values.use_global_tmp_dir ? ' (from globals)' : '')"
                                          :value="tmpDir"/>

                        <breeze-checkbox-line v-if="edit_mode"
                                              :label="'Use global second temporary directory settings (empty to disable)'"
                                              v-model:checked="values.use_global_tmp2_dir"/>
                        <breeze-input-line v-if="edit_mode && !values.use_global_tmp2_dir"
                                           :label="'Second temporary directory for plotting files'" :type="'text'"
                                           v-model="values.tmp2_dir"/>
                        <breeze-info-line v-if="values.use_global_tmp2_dir || !edit_mode"
                                          :label="'Second temporary directory' + (!!values.use_global_tmp2_dir ? ' (from globals)' : '')"
                                          :value="tmp2Dir"/>

                        <breeze-checkbox-line v-if="edit_mode" :label="'Use global final directory settings'"
                                              v-model:checked="values.use_global_final_dir"/>
                        <breeze-input-line v-if="edit_mode && !values.use_global_final_dir"
                                           :label="'Final directory for plots'" :type="'text'"
                                           v-model="values.final_dir"/>
                        <breeze-info-line v-if="values.use_global_final_dir || !edit_mode"
                                          :label="'Final directory' + (!!values.use_global_final_dir ? ' (from globals)' : '')"
                                          :value="finalDir"/>

                        <breeze-checkbox-line v-if="edit_mode" :label="'Use global disable bitfield settings'"
                                              v-model:checked="values.use_global_disable_bitfield"/>
                        <breeze-checkbox-line v-if="edit_mode && !values.use_global_disable_bitfield"
                                              :label="'Disable bitfield'" v-model:checked="values.disable_bitfield"/>
                        <breeze-info-line v-if="values.use_global_disable_bitfield || !edit_mode"
                                          :label="'Disable bitfield' + (!!values.use_global_disable_bitfield ? ' (from globals)' : '')"
                                          :value="disableBitfield ? 'yes' : 'no'"/>

                        <breeze-checkbox-line v-if="edit_mode" :label="'Use global skip adding to harvester settings'"
                                              v-model:checked="values.use_global_skip_add"/>
                        <breeze-checkbox-line v-if="edit_mode && !values.use_global_skip_add"
                                              :label="'Skip adding to harvester'" v-model:checked="values.skip_add"/>
                        <breeze-info-line v-if="values.use_global_skip_add || !edit_mode"
                                          :label="'Skip adding to harvester' + (!!values.use_global_skip_add ? ' (from globals)' : '')"
                                          :value="skipAdd ? 'yes' : 'no'"/>
                    </div>
                    <div v-if="!edit_mode && short_mode">
                        <div class="px-6 my-4">
                            <div class="block font-medium text-sm text-blue-700 font-bold">
                                <span class="inline-block mr-1.5">-k <span class="text-gray-900">{{
                                        plotSize
                                    }}</span></span>
                                <span class="inline-block mr-1.5">-b <span class="text-gray-900">{{
                                        buffer
                                    }}</span></span>
                                <span class="inline-block mr-1.5">-r <span class="text-gray-900">{{
                                        threads
                                    }}</span></span>
                                <span class="inline-block mr-1.5">-u <span class="text-gray-900">{{
                                        buckets
                                    }}</span></span>
                                <span class="inline-block mr-1.5">-t <span class="text-gray-900">{{
                                        tmpDir
                                    }}</span></span>
                                <span class="inline-block mr-1.5">-2 <span class="text-gray-900">{{
                                        tmp2Dir
                                    }}</span></span>
                                <span class="inline-block mr-1.5">-d <span class="text-gray-900">{{
                                        finalDir
                                    }}</span></span>
                                <span class="inline-block mr-1.5" v-if="disableBitfield">-e</span>
                                <span class="inline-block mr-1.5" v-if="skipAdd">-x</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="border-b border-gray-200"></div>
                <!-- CPU -->
                <breeze-checkbox-line v-if="edit_mode" :label="'Enable CPU affinity'"
                                      v-model:checked="values.cpu_affinity_enable"/>
                <breeze-info-line v-if="!edit_mode" :label="'CPU affinity'"
                                  :value="!!values.cpu_affinity_enable ? 'enabled' : 'disabled'"/>
                <breeze-checkbox-group-line v-if="edit_mode && values.cpu_affinity_enable"
                                            :label="'Pin this job to CPUs:'" :values="cpus" :inline="true"
                                            v-model="values.cpus"></breeze-checkbox-group-line>
                <breeze-info-line v-if="!edit_mode && values.cpu_affinity_enable" :label="'Pinned to CPUs'"
                                  :value="values.cpus.join(', ')"/>

                <div class="border-b border-gray-200"></div>
                <!-- Events -->
                <p class="px-6 my-4">Events</p>
                <breeze-checkbox-line v-if="edit_mode" :label="'Disable events (only manual start for workers)'"
                                      v-model:checked="values.events_disable"/>
                <breeze-info-line v-if="!edit_mode" :label="'Events'"
                                  :value="values.events_disable ? 'disabled' : 'enabled'"/>
                <!-- Events list here -->
                <div v-if="!values.events_disable" class="px-6 my-4 text-sm">
                    <p v-if="values.events.length === 0">No events defined</p>
                    <breeze-event v-for="(event, key) in values.events"
                                  :index="key"
                                  :origin="event"
                                  :edit_mode="edit_mode"
                                  @update="updateEvent"
                                  @remove="removeEvent"
                    ></breeze-event>
                    <p class="my-4 text-sm cursor-pointer text-blue-600 underline" v-if="edit_mode" @click="addEvent">
                        add event</p>
                </div>

                <div class="border-b border-gray-200"></div>
                <!-- Workers -->
                <p class="px-6 my-4">Workers</p>
                <breeze-input-line v-if="edit_mode" :label="'Maximum workers can do this job (0 for infinite)'"
                                   :type="'text'" v-model="values.max_workers"/>
                <breeze-info-line v-if="!edit_mode" :label="'Maximum workers'"
                                  :value="values.max_workers === 0 ? 'infinite' : values.max_workers"/>
                <breeze-checkbox-line v-if="edit_mode" :label="'Save worker\'s log and statistics'"
                                      v-model:checked="values.save_worker_monitor_log"/>
                <breeze-info-line v-if="!edit_mode" :label="'Save worker\'s log'"
                                  :value="values.save_worker_monitor_log ? 'yes' : 'no'"/>
                <breeze-input-line v-if="edit_mode && values.save_worker_monitor_log"
                                   :label="'Number of worker logs to save'" :type="'text'"
                                   v-model="values.number_of_worker_logs"/>
                <breeze-info-line v-if="!edit_mode && values.save_worker_monitor_log"
                                  :label="'Number of worker logs to save'" :value="values.number_of_worker_logs"/>
                <!-- Workers list here -->
                <div class="px-6 my-4 text-sm">
                    <p v-if="values.starts.length === 0">No workers starts defined</p>
                    <breeze-worker v-for="(worker, key) in values.starts"
                                   :index="key"
                                   :origin="worker"
                                   :edit_mode="edit_mode"
                                   @update="updateWorker"
                                   @remove="removeWorker"
                    ></breeze-worker>
                    <p class="my-4 text-sm cursor-pointer text-blue-600 underline" v-if="edit_mode" @click="addWorker">
                        add worker start</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import BreezeCheckbox from '@/Components/Checkbox';
import BreezeDropdown from '@/Components/Dropdown';
import BreezeInput from '@/Components/Input';
import BreezeLabel from '@/Components/Label';
import BreezeButton from '@/Components/Button';
import BreezeInputLine from '@/Components/InputLine';
import BreezeSelect from '@/Components/Select';
import BreezeCheckboxLine from '@/Components/CheckboxLine';
import BreezeInfoLine from '@/Components/InfoLine';
import BreezeCheckboxGroupLine from '@/Components/CheckboxGroupLine';
import BreezeEvent from '@/Components/Event';
import BreezeWorker from '@/Components/Worker';

export default {
    name: "Job",

    components: {
        BreezeCheckbox,
        BreezeDropdown,
        BreezeInput,
        BreezeLabel,
        BreezeButton,
        BreezeInputLine,
        BreezeSelect,
        BreezeCheckboxLine,
        BreezeInfoLine,
        BreezeCheckboxGroupLine,
        BreezeEvent,
        BreezeWorker,
    },

    props: {
        auth: Object,
        errors: Object,
        m_config: Object,
        cpu_count: Number,
        deleted: Boolean,
        id: {
            type: Number,
            default: 0
        },
        new_job: {
            type: Boolean,
            default: true
        },
        edit_mode_initial: {
            type: Boolean,
            default: false
        },
        original: {
            type: Object,
            default: null
        }
    },

    computed: {
        canBeUpdated: function () {
            return true;
        },
        cpus: function () {
            return [...Array(this.cpu_count).keys()].map(i => 'CPU' + i);
        },
        farmerKey: function () {
            return this.values.use_global_keys ? this.m_config.general.default_farmer_key : this.values.farmer_public_key;
        },
        poolKey: function () {
            return this.values.use_global_keys ? this.m_config.general.default_pool_key : this.values.pool_public_key;
        },
        plotSize: function () {
            return this.values.use_global_plot_size ? this.m_config.job.default_plot_size : this.values.plot_size;
        },
        buckets: function () {
            return this.values.use_global_buckets ? this.m_config.job.default_buckets : this.values.buckets;
        },
        buffer: function () {
            return this.values.use_global_buffer ? this.m_config.job.default_buffer : this.values.buffer;
        },
        threads: function () {
            return this.values.use_global_threads ? this.m_config.job.default_threads : this.values.threads;
        },
        tmpDir: function () {
            return this.values.use_global_tmp_dir ? this.m_config.job.default_tmp_dir : this.values.tmp_dir;
        },
        tmp2Dir: function () {
            const tmp2 = this.values.use_global_tmp2_dir ? this.m_config.job.default_tmp2_dir : this.values.tmp2_dir;
            return !!tmp2 ? tmp2 : 'disabled';
        },
        finalDir: function () {
            return this.values.use_global_final_dir ? this.m_config.job.default_final_dir : this.values.final_dir;
        },
        disableBitfield: function () {
            return this.values.use_global_disable_bitfield ? this.m_config.job.default_disable_bitfield : this.values.disable_bitfield;
        },
        skipAdd: function () {
            return this.values.use_global_skip_add ? this.m_config.job.default_skip_add : this.values.skip_add;
        },

    },

    created() {
        this.edit_mode = this.edit_mode_initial;
        if (!this.new_job) {
            this.migrateFromOriginal();
        }
    },

    data: () => ({
        edit_mode: false,
        short_mode: true,
        values: {
            title: 'New job',
            disable: false,

            use_global_keys: true,
            farmer_public_key: null,
            pool_public_key: null,

            number_of_plots: 0,
            plots_done: 0,

            use_global_plot_size: true,
            plot_size: 32,
            use_global_buckets: true,
            buckets: 128,
            use_global_buffer: true,
            buffer: 3389,
            use_global_threads: true,
            threads: 2,
            use_global_tmp_dir: true,
            tmp_dir: null,
            use_global_tmp2_dir: true,
            tmp2_dir: null,
            use_global_final_dir: true,
            final_dir: null,
            use_global_disable_bitfield: true,
            disable_bitfield: false,
            use_global_skip_add: true,
            skip_add: false,

            cpu_affinity_enable: false,
            cpus: [],

            events_disable: false,
            events: [],

            max_workers: 0,
            save_worker_monitor_log: false,
            number_of_worker_logs: 0,
            starts: [],
        },
    }),

    methods: {
        shortModeToggle() {
            if (!this.edit_mode) {
                this.short_mode = !this.short_mode;
            }
        },

        deleteJob() {
            if (confirm('Are you sure want to remove job ID:' + this.id + ' "' + this.values.title + '"? All running workers on this job would be dismissed.')) {
                this.$emit('remove-job', this.id);
            }
        },

        editJob() {
            this.edit_mode = true;
        },

        saveJob() {
            let job = {
                id: this.id,
                title: this.values.title,
                disable: this.values.disable,
                use_global_keys: this.values.use_global_keys,
                farmer_public_key: this.values.farmer_public_key,
                pool_public_key: this.values.pool_public_key,
                number_of_plots: this.values.number_of_plots,
                plots_done: this.values.plots_done,
                plot_size: this.values.plot_size,
                use_global_plot_size: this.values.use_global_plot_size,
                buckets: this.values.buckets,
                use_global_buckets: this.values.use_global_buckets,
                buffer: this.values.buffer,
                use_global_buffer: this.values.use_global_buffer,
                threads: this.values.threads,
                use_global_threads: this.values.use_global_threads,
                tmp_dir: this.values.tmp_dir,
                use_global_tmp_dir: this.values.use_global_tmp_dir,
                tmp2_dir: this.values.tmp2_dir,
                use_global_tmp2_dir: this.values.use_global_tmp2_dir,
                final_dir: this.values.final_dir,
                use_global_final_dir: this.values.use_global_final_dir,
                skip_add: this.values.skip_add,
                use_global_disable_bitfield: this.values.use_global_disable_bitfield,
                disable_bitfield: this.values.disable_bitfield,
                use_global_skip_add: this.values.use_global_skip_add,
                cpu_affinity_enable: this.values.cpu_affinity_enable,
                cpus: Array.from(this.values.cpus),

                events_disable: this.values.events_disable,
                events: Array.from(this.values.events),

                max_workers: this.values.max_workers,
                save_worker_monitor_log: this.values.save_worker_monitor_log,
                number_of_worker_logs: this.values.number_of_worker_logs,
                starts: Array.from(this.values.starts),
            };

            if (this.new_job) {
                this.$emit('create-job', this.id, job);
            } else {
                this.$emit('change-job', this.id, job);
            }

            this.edit_mode = false;
        },

        cancelEditing() {
            if (this.new_job) {
                this.$emit('discard-new-job', this.id);
            } else {
                this.migrateFromOriginal()
                this.edit_mode = false;
            }
        },

        resetPlotsDone() {
            this.values.plots_done = 0;
        },

        migrateFromOriginal() {
            this.values.title = this.original.title;
            this.values.disable = Boolean(this.original.disable);
            this.values.use_global_keys = this.original.use_global_keys;
            this.values.farmer_public_key = this.original.farmer_public_key;
            this.values.pool_public_key = this.original.pool_public_key;
            this.values.number_of_plots = Number(this.original.number_of_plots);
            this.values.plots_done = Number(this.original.plots_done);
            this.values.use_global_plot_size = Boolean(this.original.use_global_plot_size);
            this.values.plot_size = Number(this.original.plot_size);
            this.values.use_global_buckets = Boolean(this.original.use_global_buckets);
            this.values.buckets = Number(this.original.buckets);
            this.values.use_global_buffer = Boolean(this.original.use_global_buffer);
            this.values.buffer = Number(this.original.buffer);
            this.values.use_global_threads = Boolean(this.original.use_global_threads);
            this.values.threads = Number(this.original.threads);
            this.values.use_global_tmp_dir = Boolean(this.original.use_global_tmp_dir);
            this.values.tmp_dir = this.original.tmp_dir;
            this.values.use_global_tmp2_dir = Boolean(this.original.use_global_tmp2_dir);
            this.values.tmp2_dir = this.original.tmp2_dir;
            this.values.use_global_final_dir = Boolean(this.original.use_global_final_dir);
            this.values.final_dir = this.original.final_dir;
            this.values.use_global_disable_bitfield = Boolean(this.original.use_global_disable_bitfield);
            this.values.disable_bitfield = Boolean(this.original.disable_bitfield);
            this.values.use_global_skip_add = Boolean(this.original.use_global_skip_add);
            this.values.skip_add = Boolean(this.original.skip_add);
            this.values.cpu_affinity_enable = Boolean(this.original.cpu_affinity_enable);
            this.values.cpus = Array.from(this.original.cpus);
            this.values.events_disable = Boolean(this.original.events_disable);
            this.values.events = Array.from(this.original.events);
            this.values.max_workers = Number(this.original.max_workers);
            this.values.save_worker_monitor_log = Boolean(this.original.save_worker_monitor_log);
            this.values.number_of_worker_logs = Number(this.original.number_of_worker_logs);
            this.values.starts = Array.from(this.original.starts);
        },

        addEvent() {
            this.values.events.push({});
        },

        updateEvent(index, attribute, value) {
            this.values.events[index][attribute] = value;
        },

        removeEvent(index) {
            this.values.events.splice(index, 1);
        },

        addWorker() {
            this.values.starts.push({});
        },

        updateWorker(index, attribute, value) {
            this.values.starts[index][attribute] = value;
        },

        removeWorker(index) {
            this.values.starts.splice(index, 1);
        }
    },
}
</script>
