<template>
    <inertia-link :href="href" :class="classes">
        <slot/>
    </inertia-link>
</template>

<script>
export default {
    props: {
        href: {type: String, default: '/'},
        active: {type: Boolean, default: false},
        responsive: {type: Boolean, default: false},
    },

    computed: {
        classes() {
            let classes;

            if (this.responsive) {
                classes = 'block pl-3 pr-4 py-2 border-l-4 text-base font-medium focus:outline-none transition duration-150 ease-in-out w-full text-left';
            } else {
                classes = 'inline-flex items-center px-1 pt-1 border-b-2 transition duration-150 ease-in-out text-sm font-medium leading-5 focus:outline-none';
            }

            return classes + (this.active ? ' text-gray-900 dark:text-gray-200 border-green-400 dark:border-green-600'
                : ' text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 focus:text-gray-700 dark:focus:text-gray-200 ' +
                'border-transparent hover:border-gray-300 dark:hover:border-gray-500 focus:border-gray-300 dark:focus:border-gray-500');
        }
    }
}
</script>
