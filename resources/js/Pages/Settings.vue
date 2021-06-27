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
        <m-c-f-card :title="'General plotting defaults'">
            <m-c-f-input v-for="(input, key) in plotting_general" :key="key"
                         :type="input['type']" :title="input['title']"
                         :required="input['required']" :options="input['options']"
                         v-model="values.plotting.globals[key]"
                         @validation="(state)=>{setValidation('plotting.globals.'+key, state)}"
            />
            <m-c-f-buttons :align="'right'">
                <m-c-f-button :theme="'white'" :disabled="!changed('plotting.globals')"
                              :caption="'revert'" @click="revert('plotting.globals')"/>
                <m-c-f-button :theme="'green'" :disabled="!updatable('plotting.globals')"
                              :caption="'apply'" @click="update('plotting.globals')"/>
            </m-c-f-buttons>
        </m-c-f-card>

        <m-c-f-card v-for="(plotter, key) in plotting_plotters" :title="plotter['name']" :key="key">
            <!-- executable -->
            <m-c-f-input :type="plotter['executable']['type']" :title="plotter['executable']['title']"
                         :required="plotter['executable']['required']" :options="plotter['executable']['options']"
                         :modelValue="values.plotting.plotters[key]['executable']"
                         @update:modelValue="values.plotting.plotters[key]['executable'] = $event"
                         @validation="(state)=>{setValidation('plotting.plotters.'+key+'.executable', state)}"
            />
            <div class="px-6 py-2 bg-white sm:rounded-lg border-b border-gray-200">
                <h3 class="font-semibold text-lg text-gray-800 leading-tight">Defaults</h3>
            </div>
            <!-- arguments -->
            <m-c-f-input v-for="(arg, arg_key) in plotter['arguments']" :key="arg_key"
                         :type="arg['type']" :title="arg_key + ': ' + arg['title']"
                         :required="arg['required']" :options="arg['options']"
                         :modelValue="values.plotting.plotters[key]['arguments'][arg_key]"
                         @update:modelValue="values.plotting.plotters[key]['arguments'][arg_key] = $event"
                         @validation="(state)=>{setValidation('plotting.plotters.'+key+'.arguments.'+arg_key, state)}"
            />
            <m-c-f-buttons :align="'right'">
                <m-c-f-button :theme="'white'" :disabled="!changed('plotting.plotters.'+key)"
                              :caption="'revert'" @click="revert('plotting.plotters.'+key)"/>
                <m-c-f-button :theme="'green'" :disabled="!updatable('plotting.plotters.'+key)"
                              :caption="'apply'" @click="update('plotting.plotters.'+key)"/>
            </m-c-f-buttons>
        </m-c-f-card>
    </authenticated-layout>
</template>

<script>
// libraries
import axios from 'axios';

// mixins
import clone from '@/Helpers/clone';
import changed from "@/Helpers/changed";
import revert from "@/Helpers/revert";
import validation from "@/Helpers/validation";

// components
import AuthenticatedLayout from '@/Layouts/Authenticated';
import MCFCard from '@/Components/Wrappers/Card';
import MCFInput from '@/Components/Inputs/Input';
import MCFButtons from '@/Components/Wrappers/Buttons';
import MCFButton from '@/Components/Buttons/Button';

export default {
    components: {
        AuthenticatedLayout,
        MCFCard,
        MCFInput,
        MCFButtons,
        MCFButton,
    },

    mixins: [clone, changed, revert, validation],

    props: {
        auth: Object,
        errors: Object,

        config: Object,
        plotting_general: Object,
        plotting_plotters: Object,
    },

    data() {
        return {
            values: {
                plotting: {
                    globals: {},
                    plotters: {},
                },
            },

            old: {
                plotting: {
                    globals: {},
                    plotters: {},
                },
            },

            validation: {
                plotting: {
                    globals: {},
                    plotters: {},
                },
            },

            processing: {},
        }
    },

    created() {
        this.values = this.clone(this.config);
        this.old = this.clone(this.config);
        // initialize values and old structures to handle input
        // for global
        if (typeof this.values.plotting === 'undefined') {
            this.values.plotting = {};
            this.old.plotting = {};
        }
        if (typeof this.values.plotting.globals === 'undefined') {
            this.values.plotting.globals = {};
            this.old.plotting.globals = {};
        }
        Object.keys(this.plotting_general).map(key => {
            if (typeof this.values.plotting.globals[key] === 'undefined') {
                this.values.plotting.globals[key] = null;
                this.old.plotting.globals[key] = null;
            }
        })
        // and for plotters
        if (typeof this.values.plotting.plotters === 'undefined') {
            this.values.plotting.plotters = {};
            this.old.plotting.plotters = {};
        }
        Object.keys(this.plotting_plotters).map(key => {
            if (typeof this.values.plotting.plotters[key] === 'undefined') {
                this.values.plotting.plotters[key] = {executable: null, arguments: {}};
                this.old.plotting.plotters[key] = {executable: null, arguments: {}};
                Object.keys(this.plotting_plotters[key]['arguments']).map(arg_key => {
                    this.values.plotting.plotters[key].arguments[arg_key] = null;
                    this.old.plotting.plotters[key].arguments[arg_key] = null;
                });
            }
        })
    },

    methods: {
        updatable(key_chain) {
            return this.changed(key_chain)
                && this.isValid(key_chain)
                && (typeof this.processing[key_chain] === 'undefined' || this.processing[key_chain] === false);
        },

        update(key_chain) {
            if (!this.updatable(key_chain)) {
                return;
            }

            this.processing[key_chain] = true;

            let data = this.cloneFrom('values.' + key_chain);

            axios.post('/api/config', {key: key_chain, value: data})
                .then((response) => {
                    this.cloneTo('values.' + key_chain, response.data.data);
                    this.cloneTo('old.' + key_chain, response.data.data);
                    let message = response.data.message;
                    console.log(message);
                })
                .catch((error) => {
                    let message = error.response.data.message;
                    console.log(message);
                })
                .finally(() => {
                    this.processing[key_chain] = false;
                })
        },
    }
}
</script>
