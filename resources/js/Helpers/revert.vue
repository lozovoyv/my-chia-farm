<!--
  -  This file is part of the MyChiaFarm project.
  -
  -    (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
  -
  -  For the full copyright and license information, please view the LICENSE
  -  file that was distributed with this source code.
  -->

<script>
import clone from "@/Helpers/Lib/clone";

export default {
    methods: {
        revert(key_chain) {
            if (typeof key_chain === 'undefined') {
                this.values = clone(this.old);
                return;
            }

            let values = this.values;
            let old = this.old;

            const keys = key_chain.split('.');
            let i;

            for (i = 0; i < keys.length - 1; i++) {
                values = values[keys[i]];
                old = old[keys[i]];
            }

            values[keys[i]] = clone(old)[keys[i]];
        }
    }
}
</script>
