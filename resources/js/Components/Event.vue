<template>
    <div class="p-2 text-sm hover:bg-blue-100" v-if="edit_mode">
        <span class="h-6 align-top inline-block" style="line-height: 1.5rem">Fire</span>
        <breeze-inline-input v-model="nameProxy" class="w-40 ml-2 inline-block" :placeholder="'event name'" :list="'events-list'"/>
        <breeze-inline-select v-model="firstModeProxy" :values="['on phase', 'on percents', 'after']" :placeholder="'on condition'"/>
        <!-- delay -->
        <breeze-inline-input v-if="withDelay" class="w-20 inline-block" v-model="delayValueProxy" :placeholder="'number of'"/>
        <span v-if="withDelay" class="h-6 ml-2 align-top inline-block" style="line-height: 1.5rem">seconds later than</span>
        <breeze-inline-select v-if="withDelay" v-model="secondModeProxy" :values="['phase', 'percents']" :placeholder="'condition'"/>

        <!-- phase -->
        <breeze-inline-select v-if="withPhase" v-model="phaseProxy" :values="phases" :placeholder="'number'"/>
        <breeze-inline-select v-if="withPhase" v-model="phaseConditionProxy" :values="['starts', 'ends']" :placeholder="'doing'"/>
        <!-- percents -->
        <breeze-inline-input v-if="withPercents" class="w-20 inline-block" v-model="percentCountProxy" :placeholder="'number'"/>
        <span v-if="withPercents" class="h-6 ml-2 align-top inline-block" style="line-height: 1.5rem">is done</span>
        <svg class="w-4 h-4 inline float-right m-1 cursor-pointer text-red-600" xmlns="http://www.w3.org/2000/svg"
             viewBox="0 0 20 20" fill="currentColor"
             @click="removeJob"
        >
            <path fill-rule="evenodd"
                  d="M 0.5468683,16.299691 6.8464951,10.000001 0.5468683,3.7004089 3.700374,0.54683812 l 6.299627,6.29969938 6.299624,-6.29969938 3.153507,3.15357078 -6.299626,6.2995921 6.299626,6.29969 -3.153507,3.153471 -6.299624,-6.299599 -6.299627,6.299599 z"
                  clip-rule="evenodd"/>
        </svg>
    </div>
    <div class="text-sm" v-if="!edit_mode">
        <span>Fire event </span>
        <span class="font-bold ml-1">{{ nameProxy }}</span>
        <span class="ml-1">{{ firstModeProxy !== 'on percents' ? firstModeProxy : 'on' }}</span>
        <!-- delay -->
        <span class="ml-1" v-if="withDelay">{{ delayValueProxy }} seconds later than</span>
        <!-- phase -->
        <span class="ml-1" v-if="withPhase">{{ phaseProxy }} {{ phaseConditionProxy }}</span>
        <!-- percents -->
        <span class="ml-1" v-if="withPercents">{{ percentCountProxy }}% is done</span>
    </div>
</template>

<script>
import BreezeInlineInput from "@/Components/InlineInput";
import BreezeInlineSelect from "@/Components/InlineSelect";

const phases = {
    '1_0': '1',
    '1_1': '1, computing table 1',
    '1_2': '1, computing table 2',
    '1_3': '1, computing table 3',
    '1_4': '1, computing table 4',
    '1_5': '1, computing table 5',
    '1_6': '1, computing table 6',
    '1_7': '1, computing table 7',
    '2_0': '2',
    '2_1': '2, backpropagating on table 7',
    '2_2': '2, backpropagating on table 6',
    '2_3': '2, backpropagating on table 5',
    '2_4': '2, backpropagating on table 4',
    '2_5': '2, backpropagating on table 3',
    '2_6': '2, backpropagating on table 2',
    '3_0': '3',
    '3_1': '3, compressing tables 1 and 2',
    '3_2': '3, compressing tables 2 and 3',
    '3_3': '3, compressing tables 3 and 4',
    '3_4': '3, compressing tables 4 and 5',
    '3_5': '3, compressing tables 5 and 6',
    '3_6': '3, compressing tables 6 and 7',
    '4_0': '4',
    '4_1': '4, writing C1 and C3 tables',
    '4_2': '4, writing C2 table',
    '5_0': 'of copying final file',
};

export default {
    name: "Event",

    components: {
        BreezeInlineSelect,
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
        // name: {type: String, default: null},
        // with_delay: {type: Boolean, default: false},
        // delay_seconds: {type: Number, default: null},
        // on_phase: {type: Boolean, default: false},
        // phase_number: {type: String, default: null},
        // phase_condition: {type: String, default: null},
        // on_percent: {type: Boolean, default: false},
        // percent_count: {type: Number, default: null},
    },

    computed: {
        nameProxy: {
            get: function () {
                return typeof this.origin['name'] !== 'undefined' ? this.origin['name'] : '';
            },
            set: function (val) {
                this.$emit('update', this.index, 'name', val);
            }
        },

        firstModeProxy: {
            get: function () {
                if (typeof this.origin['with_delay'] !== 'undefined' && (this.origin['with_delay'] === true || this.origin['with_delay'] === '1')) {
                    return 'after';
                }
                if (typeof this.origin['on_phase'] !== 'undefined' && (this.origin['on_phase'] === true || this.origin['on_phase'] === '1')) {
                    return 'on phase';
                }
                if (typeof this.origin['on_percent'] !== 'undefined' && (this.origin['on_percent'] === true || this.origin['on_percent'] === '1')) {
                    return 'on percents';
                }
                return '';
            },
            set: function (val) {
                if (val === 'on phase') {
                    this.$emit('update', this.index, 'on_phase', true);
                    this.$emit('update', this.index, 'on_percent', false);
                    this.$emit('update', this.index, 'with_delay', false);
                } else if (val === 'on percents') {
                    this.$emit('update', this.index, 'on_phase', false);
                    this.$emit('update', this.index, 'on_percent', true);
                    this.$emit('update', this.index, 'with_delay', false);
                } else {
                    this.$emit('update', this.index, 'with_delay', true);
                }
            },
        },

        secondModeProxy: {
            get: function () {
                if (typeof this.origin['on_phase'] !== 'undefined' && (this.origin['on_phase'] === true || this.origin['on_phase'] === '1')) {
                    return 'phase';
                }
                if (typeof this.origin['on_percent'] !== 'undefined' && (this.origin['on_percent'] === true || this.origin['on_percent'] === '1')) {
                    return 'percents';
                }
                return '';
            },
            set: function (val) {
                if (val === 'phase') {
                    this.$emit('update', this.index, 'on_phase', true);
                    this.$emit('update', this.index, 'on_percent', false);
                } else if (val === 'percents') {
                    this.$emit('update', this.index, 'on_phase', false);
                    this.$emit('update', this.index, 'on_percent', true);
                }
            },
        },

        delayValueProxy: {
            get: function () {
                return typeof this.origin['delay_seconds'] !== 'undefined' ? this.origin['delay_seconds'] : null;
            },
            set: function (val) {
                this.$emit('update', this.index, 'delay_seconds', val);
            }
        },

        phaseProxy: {
            get: function () {
                const n = typeof this.origin['phase_number'] !== 'undefined' ? this.origin['phase_number'] : null;
                if (n !== null && typeof this.phases[n] !== 'undefined') {
                    return this.phases[n];
                }
                return null;
            },
            set: function (val) {
                let key = null;
                Object.keys(this.phases).some(k => {
                    if(this.phases[k] === val) {
                        key = k;
                        return true;
                    }
                    return false;
                })
                this.$emit('update', this.index, 'phase_number', key);
            }
        },

        phaseConditionProxy: {
            get: function () {
                return typeof this.origin['phase_condition'] !== 'undefined' ? this.origin['phase_condition'] : null;
            },
            set: function (val) {
                this.$emit('update', this.index, 'phase_condition', val);
            }
        },

        percentCountProxy: {
            get: function () {
                return typeof this.origin['percent_count'] !== 'undefined' ? this.origin['percent_count'] : null;
            },
            set: function (val) {
                this.$emit('update', this.index, 'percent_count', val);
            }
        },

        withDelay: function () {
            return typeof this.origin['with_delay'] !== 'undefined' && (this.origin['with_delay'] === true || this.origin['with_delay'] === '1');
        },
        withPhase: function () {
            return typeof this.origin['on_phase'] !== 'undefined' && (this.origin['on_phase'] === true || this.origin['on_phase'] === '1');
        },
        withPercents: function () {
            return typeof this.origin['on_percent'] !== 'undefined' && (this.origin['on_percent'] === true || this.origin['on_percent'] === '1');
        },
    },

    data: () => ({
        phases: phases,
    }),

    methods: {
        removeJob() {
            this.$emit('remove', this.index);
        },
    },
}
</script>
