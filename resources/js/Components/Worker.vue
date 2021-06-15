<template>
    <div class="p-2 text-sm hover:bg-blue-100" v-if="edit_mode">
        <span class="h-6 align-top inline-block" style="line-height: 1.5rem">Start</span>
        <breeze-inline-input class="w-20 inline-block" v-model="numberOfWorkersProxy" :placeholder="'number of'"/>
        <span class="h-6 align-top ml-2 inline-block" style="line-height: 1.5rem">worker(s) on</span>
        <breeze-inline-input v-model="eventNameProxy" class="w-40 ml-2 inline-block" :placeholder="'event name'" :list="'events-list'"/>
        <svg class="w-4 h-4 inline float-right m-1 cursor-pointer text-red-600" xmlns="http://www.w3.org/2000/svg"
             viewBox="0 0 20 20" fill="currentColor"
             @click="removeWorker"
        >
            <path fill-rule="evenodd"
                  d="M 0.5468683,16.299691 6.8464951,10.000001 0.5468683,3.7004089 3.700374,0.54683812 l 6.299627,6.29969938 6.299624,-6.29969938 3.153507,3.15357078 -6.299626,6.2995921 6.299626,6.29969 -3.153507,3.153471 -6.299624,-6.299599 -6.299627,6.299599 z"
                  clip-rule="evenodd"/>
        </svg>
    </div>
    <div class="text-sm" v-if="!edit_mode">
        <span>Start </span>
        <span class="font-bold ml-1">{{ numberOfWorkersProxy }}</span>
        <span class="ml-1">worker(s) on</span>
        <span class="font-bold ml-1">{{ eventNameProxy }}</span>
    </div>
</template>

<script>
import BreezeInlineInput from "@/Components/InlineInput";

export default {
    name: "Worker",

    components: {
        BreezeInlineInput,
    },

    emits: ['update', 'remove'],

    props: {
        index: Number,
        edit_mode: Boolean,
        origin: {
            type: Object,
            default: () => ({}),
        }
        // id: {type: Number, default: 0},
        // event_name: {type: String, default: null},
        // number_of_workers: {type: Number, default: null},
    },

    computed: {
        eventNameProxy: {
            get: function () {
                return typeof this.origin['event_name'] !== 'undefined' ? this.origin['event_name'] : '';
            },
            set: function (val) {
                this.$emit('update', this.index, 'event_name', val);
            }
        },

        numberOfWorkersProxy: {
            get: function () {
                return typeof this.origin['number_of_workers'] !== 'undefined' ? this.origin['number_of_workers'] : null;
            },
            set: function (val) {
                this.$emit('update', this.index, 'number_of_workers', val);
            }
        },
    },

    methods: {
        removeWorker() {
            this.$emit('remove', this.index);
        },
    },
}
</script>
