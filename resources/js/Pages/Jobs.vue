<template>
    <breeze-authenticated-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Monitor
            </h2>
        </template>

        <div class="my-4">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <breeze-button class="ml-4 py-3 bg-green-500 text-white" @click="addJob"
                               :class="{ 'opacity-25 cursor-not-allowed': new_job}"
                               :disabled="new_job"
                >Add job
                </breeze-button>
            </div>
        </div>

        <div class="text-2xl text-center w-full py-10" v-if="jobs.length === 0 && !new_job">You haven't jobs yet.
            Add one.
        </div>

        <datalist id="events-list">
            <option v-for="option in event_names">{{ option }}</option>
        </datalist>

        <job v-if="new_job"
             :m_config="m_config"
             :cpu_count="cpu_count"
             :id="0"
             :new_job="true"
             :edit_mode_initial="true"
             @discard-new-job="discardNew"
             @create-job="createNew"
        ></job>

        <job v-for="(job, key) in jobs"
             :key="key"
             :original="job"
             :m_config="m_config"
             :cpu_count="cpu_count"
             :id="job === null ? -1 : job.id"
             :new_job="false"
             :deleted="job === null"
             @change-job="changeJob"
             @remove-job="removeJob"
        ></job>

    </breeze-authenticated-layout>
</template>

<script>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated'
import Job from '@/Components/Job'
import Event from '@/Components/Event'
import BreezeButton from '@/Components/Button';
import axios from "axios";
import * as Vue from "vue";

export default {
    components: {
        BreezeAuthenticatedLayout,
        Job,
        Event,
        BreezeButton,
    },

    props: {
        auth: Object,
        errors: Object,
        m_config: Object,
        cpu_count: Number,
        jobs_original: Array,
        event_names_original: Array,
    },

    data: () => ({
        jobs: Vue.reactive([]),
        new_job: false,
        event_names: []
    }),

    created() {
        this.jobs = Array.from(this.jobs_original);
        this.event_names = Array.from(this.event_names_original);
    },

    methods: {
        addJob() {
            this.new_job = true;
        },

        discardNew(id) {
            this.new_job = false;
        },

        createNew(id, job) {
            axios.post('/api/new-job', job)
                .then((response) => {
                    this.jobs.push(response.data);
                    this.new_job = false;
                    this.updateEventsNames();
                })
                .catch((error) => {
                    console.log(error);
                });
        },

        changeJob(id, job) {
            axios.post('/api/update-job', job)
                .then((response) => {
                    // update job original
                    this.jobs.some((j, i) => {
                        if (j !== null && j.id === id) {
                            this.jobs[i] = job;
                            return true;
                        }
                        return false;
                    });
                    this.updateEventsNames();
                    // todo message
                })
                .catch((error) => {
                    console.log(error);
                });
        },

        removeJob(id) {
            axios.post('/api/remove-job', {id: id})
                .then((response) => {
                    // remove job
                    this.jobs.some((j, i) => {
                        if (j !== null && j.id === id) {
                            this.jobs[i] = null;
                            return true;
                        }
                        return false;
                    });
                    this.updateEventsNames();
                    // todo message
                })
                .catch((error) => {
                    console.log(error);
                });
        },

        updateEventsNames() {
            console.log('need to update event names');
        },
    }
}
</script>
