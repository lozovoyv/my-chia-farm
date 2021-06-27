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
        clone: clone,

        cloneFrom(key_chain) {
            let source = this;
            const keys = key_chain.split('.');
            let i;

            for (i = 0; i < keys.length - 1; i++) {
                source = source[keys[i]];
            }

            return clone(source[keys[i]]);
        },

        cloneTo(key_chain, value) {
            let destination = this;
            const keys = key_chain.split('.');
            let i;

            for (i = 0; i < keys.length - 1; i++) {
                destination = destination[keys[i]];
            }

            destination[keys[i]] = clone(value);
        },
    }
}
</script>
