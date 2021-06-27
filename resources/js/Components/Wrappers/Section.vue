<!--
  -  This file is part of the MyChiaFarm project.
  -
  -    (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
  -
  -  For the full copyright and license information, please view the LICENSE
  -  file that was distributed with this source code.
  -->

<template>
    <div class="bg-white pb-4">
        <div v-if="title" class="px-6 py-3 border-t border-gray-200">
            <h4 class="font-semibold text-md text-gray-800 leading-tight">{{ title }}</h4>
        </div>
        <slot></slot>
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
