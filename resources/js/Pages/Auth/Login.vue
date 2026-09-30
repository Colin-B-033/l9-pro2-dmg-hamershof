<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.transform(data => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Log in" />

    <main class="login-screen">
        <Link href="/" class="login-logo" aria-label="Home">
            <svg viewBox="0 0 48 48" aria-hidden="true">
                <path d="M11.395 44.428C4.557 40.198 0 32.632 0 24 0 10.745 10.745 0 24 0a23.891 23.891 0 0113.997 4.502c-.2 17.907-11.097 33.245-26.602 39.926z" />
                <path d="M14.134 45.885A23.914 23.914 0 0024 48c13.255 0 24-10.745 24-24 0-3.516-.756-6.856-2.115-9.866-4.659 15.143-16.608 27.092-31.75 31.751z" />
            </svg>
        </Link>

        <section class="login-panel" aria-labelledby="login-title">
            <h1 id="login-title" class="sr-only">Log in</h1>
            <p v-if="status" class="login-status">{{ status }}</p>

            <form class="login-form" @submit.prevent="submit">
                <div class="login-field">
                    <label for="email">Email</label>
                    <input id="email" v-model="form.email" type="email" required autofocus autocomplete="username">
                    <p v-if="form.errors.email" class="login-error">{{ form.errors.email }}</p>
                </div>

                <div class="login-field">
                    <label for="password">Password</label>
                    <input id="password" v-model="form.password" type="password" required autocomplete="current-password">
                    <p v-if="form.errors.password" class="login-error">{{ form.errors.password }}</p>
                </div>

                <label class="remember-field">
                    <input v-model="form.remember" type="checkbox" name="remember">
                    <span>Remember me</span>
                </label>

                <div class="login-actions">
                    <Link v-if="canResetPassword" :href="route('password.request')" class="forgot-link">Forgot your password?</Link>
                    <button type="submit" :disabled="form.processing">LOG IN</button>
                </div>
            </form>
        </section>
    </main>
</template>

<style scoped>
.login-screen { display: flex; min-height: 100vh; flex-direction: column; align-items: center; padding-top: 233px; background: #f3f4f6; color: #334155; font-family: Arial, sans-serif; }
.login-logo { display: block; width: 58px; height: 58px; margin-bottom: 21px; }
.login-logo svg { display: block; width: 100%; height: 100%; }
.login-logo path { fill: #6875f5; }
.login-panel { width: 405px; min-height: 240px; padding: 17px 22px 14px; border-radius: 8px; background: #fff; box-shadow: 0 2px 4px rgb(0 0 0 / 12%); }
.login-form { display: flex; flex-direction: column; }
.login-field { margin-bottom: 14px; }
.login-field label, .remember-field, .forgot-link { color: #475569; font-size: 12px; }
.login-field label { display: block; margin-bottom: 5px; }
.login-field input[type='email'], .login-field input[type='password'] { display: block; width: 100%; height: 40px; padding: 8px 10px; border: 1px solid #d7dce2; border-radius: 5px; background: #fff; color: #334155; font-size: 14px; outline: none; }
.login-field input:focus { border-color: #6366f1; box-shadow: 0 0 0 1px #6366f1; }
.login-error, .login-status { margin: 5px 0 0; font-size: 12px; }
.login-error { color: #dc2626; }
.login-status { color: #16a34a; }
.remember-field { display: flex; align-items: center; gap: 7px; margin: 0 0 16px; }
.remember-field input { width: 15px; height: 15px; margin: 0; accent-color: #6366f1; }
.login-actions { display: flex; align-items: center; justify-content: flex-end; gap: 15px; }
.forgot-link { text-decoration: underline; }
.login-actions button { min-width: 74px; height: 32px; padding: 0 15px; border: 0; border-radius: 5px; background: #1f2937; color: #fff; cursor: pointer; font-size: 11px; font-weight: 700; letter-spacing: 0.2px; }
.login-actions button:disabled { cursor: wait; opacity: 0.6; }
.sr-only { position: absolute; width: 1px; height: 1px; padding: 0; overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border: 0; }
@media (max-width: 520px) { .login-screen { padding: 120px 16px 24px; } .login-panel { width: 100%; max-width: 405px; } }
</style>
