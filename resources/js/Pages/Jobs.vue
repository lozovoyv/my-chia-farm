<!--
  -  This file is part of the MyChiaFarm project.
  -
  -    (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
  -
  -  For the full copyright and license information, please view the LICENSE
  -  file that was distributed with this source code.
  -->

<template>
    <authenticated-layout>
        <template #header>
            <m-c-f-button :caption="'Add job'" :theme="'green'" :disabled="creating" @click="create"></m-c-f-button>
        </template>

        <div class="text-2xl text-center w-full py-10" v-if="!hasJobs && !creating">You haven't jobs yet.
            Add one.
        </div>

        <m-c-f-job v-if="creating"
                   :creating="true"
                   :cpu_count="cpu_count"
                   :plotters="plotters"
                   :event_names="event_names"
                   :now="now"
                   @created="created"
                   @discarded="discard"
        ></m-c-f-job>

        <m-c-f-job v-for="(job, key) in jobs"
                   :job_original="job"
                   :deleted="job === null"
                   :cpu_count="cpu_count"
                   :plotters="plotters"
                   :event_names="event_names"
                   :now="now"
                   @updated="$job => updated($job, key)"
                   @removed="() => removed(key)"
        ></m-c-f-job>

    </authenticated-layout>
</template>

<script>
// mixins
import clone from "@/Helpers/clone";

// components
import AuthenticatedLayout from '@/Layouts/Authenticated'
import MCFButton from '@/Components/Buttons/Button';
import MCFJob from "@/Components/Job/Job";
import empty from "@/Helpers/Lib/empty";

export default {
    components: {
        AuthenticatedLayout,
        MCFButton,
        MCFJob,
    },

    mixins: [clone],

    props: {
        auth: Object,
        errors: Object,

        now: String,
        cpu_count: Number,
        plotters: Object,

        jobs_original: Array,
    },

    data: () => ({
        creating: false,
        jobs: [],
        event_names: [],
    }),

    computed: {
        hasJobs() {
            return this.jobs.length !== 0 && this.jobs.some(job => job !== null);
        },
    },

    created() {
        this.jobs = this.clone(this.jobs_original);
        this.collectEventNames();
    },

    methods: {
        create() {
            this.creating = true;
        },

        discard() {
            this.creating = false;
        },

        created(job) {
            this.jobs.push(job);
            this.discard();
            this.collectEventNames();
        },

        updated(job, key) {
            this.jobs[key] = job;
            this.collectEventNames();

        },

        removed(key) {
            this.jobs[key] = null;
            this.collectEventNames();
        },

        collectEventNames() {
            let names = [];
            this.jobs.map(job => {
                if (job === null || typeof job['events'] === 'undefined' || empty(job['events'])) return;
                job['events'].map(event => {
                    if (typeof event['name'] !== 'undefined' && !empty(event['name'])) names.push(event['name']);
                });
            });
            this.event_names = names;
        }
    }
}
</script>
