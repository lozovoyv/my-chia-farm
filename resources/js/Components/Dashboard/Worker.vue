<!--
  -  This file is part of the MyChiaFarm project.
  -
  -    (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
  -
  -  For the full copyright and license information, please view the LICENSE
  -  file that was distributed with this source code.
  -->

<template>
    <div class="my-2 w-full">
        <div class="relative h-6 pr-8">
            <div class="relative bg-gray-100 h-6">
                <div class="absolute top-0 left-0 h-full bg-green-200 z-0" :class="{'bg-red-200': has_error}"
                     :style="{'width': progress + '%'}"></div>
                <div class="text-sm absolute top-0 left-0 w-full h-full py-1 px-2">
                    <p class="text-gray-800">{{ progress }}%</p>
                </div>
            </div>
            <span class="w-4 h-4 absolute top-1 right-1 cursor-pointer text-red-400 hover:text-red-700" title="Dismiss">
                <icon-close class="w-full h-full" @click="dismiss"/>
            </span>
        </div>
        <div v-if="!is_killing">
            <p class="text-gray-600 text-sm mr-2 inline-block">#{{ id }}</p>
            <p class="text-gray-600 text-sm mr-2 inline-block">pid: {{ pid }}</p>
            <p class="text-gray-600 text-sm mr-2 inline-block">started: {{ created_at }}</p>
            <p class="text-gray-600 text-sm mr-2 inline-block" v-if="!has_error">{{ status }}</p>
            <p class="text-red-600 text-sm mr-2 inline-block" v-if="has_error">{{ error }}</p>
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
import IconClose from "@/Components/Icons/Close";

export default {
    name: "DashWorker",

    props: ['original'],

    emits: ['dismissed'],

    components: {
        IconClose,
    },

    computed: {
        id: function () {
            return typeof this.original['id'] !== 'undefined' ? this.original['id'] : '';
        },
        pid: function () {
            return typeof this.original['pid'] !== 'undefined' ? this.original['pid'] : '';
        },
        progress: function () {
            return typeof this.original['percents'] !== 'undefined' ? this.original['percents'] : 0;
        },
        has_error: function () {
            return typeof this.original['has_error'] !== 'undefined' ? this.original['has_error'] : false;
        },
        error: function () {
            return typeof this.original['error'] !== 'undefined' ? this.original['error'] : '';
        },
        status: function () {
            return typeof this.original['status'] !== 'undefined' ? this.original['status'] : '';
        },
        phase: function () {
            return typeof this.original['phase'] !== 'undefined' ? this.original['phase'] : '';
        },
        step: function () {
            return typeof this.original['step'] !== 'undefined' ? this.original['step'] : '';
        },
        created_at: function () {
            return typeof this.original['created_at'] !== 'undefined' ? this.original['created_at'] : null;
        },
    },

    data: () => ({
        is_killing: false,
    }),

    methods: {
        dismiss() {
            if (confirm('Are you sure want to dismiss this worker? All temporary files and logs will be cleared.')) {

                this.is_killing = true;

                axios.post('/api/worker/dismiss', {id: this.id})
                    .then((response) => {
                        const message = response.data['message'];
                        this.$toast.success(message, 5000);
                        this.$emit('dismissed');
                    })
                    .catch((error) => {
                        const message = error.response.data['message'];
                        this.$toast.error(message);
                    })
                    .finally(() => {
                        this.is_killing = false;
                    });
            }
        },
    },
}
</script>
