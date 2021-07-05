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
                    classes += ' bg-transparent text-gray-800 dark:text-gray-200';
                    break;
                case 'green':
                    classes += ' bg-green-500 dark:bg-green-600 text-white dark:text-gray-200';
                    break;
                case 'blue':
                    classes += ' bg-blue-500 dark:bg-blue-600 text-white dark:text-gray-200';
                    break;
                case 'red':
                    classes += ' bg-red-500 dark:bg-red-600 text-white dark:text-gray-200';
                    break;
                case 'yellow':
                    classes += ' bg-yellow-500 dark:bg-yellow-600 text-white dark:text-gray-200';
                    break;
                case 'default':
                default:
                    classes += ' bg-gray-500 dark:bg-gray-600 text-white dark:text-gray-200';
            }

            if (this.disabled) {
                classes += ' opacity-40 cursor-not-allowed';
            } else {
                classes += ' hover:bg-indigo-900 dark:hover:bg-indigo-900 active:bg-indigo-900 dark:active:bg-indigo-900 hover:text-white dark:hover:text-gray-100 focus:shadow-outline-gray'
            }

            return classes;
        },
    },
}
</script>

