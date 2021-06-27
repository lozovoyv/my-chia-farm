<template>
    <authenticated-layout>
        <job v-for="(job, key) in jobs"
                  :key="key"
                  :original="job"
                  @changed="update"
        />
    </authenticated-layout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/Authenticated';
import Job from '@/Components/Dashboard/Job';

import axios from "axios";
import clone from "@/Helpers/Lib/clone";

export default {
    components: {
        AuthenticatedLayout,
        Job,
    },

    props: {
        auth: Object,
        errors: Object,
        jobs_original: Array,
    },

    data: () => ({
        jobs: [],
        timer: null,
        updating: false,
    }),

    created() {
        this.jobs = clone(this.jobs_original);

        this.timer = setInterval(this.update, 5000);
    },

    methods: {
        update() {
            if (this.updating) return;

            this.updating = true;

            axios.post('/api/job/all', {})
                .then((response) => {
                    this.jobs = clone(response.data['data']);
                })
                .catch((error) => {
                    const message = error.response.data['message'];
                    console.log(message);
                })
                .finally(() => {
                    this.updating = false;
                });
        },
    },
}
</script>
