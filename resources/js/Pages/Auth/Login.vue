<template>
    <div v-if="status" class="mb-4 font-medium text-sm text-green-600">{{ status }}</div>

    <form @submit.prevent="submit">
        <m-c-f-input :type="'string'" :input-type="'email'" v-model="form.email" :title="'Email'" :required="true"
                     :autocomplete="'username'"/>
        <m-c-f-input :type="'string'" :input-type="'password'" v-model="form.password" :title="'Password'"
                     :required="true" :autocomplete="'current-password'"/>
        <m-c-f-input :type="'bool'" v-model="form.remember" :title="'Remember me'"/>

        <!--<inertia-link v-if="canResetPassword" :href="route('password.request')" class="underline text-sm text-gray-600 hover:text-gray-900">-->
        <!--Forgot your password?-->
        <!--</inertia-link>-->

        <validation-errors/>

        <m-c-f-buttons :align="'right'">
            <m-c-f-button :type="'submit'" :theme="'green'" :class="{ 'opacity-25': form.processing }"
                          :disabled="form.processing" :caption="'Log in'"/>
        </m-c-f-buttons>
    </form>
</template>

<script>
import GuestLayout from "@/Layouts/Guest";
import MCFButtons from '@/Components/Wrappers/Buttons'
import MCFButton from '@/Components/Buttons/Button'
import MCFInput from "@/Components/Inputs/Input";

import ValidationErrors from '@/Components/ValidationErrors'

export default {
    layout: GuestLayout,

    components: {
        MCFButtons,
        MCFButton,
        MCFInput,
        ValidationErrors
    },

    props: {
        auth: Object,
        canResetPassword: Boolean,
        errors: Object,
        status: String,
    },

    data() {
        return {
            form: this.$inertia.form({
                email: '',
                password: '',
                remember: false
            })
        }
    },

    methods: {
        submit() {
            this.form.post(this.route('login'), {
                onFinish: () => this.form.reset('password'),
            })
        }
    }
}
</script>
