<!--
  -  This file is part of the MyChiaFarm project.
  -
  -    (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
  -
  -  For the full copyright and license information, please view the LICENSE
  -  file that was distributed with this source code.
  -->

<template>
    <div class="cursor-pointer text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200" @click="switchMode">
        <svg v-if="!darkMode" class="w-6 h-6" focusable="false" viewBox="0 0 24 24"
             aria-hidden="true"
             fill="currentColor">
            <path fill-rule="evenodd"
                  d="M20 8.69V4h-4.69L12 .69 8.69 4H4v4.69L.69 12 4 15.31V20h4.69L12 23.31 15.31 20H20v-4.69L23.31 12 20 8.69zM12 18c-.89 0-1.74-.2-2.5-.55C11.56 16.5 13 14.42 13 12s-1.44-4.5-3.5-5.45C10.26 6.2 11.11 6 12 6c3.31 0 6 2.69 6 6s-2.69 6-6 6z"
                  clip-rule="evenodd"></path>
        </svg>
        <svg v-if="darkMode" class="w-6 h-6" focusable="false" viewBox="0 0 24 24"
             aria-hidden="true"
             fill="currentColor">
            <path fill-rule="evenodd"
                  d="M20 8.69V4h-4.69L12 .69 8.69 4H4v4.69L.69 12 4 15.31V20h4.69L12 23.31 15.31 20H20v-4.69L23.31 12 20 8.69zM12 18c-3.31 0-6-2.69-6-6s2.69-6 6-6 6 2.69 6 6-2.69 6-6 6zm0-10c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4z"
                  clip-rule="evenodd"></path>
        </svg>
    </div>
</template>

<script>
export default {
    data() {
        return {
            showingNavigationDropdown: false,
            darkMode: false,
        }
    },

    created() {
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
            this.darkMode = true;
        } else {
            document.documentElement.classList.remove('dark');
            this.darkMode = false;
        }
    },

    methods: {
        switchMode() {
            this.darkMode = !this.darkMode;

            if (this.darkMode) {
                localStorage.theme = 'dark'
                document.documentElement.classList.add('dark');
            } else {
                localStorage.theme = 'light'
                document.documentElement.classList.remove('dark');
            }
        },
    }
}
</script>

