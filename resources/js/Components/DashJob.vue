<template>
    <div class="pt-2 pb-0.5">
        <div class="w-full box-border px-6 py-2 bg-white">
            <div class="relative w-full pl-10">
                <h2 class="mt-0.5">{{ title }}</h2>
                <div class="text-sm text-gray-900">done {{ done }}, pending {{ pending }}, limit {{ total }}</div>
                <span class="w-8 h-8 absolute left-0 top-1 block cursor-pointer text-green-400 hover:text-green-600"
                      title="Start new worker">
                <svg class="w-full h-full"
                     xmlns="http://www.w3.org/2000/svg"
                     viewBox="0 0 26 26" fill="currentColor"
                     @click="newWorker"
                >
                    <polygon fill-rule="evenodd" clip-rule="evenodd"
                             points="9.33 6.69 9.33 19.39 19.3 13.04 9.33 6.69"/>
                    <path fill-rule="evenodd" clip-rule="evenodd"
                          d="M26,13A13,13,0,1,1,13,0,13,13,0,0,1,26,13ZM13,2.18A10.89,10.89,0,1,0,23.84,13.06,10.89,10.89,0,0,0,13,2.18Z"/>
                </svg>
                </span>
            </div>
            <dash-worker v-for="(worker, key) in workers"
                         :key="key"
                         :original="worker"
                         @dismiss="$emit('changed')"
            />
        </div>
    </div>
</template>

<script>
import DashWorker from '@/Components/DashWorker';
import axios from "axios";

export default {
    name: "DashJob",

    components: {
        DashWorker,
    },

    props: ['original'],

    computed: {
        id: function () {
            return typeof this.original.id !== 'undefined' ? this.original.id : null;
        },
        workers: function () {
            return typeof this.original.workers === 'object' ? this.original.workers : [];
        },
        title: function () {
            return typeof this.original.title !== 'undefined' ? this.original.title : '';
        },
        done: function () {
            return typeof this.original.plots_done !== 'undefined' ? this.original.plots_done : 0;
        },
        pending: function () {
            return typeof this.original.workers === 'object' ? this.original.workers.length : 0;
        },
        total: function () {
            return typeof this.original.number_of_plots !== 'undefined' ? (this.original.number_of_plots === 0 ? 'infinite' : this.original.number_of_plots) : '';
        },

    },

    methods: {
        newWorker() {
            if (confirm('Ready to start new worker?')) {

                axios.post('/api/add-worker', {job_id: this.id})
                    .then((response) => {
                        this.$emit('changed');
                    })
                    .catch((error) => {
                        console.log(error);
                    });
            }
        },
    }
}
</script>

