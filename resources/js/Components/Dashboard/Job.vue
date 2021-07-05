<!--
  -  This file is part of the MyChiaFarm project.
  -
  -    (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
  -
  -  For the full copyright and license information, please view the LICENSE
  -  file that was distributed with this source code.
  -->

<template>
    <div class="pt-2 pb-0.5">
        <div class="w-full box-border px-6 py-2 bg-white dark:bg-gray-700">
            <div class="relative w-full pl-10">
                <h2 class="mt-0.5 text-gray-900 dark:text-gray-300">{{ title }}</h2>
                <div class="text-sm text-gray-900 dark:text-gray-300">done {{ done }} of {{ total }}<span
                    v-if="pending !==0">, pending {{ pending }}</span></div>
                <span class="w-8 h-8 absolute left-0 top-1 block cursor-pointer text-green-400 hover:text-green-600 dark:text-green-600 dark:hover:text-green-400"
                      title="Start new worker">
                    <icon-play class="w-full h-full" @click="start"/>
                </span>
            </div>

            <worker v-for="(worker, key) in workers" :key="key" :original="worker" @dismissed="$emit('changed')"/>

        </div>
    </div>
</template>

<script>
import IconPlay from "@/Components/Icons/Play";
import Worker from '@/Components/Dashboard/Worker';
import axios from "axios";

export default {
    name: "DashJob",

    components: {
        IconPlay,
        Worker,
    },

    emits: ['changed'],

    props: ['original'],

    computed: {
        id: function () {
            return typeof this.original['id'] !== 'undefined' ? this.original['id'] : null;
        },
        title: function () {
            return typeof this.original['title'] !== 'undefined' ? this.original['title'] : '';
        },
        done: function () {
            return typeof this.original['plots_done'] !== 'undefined' ? this.original['plots_done'] : 0;
        },
        total: function () {
            return typeof this.original['plots_to_do'] !== 'undefined' ? (this.original['plots_to_do'] === 0 ? 'infinite' : this.original['plots_to_do']) : '';
        },
        pending: function () {
            return typeof this.original['workers'] === 'object' ? this.original['workers'].length : 0;
        },
        workers: function () {
            return typeof this.original['workers'] === 'object' ? this.original['workers'] : [];
        },
    },

    methods: {
        start() {
            if (confirm('Ready to start new worker?')) {

                axios.post('/api/worker/add', {job_id: this.id})
                    .then((response) => {
                        const message = response.data['message'];
                        this.$toast.success(message, 5000);
                        this.$emit('changed');
                    })
                    .catch((error) => {
                        const message = error.response.data['message'];
                        this.$toast.error(message);
                    });
            }
        },
    }
}
</script>

