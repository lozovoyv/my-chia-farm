<template>
    <breeze-authenticated-layout>
        <breeze-card :title="'General'">
            <breeze-input-line :label="'Chia installation path'" :type="'text'" v-model="general.chia_path"/>
            <breeze-input-line :label="'Default farmer key'" :type="'text'" v-model="general.default_farmer_key"/>
            <breeze-input-line :label="'Default pool key'" :type="'text'" v-model="general.default_pool_key"/>

            <div class="px-6 mt-6 mb-2 text-right">
                <breeze-button class="ml-4 py-3 bg-white text-gray-700 hover:text-white"
                               :class="{ 'opacity-25 cursor-not-allowed': processing.keys || !general.chia_path}"
                               :disabled="processing.keys || !general.chia_path"
                               @click="getKeysFromChia">
                    Get keys from installation
                </breeze-button>
                <breeze-button class="ml-4 py-3 bg-white text-gray-700 hover:text-white"
                               :class="{ 'opacity-25 cursor-not-allowed': !canBeUpdated('general')}"
                               :disabled="!canBeUpdated('general')"
                               @click="syncFromOld('general')">
                    Revert
                </breeze-button>
                <breeze-button class="ml-4 py-3 bg-green-500 text-white"
                               :class="{ 'opacity-25 cursor-not-allowed': !canBeUpdated('general')}"
                               :disabled="!canBeUpdated('general')" @click="update('general')">
                    Apply
                </breeze-button>
            </div>
        </breeze-card>

        <breeze-card :title="'Job defaults'">
            <breeze-select :label="'Plot size'" :values="[32,33,34,35]" v-model="job.default_plot_size"/>
            <breeze-select :label="'Number of buckets'" :values="[16,32,64,128]" v-model="job.default_buckets"/>
            <breeze-input-line :label="'Megabytes for sort/plot buffer'" :type="'text'" v-model="job.default_buffer"/>
            <breeze-input-line :label="'Number of threads to use'" :type="'text'" v-model="job.default_threads"/>
            <breeze-input-line :label="'Temporary directory for plotting files'" :type="'text'"
                               v-model="job.default_tmp_dir"/>
            <breeze-input-line :label="'Second temporary directory for plotting files'" :type="'text'"
                               v-model="job.default_tmp2_dir"/>
            <breeze-input-line :label="'Final directory for plots'" :type="'text'" v-model="job.default_final_dir"/>
            <breeze-checkbox-line :label="'Disable bitfield'" v-model:checked="job.default_disable_bitfield"/>
            <breeze-checkbox-line :label="'Skip adding to harvester for farming'" v-model:checked="job.default_skip_add"/>
            <div class="px-6 mt-6 mb-2 text-right">
                <breeze-button class="ml-4 py-3 bg-white text-gray-700 hover:text-white"
                               :class="{ 'opacity-25 cursor-not-allowed': !canBeUpdated('job')}"
                               :disabled="!canBeUpdated('job')"
                               @click="syncFromOld('job')">
                    Revert
                </breeze-button>
                <breeze-button class="ml-4 py-3 bg-green-500 text-white"
                               :class="{ 'opacity-25 cursor-not-allowed': !canBeUpdated('job')}"
                               :disabled="!canBeUpdated('job')" @click="update('job')">
                    Apply
                </breeze-button>
            </div>
        </breeze-card>

<!--        <breeze-card :title="'Monitor'">-->
<!--            <breeze-checkbox-line :label="'Enable monitor'" v-model:checked="monitor.enabled"/>-->
<!--            <breeze-input-line :label="'Refresh frequency, seconds'" :type="'text'"-->
<!--                               v-model="monitor.refresh_frequency"/>-->
<!--            <breeze-input-line :label="'History length, values'" :type="'text'" v-model="monitor.history_length"/>-->
<!--            <div class="px-6 mt-6 mb-2 text-right">-->
<!--                <breeze-button class="ml-4 py-3 bg-white text-gray-700 hover:text-white"-->
<!--                               :class="{ 'opacity-25 cursor-not-allowed': !canBeUpdated('monitor')}"-->
<!--                               :disabled="!canBeUpdated('monitor')"-->
<!--                               @click="syncFromOld('monitor')">-->
<!--                    Revert-->
<!--                </breeze-button>-->
<!--                <breeze-button class="ml-4 py-3 bg-green-500 text-white"-->
<!--                               :class="{ 'opacity-25 cursor-not-allowed': !canBeUpdated('monitor')}"-->
<!--                               :disabled="!canBeUpdated('monitor')" @click="update('monitor')">-->
<!--                    Apply-->
<!--                </breeze-button>-->
<!--            </div>-->
<!--        </breeze-card>-->

    </breeze-authenticated-layout>
</template>

<script>
import axios from 'axios';
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated';
import BreezeCheckboxLine from '@/Components/CheckboxLine';
import BreezeDropdown from '@/Components/Dropdown';
import BreezeCard from '@/Components/Card';
import BreezeInput from '@/Components/Input';
import BreezeInputLine from '@/Components/InputLine';
import BreezeLabel from '@/Components/Label';
import BreezeButton from '@/Components/Button';
import BreezeSelect from '@/Components/Select';

export default {
    components: {
        BreezeAuthenticatedLayout,
        BreezeCheckboxLine,
        BreezeDropdown,
        BreezeCard,
        BreezeInputLine,
        BreezeInput,
        BreezeLabel,
        BreezeButton,
        BreezeSelect,
    },

    props: {
        auth: Object,
        errors: Object,
        m_config: Object,
    },

    data() {
        return {
            old: {
                general: {},
                job: {},
                monitor: {},
            },
            general: {
                chia_path: null,
                default_farmer_key: null,
                default_pool_key: null,
            },
            job: {
                default_plot_size: null,
                default_buckets: null,
                default_buffer: null,
                default_threads: null,
                default_tmp_dir: null,
                default_tmp2_dir: null,
                default_final_dir: null,
                default_disable_bitfield: null,
                default_skip_add: null,
            },
            monitor: {
                enabled: true,
                refresh_frequency: null,
                history_length: null,
            },
            processing: {
                general: false,
                keys: false,
                job: false,
                monitor: false,
            }
        }
    },

    computed: {
        isGeneralChanged: function () {
            return (this.general_old.chia_path !== this.general.chia_path) ||
                (this.general_old.default_farmer_key !== this.general.default_farmer_key) ||
                (this.general_old.default_pool_key !== this.general.default_pool_key);
        },
        isJobChanged: function () {
            return (typeof this.m_config.job === 'undefined') || (
                (this.job.default_plot_size !== (typeof this.m_config.job.default_plot_size !== 'undefined' ? this.m_config.job.default_plot_size : null)) ||
                (this.job.default_buckets !== (typeof this.m_config.job.default_buckets !== 'undefined' ? this.m_config.job.default_buckets : null)) ||
                (this.job.default_buffer !== (typeof this.m_config.job.default_buffer !== 'undefined' ? this.m_config.job.default_buffer : null)) ||
                (this.job.default_threads !== (typeof this.m_config.job.default_threads !== 'undefined' ? this.m_config.job.default_threads : null)) ||
                (this.job.default_tmp_dir !== (typeof this.m_config.job.default_tmp_dir !== 'undefined' ? this.m_config.job.default_tmp_dir : null)) ||
                (this.job.default_tmp2_dir !== (typeof this.m_config.job.default_tmp2_dir !== 'undefined' ? this.m_config.job.default_tmp2_dir : null)) ||
                (this.job.default_final_dir !== (typeof this.m_config.job.default_final_dir !== 'undefined' ? this.m_config.job.default_final_dir : null)) ||
                (this.job.default_disable_bitfield !== (typeof this.m_config.job.default_disable_bitfield !== 'undefined' ? this.m_config.job.default_disable_bitfield : null)) ||
                (this.job.default_skip_add !== (typeof this.m_config.job.default_skip_add !== 'undefined' ? this.m_config.job.default_skip_add : null))
            );
        }
    },

    created() {
        if (typeof this.m_config.general !== 'undefined') {
            this.copy(this.m_config.general, this.general);
            this.syncToOld('general');
        }
        if (typeof this.m_config.job !== 'undefined') {
            this.copy(this.m_config.job, this.job);
            this.syncToOld('job');
        }
        if (typeof this.m_config.monitor !== 'undefined') {
            this.copy(this.m_config.monitor, this.monitor);
            this.syncToOld('monitor');
        }
    },

    methods: {
        copy(from, to) {
            Object.keys(from).map(key => {
                to[key] = from[key];
            })
        },

        syncToOld(key) {
            if (typeof this[key] !== 'undefined') {
                this.copy(this[key], this.old[key]);
            }
        },

        syncFromOld(key) {
            if (typeof this.old[key] !== 'undefined') {
                this.copy(this.old[key], this[key]);
            }
        },

        canBeUpdated(key) {
            // check if not updating right now and differs from old values
            return !this.processing[key] && JSON.stringify(this[key]) !== JSON.stringify(this.old[key]);
        },

        update(key) {
            if (!this.canBeUpdated(key)) {
                return;
            }

            this.processing[key] = true;

            axios.post('/api/config', {key: key, value: this[key]})
                .then((response) => {
                    this.copy(response.data[key], this[key]);
                    this.syncToOld(key);
                })
                .catch((error) => {
                    console.log(error);
                })
                .finally(() => {
                    this.processing[key] = false;
                })
        },

        getKeysFromChia() {
            if (this.processing.keys || !this.general.chia_path) {
                return;
            }
            this.processing.keys = true;

            axios.post('/api/get-keys', {path: this.general.chia_path})
                .then((response) => {
                    this.general.default_farmer_key = response.data.farmer_key;
                    this.general.default_pool_key = response.data.pool_key;
                })
                .catch((error) => {
                    console.log(error);
                })
                .finally(() => {
                    this.processing.keys = false;
                })
        }
    }
}
</script>
