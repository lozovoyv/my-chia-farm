<!--
  -  This file is part of the MyChiaFarm project.
  -
  -    (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
  -
  -  For the full copyright and license information, please view the LICENSE
  -  file that was distributed with this source code.
  -->

<script>
export default {
    methods: {
        changed(key_chain) {
            let values = this.values;
            let old = this.old;

            if (typeof key_chain !== 'undefined') {
                const keys = key_chain.split('.');
                keys.map(key => {
                    values = values[key];
                    old = old[key];
                });
            }

            return JSON.stringify(values) !== JSON.stringify(old);
        }
    }
}
</script>
