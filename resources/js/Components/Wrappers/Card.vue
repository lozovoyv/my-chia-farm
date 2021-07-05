<!--
  -  This file is part of the MyChiaFarm project.
  -
  -    (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
  -
  -  For the full copyright and license information, please view the LICENSE
  -  file that was distributed with this source code.
  -->

<template>
    <div class="my-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-700 pb-4 shadow-sm sm:rounded-lg">
                <div v-if="title" class="p-6 border-b border-gray-200 dark:border-gray-600">
                    <h3 class="font-semibold text-xl text-gray-800 dark:text-gray-300 leading-tight">{{ title }}</h3>
                </div>
                <slot></slot>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "Card",

    props: {
        title: {
            type: String,
            default: null
        }
    },
}
</script>
