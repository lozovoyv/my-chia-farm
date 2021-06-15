<template>
    <breeze-authenticated-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>

        <dash-job v-for="(job, key) in current_jobs"
                  :key="key"
                  :original="job"
                  @changed="updateStatus"
        />
    </breeze-authenticated-layout>
</template>

<script>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated';
import DashJob from '@/Components/DashJob';

import axios from "axios";

export default {
    components: {
        BreezeAuthenticatedLayout,
        DashJob,
    },

    props: {
        auth: Object,
        errors: Object,
        jobs: Array,
    },

    data: () => ({
        current_jobs: [],
        timer: null,
        updating: false,
    }),

    created() {
        this.current_jobs = this.jobs;

        this.timer = setInterval(this.updateStatus, 2000);
    },

    methods: {
        updateStatus() {
            if (this.updating) return;

            this.updating = true;

            axios.post('/api/get-jobs', {})
                .then((response) => {
                    this.current_jobs = Array.from(response.data);
                })
                .catch((error) => {
                    console.log(error);
                })
                .finally(() => {
                    this.updating = false;
                });
        },
    },
}
</script>
