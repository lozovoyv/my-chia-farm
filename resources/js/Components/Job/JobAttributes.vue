<!--
  -  This file is part of the MyChiaFarm project.
  -
  -    (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
  -
  -  For the full copyright and license information, please view the LICENSE
  -  file that was distributed with this source code.
  -->

<script>
import empty from "@/Helpers/Lib/empty";

export default {
    computed: {
        title: {
            get() {
                if (typeof this.values['title'] === 'undefined') this.values['title'] = 'New job';
                return this.values['title'];
            },
            set(val) {
                this.values['title'] = val;
            },
        },
        plots_to_do: {
            get() {
                if (typeof this.values['plots_to_do'] === 'undefined') this.values['plots_to_do'] = 0;
                return this.values['plots_to_do'];
            },
            set(val) {
                this.values['plots_to_do'] = val;
            },
        },
        plots_done: {
            get() {
                if (typeof this.values['plots_done'] === 'undefined') this.values['plots_done'] = 0;
                return this.values['plots_done'];
            },
            set(val) {
                this.values['plots_done'] = val;
            },
        },
        plotter_alias: {
            get() {
                if (typeof this.values['plotter_alias'] === 'undefined') this.values['plotter_alias'] = null;
                return this.values['plotter_alias'];
            },
            set(val) {
                this.values['plotter_alias'] = val;
            },
        },
        executable: {
            get() {
                if (typeof this.values['executable'] === 'undefined') this.values['executable'] = null;
                return this.values['executable'];
            },
            set(val) {
                this.values['executable'] = val;
            },
        },
        use_default_executable: {
            get() {
                if (typeof this.values['use_default_executable'] === 'undefined') this.values['use_default_executable'] = true;
                return this.values['use_default_executable'];
            },
            set(val) {
                this.values['use_default_executable'] = val;
            },
        },
        cpu_affinity_enable: {
            get() {
                if (typeof this.values['cpu_affinity_enable'] === 'undefined') this.values['cpu_affinity_enable'] = false;
                return this.values['cpu_affinity_enable'];
            },
            set(val) {
                this.values['cpu_affinity_enable'] = val;
            },
        },
        cpus: {
            get() {
                if (typeof this.values['cpus'] === 'undefined' || this.values['cpus'] === null) this.values['cpus'] = [];
                return this.values['cpus'];
            },
            set(val) {
                this.values['cpus'] = val;
            },
        },
        pre_command_enabled: {
            get() {
                if (typeof this.values['pre_command_enabled'] === 'undefined') this.values['pre_command_enabled'] = false;
                return this.values['pre_command_enabled'];
            },
            set(val) {
                this.values['pre_command_enabled'] = val;
            },
        },
        pre_command: {
            get() {
                if (typeof this.values['pre_command'] === 'undefined') this.values['pre_command'] = null;
                return this.values['pre_command'];
            },
            set(val) {
                this.values['pre_command'] = val;
            },
        },
        post_command_enabled: {
            get() {
                if (typeof this.values['post_command_enabled'] === 'undefined') this.values['post_command_enabled'] = false;
                return this.values['post_command_enabled'];
            },
            set(val) {
                this.values['post_command_enabled'] = val;
            },
        },
        post_command: {
            get() {
                if (typeof this.values['post_command'] === 'undefined') this.values['post_command'] = null;
                return this.values['post_command'];
            },
            set(val) {
                this.values['post_command'] = val;
            },
        },
        remove_oldest: {
            get() {
                if (typeof this.values['remove_oldest'] === 'undefined') this.values['remove_oldest'] = false;
                return this.values['remove_oldest'];
            },
            set(val) {
                this.values['remove_oldest'] = val;
            },
        },
        removing_stop_ts: {
            get() {
                if (typeof this.values['removing_stop_ts'] === 'undefined' || empty(this.values['removing_stop_ts'])) this.values['removing_stop_ts'] = this.now;
                return this.values['removing_stop_ts'];
            },
            set(val) {
                this.values['removing_stop_ts'] = val;
            },
        },
        disable_events_emitting: {
            get() {
                if (typeof this.values['disable_events_emitting'] === 'undefined') this.values['disable_events_emitting'] = false;
                return this.values['disable_events_emitting'];
            },
            set(val) {
                this.values['disable_events_emitting'] = val;
            }
        },
        disable_workers_start: {
            get() {
                if (typeof this.values['disable_workers_start'] === 'undefined') this.values['disable_workers_start'] = false;
                return this.values['disable_workers_start'];
            },
            set(val) {
                this.values['disable_workers_start'] = val;
            }
        },
        events: {
            get() {
                if (typeof this.values['events'] === 'undefined') this.values['events'] = [];
                return this.values['events'];
            },
            set(val) {
                this.values['events'] = val;
            }
        },
        max_workers: {
            get() {
                if (typeof this.values['max_workers'] === 'undefined') this.values['max_workers'] = 0;
                return this.values['max_workers'];
            },
            set(val) {
                this.values['max_workers'] = val;
            }
        },
        save_worker_log: {
            get() {
                if (typeof this.values['save_worker_log'] === 'undefined') this.values['save_worker_log'] = false;
                return this.values['save_worker_log'];
            },
            set(val) {
                this.values['save_worker_log'] = val;
            }
        },
        number_of_worker_logs: {
            get() {
                if (typeof this.values['number_of_worker_logs'] === 'undefined' || this.values['number_of_worker_logs'] === null) this.values['number_of_worker_logs'] = 0;
                return this.values['number_of_worker_logs'];
            },
            set(val) {
                this.values['number_of_worker_logs'] = val;
            }
        },
        starts: {
            get() {
                if (typeof this.values['starts'] === 'undefined') this.values['starts'] = [];
                return this.values['starts'];
            },
            set(val) {
                this.values['starts'] = val;
            }
        },
    }
}
</script>
