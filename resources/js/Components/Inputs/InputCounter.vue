<!--
  -  This file is part of the MyChiaFarm project.
  -
  -    (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
  -
  -  For the full copyright and license information, please view the LICENSE
  -  file that was distributed with this source code.
  -->

<template>
    <div class="px-6 my-4">
        <label class="block font-medium text-sm text-gray-700">
            <span>{{ title }}: <span class="font-bold">{{ modelValue }}</span> (<span class="text-blue-700 cursor-pointer"
                                                      @click="$emit('update:modelValue',0)">reset</span>)</span>
        </label>
    </div>
</template>

<script>
export default {
    props: ['modelValue', 'title', 'inputType', 'required', 'options', 'valid', 'errorMessage'],

    emits: ['update:modelValue'],
}
</script>

