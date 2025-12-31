<template>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-accent-primary to-accent-secondary p-5">
        <div class="bg-bg-elevated rounded-xl shadow-xl border border-surface-border p-10 w-full max-w-[400px]">
            <h1 class="text-3xl font-semibold text-text-primary mb-8 text-center bg-gradient-to-br from-accent-primary to-accent-secondary bg-clip-text text-transparent">Login</h1>
            <form @submit.prevent="handleLogin" class="flex flex-col gap-5">
                <div class="flex flex-col gap-2">
                    <label for="email" class="text-sm font-medium text-text-secondary">Email</label>
                    <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        class="py-3 px-4 border-2 border-surface-border rounded-lg text-sm bg-bg-primary text-text-primary transition-all duration-300 outline-none focus:border-border-focus focus:ring-2 focus:ring-accent-primary-light disabled:bg-bg-tertiary disabled:cursor-not-allowed disabled:opacity-60"
                        placeholder="Enter your email"
                        required
                        :disabled="authStore.loading"
                    />
                </div>

                <div class="flex flex-col gap-2">
                    <label for="password" class="text-sm font-medium text-text-secondary">Password</label>
                    <input
                        id="password"
                        v-model="form.password"
                        type="password"
                        class="py-3 px-4 border-2 border-surface-border rounded-lg text-sm bg-bg-primary text-text-primary transition-all duration-300 outline-none focus:border-border-focus focus:ring-2 focus:ring-accent-primary-light disabled:bg-bg-tertiary disabled:cursor-not-allowed disabled:opacity-60"
                        placeholder="Enter your password"
                        required
                        :disabled="authStore.loading"
                    />
                </div>

                <div class="flex flex-row items-center gap-2">
                    <label class="flex items-center gap-2 cursor-pointer font-normal">
                        <input
                            v-model="form.remember"
                            type="checkbox"
                            class="w-[18px] h-[18px] cursor-pointer"
                            :disabled="authStore.loading"
                        />
                        <span>Remember me</span>
                    </label>
                </div>

                <button
                    type="submit"
                    class="py-3.5 bg-gradient-to-br from-accent-primary to-accent-secondary text-text-inverse border-none rounded-lg text-base font-semibold cursor-pointer transition-all duration-300 mt-2.5 hover:-translate-y-0.5 hover:shadow-lg hover:brightness-110 active:translate-y-0 disabled:opacity-60 disabled:cursor-not-allowed"
                    :disabled="authStore.loading"
                >
                    <span v-if="authStore.loading">Logging in...</span>
                    <span v-else>Login</span>
                </button>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();

const form = ref({
    email: '',
    password: '',
    remember: false
});

const handleLogin = async () => {
    await authStore.login(form.value);
};
</script>

