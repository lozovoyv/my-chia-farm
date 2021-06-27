<!--
  -  This file is part of the MyChiaFarm project.
  -
  -    (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
  -
  -  For the full copyright and license information, please view the LICENSE
  -  file that was distributed with this source code.
  -->

<template>
    <div class="px-6 mt-6 mb-2" :class="class_name">
        <slot></slot>
    </div>
</template>

<script>
export default {
    name: "Buttons",

    props: {
        align: {
            type: String,
            default: null,
        }
    },

    computed: {
        class_name: function () {
            return this.align ? 'text-' + this.align : null
        }
    }
}
</script>
