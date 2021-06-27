<!--
  -  This file is part of the MyChiaFarm project.
  -
  -    (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
  -
  -  For the full copyright and license information, please view the LICENSE
  -  file that was distributed with this source code.
  -->

<script>
function validate(obj) {
    return !Object.keys(obj).some(key => {
        if (typeof obj[key] !== 'object') return obj[key] === false;
        return !validate(obj[key]);
    });
}

export default {
    methods: {
        isValid(key_chain) {
            let validation = this.validation;

            if (typeof key_chain !== 'undefined') {
                const keys = key_chain.split('.');
                keys.map(key => {
                    validation = validation[key];
                });
            }

            if (typeof validation !== 'object') return validation === true;

            return validate(validation);
        },

        setValidation(key_chain, state) {
            let validation = this.validation;
            const keys = key_chain.split('.');
            let i;

            for (i = 0; i < keys.length - 1; i++) {
                if (typeof validation[keys[i]] === 'undefined') {
                    validation[keys[i]] = {};
                }
                validation = validation[keys[i]];
            }

            validation[keys[i]] = state;
        },
    }
}
</script>
