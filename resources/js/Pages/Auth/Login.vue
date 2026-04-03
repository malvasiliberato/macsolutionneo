<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Login" />

        <div class="text-center mt-2">
            <h5 class="text-primary">Accedi al portale</h5>
            <p class="text-muted">Auth foundation iniziale pronta a evolvere verso Identity + Organization.</p>
        </div>

        <div v-if="status" class="alert alert-success mt-4 mb-0">
            {{ status }}
        </div>

        <div class="p-2 mt-4">
            <form @submit.prevent="submit">
                <div class="mb-3">
                    <InputLabel for="email" value="Email" />
                    <TextInput
                        id="email"
                        type="email"
                        class="form-control mt-1"
                        v-model="form.email"
                        required
                        autofocus
                        autocomplete="username"
                    />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div class="mb-3">
                    <div class="float-end" v-if="canResetPassword">
                        <Link :href="route('password.request')" class="text-muted">Password dimenticata?</Link>
                    </div>
                    <InputLabel for="password" value="Password" />
                    <TextInput
                        id="password"
                        type="password"
                        class="form-control mt-1"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                    />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div class="form-check mb-3">
                    <Checkbox name="remember" v-model:checked="form.remember" class="form-check-input" />
                    <label class="form-check-label" for="remember">Ricordami</label>
                </div>

                <div class="mt-4">
                    <PrimaryButton class="btn btn-success w-100 justify-content-center" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Accedi
                    </PrimaryButton>
                </div>
            </form>

            <div class="alert alert-info mt-4 mb-0">
                <strong>Utente bootstrap locale</strong><br>
                Email: <code>admin@macsolution.test</code><br>
                Password: <code>password</code>
            </div>
        </div>
    </GuestLayout>
</template>
