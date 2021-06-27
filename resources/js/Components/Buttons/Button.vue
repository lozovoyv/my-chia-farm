<!--
  -  This file is part of the MyChiaFarm project.
  -
  -    (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
  -
  -  For the full copyright and license information, please view the LICENSE
  -  file that was distributed with this source code.
  -->

<template>
    <button :type="type" :disabled="disabled" :class="class_name">
        <slot/>
        {{ caption }}
    </button>
</template>

<script>
export default {
    name: "Button",

    props: {
        type: {type: String, default: 'submit'},
        disabled: {type: Boolean, default: false},
        theme: {type: String, default: 'default'},
        caption: {type: String, default: null},
    },

    computed: {
        class_name: function () {
            // common styling
            let classes = 'border border-transparent rounded-md font-semibold text-sm uppercase focus:outline-none transition ease-in-out duration-150 tracking-widest';
            // spacing
            classes += ' inline-flex items-center px-4 py-2 ml-2';

            // color themes
            switch (this.theme) {
                case 'white':
                    classes += ' bg-white text-gray-800';
                    break;
                case 'green':
                    classes += ' bg-green-500 text-white';
                    break;
                case 'blue':
                    classes += ' bg-blue-500 text-white';
                    break;
                case 'red':
                    classes += ' bg-red-500 text-white';
                    break;
                case 'yellow':
                    classes += ' bg-yellow-500 text-white';
                    break;
                case 'default':
                default:
                    classes += ' bg-gray-500 text-white';
            }

            if (this.disabled) {
                classes += ' opacity-40 cursor-not-allowed';
            } else {
                classes += ' hover:bg-indigo-900 active:bg-indigo-900 hover:text-white focus:shadow-outline-gray'
            }

            return classes;
        },
    },
}
</script>

