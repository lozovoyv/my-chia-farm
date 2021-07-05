<template>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex h-16 flex-row-reverse items-center">
        <theme-switch class="flex items-center h-16 px-4"></theme-switch>
    </div>

    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-700 shadow-md overflow-hidden sm:rounded-lg sm:-top-16 relative">
            <slot />
        </div>
    </div>
</template>

<script>
import ThemeSwitch from "@/Components/Buttons/ThemeSwitch";

export default {
    components: {
        ThemeSwitch,
    },
}
</script>
