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
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="shadow-sm sm:rounded-lg py-0.5 bg-white">
                <div v-if="title || $slots.header" class="px-6 py-3 border-b border-gray-200 relative">
                    <h3 v-if="title" class="font-semibold text-xl text-gray-800 leading-tight">{{ title }}</h3>
                    <slot name="header"/>
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
