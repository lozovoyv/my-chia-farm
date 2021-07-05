<template>
    <div v-if="hasErrors" class="px-6 my-4">
        <ul class="mt-3 text-md text-red-700 dark:text-red-400">
            <li v-for="(error, key) in errors" :key="key">{{ error }}</li>
        </ul>
    </div>
</template>

<script>
    export default {
        computed: {
            errors() {
                return this.$page.props.errors
            },

            hasErrors() {
                return Object.keys(this.errors).length > 0;
            },
        }
    }
</script>
