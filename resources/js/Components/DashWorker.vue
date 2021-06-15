<template>
    <div class="my-2 w-full">
        <div class="relative h-6 pr-8">
            <div class="relative bg-gray-100 h-6">
                <div class="absolute top-0 left-0 h-full bg-green-200 z-0" :style="{'width': progress + '%'}"></div>
                <div class="text-sm absolute top-0 left-0 w-full h-full py-1 px-2">
                    <p class="text-gray-800">{{ progress }}%</p>
                </div>
            </div>
            <span class="w-4 h-4 absolute top-1 right-1 cursor-pointer text-red-400 hover:text-red-700" title="Dismiss">
                <svg class="w-full h-full"
                     xmlns="http://www.w3.org/2000/svg"
                     viewBox="0 0 20 20" fill="currentColor"
                     @click="dismiss"
                >
                    <path fill-rule="evenodd"
                          d="M 0.5468683,16.299691 6.8464951,10.000001 0.5468683,3.7004089 3.700374,0.54683812 l 6.299627,6.29969938 6.299624,-6.29969938 3.153507,3.15357078 -6.299626,6.2995921 6.299626,6.29969 -3.153507,3.153471 -6.299624,-6.299599 -6.299627,6.299599 z"
                          clip-rule="evenodd"/>
                </svg>
            </span>
        </div>
        <div v-if="!is_killing">
            <p class="text-gray-600 text-sm mr-2 inline-block">#{{ id }}</p>
            <p class="text-gray-600 text-sm mr-2 inline-block">pid: {{ pid }}</p>
            <p class="text-gray-600 text-sm mr-2 inline-block">started: {{ started_at }}</p>
            <p class="text-gray-600 text-sm mr-2 inline-block">phase: {{ phase }}</p>
        </div>
        <div v-if="is_killing">
            <p class="text-gray-600 text-sm mr-2 inline-block">#{{ id }}</p>
            <p class="text-gray-600 text-sm mr-2 inline-block">pid: {{ pid }}</p>
            <p class="text-red-700 text-sm mr-2 inline-block">killing process</p>
        </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    name: "DashWorker",

    props: ['original'],

    computed: {
        id: function () {
            return typeof this.original.id !== 'undefined' ? this.original.id : '';
        },
        pid: function () {
            return typeof this.original.pid !== 'undefined' ? this.original.pid : '';
        },
        phase: function () {
            return typeof this.original.phase !== 'undefined' ? this.original.phase : '';
        },
        step: function () {
            return typeof this.original.step !== 'undefined' ? this.original.step : '';
        },
        progress: function () {
            return typeof this.original.percents !== 'undefined' ? this.original.percents : '';
        },
        started_at: function () {
            if (typeof this.original.created_at === 'undefined') return '';
            let time = this.original.created_at;
            time = time.replaceAll('-', '/').replaceAll('T', ' ').split('.')[0];
            return time;
        },
    },

    data: () => ({
        is_killing: false,
    }),

    methods: {
        dismiss() {
            if (confirm('Are you sure want to dismiss this worker? All temporary files and logs will be cleared.')) {
                this.is_killing = true;

                axios.post('/api/dismiss-worker', {id: this.id})
                    .then((response) => {
                        this.$emit('dismiss');
                    })
                    .catch((error) => {
                        console.log(error);
                    })
                    .finally(() => {
                        this.is_killing = false;
                    });
            }
        },
    },
}
</script>
