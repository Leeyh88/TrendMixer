<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: { type: Boolean },
    status: { type: String },
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
        <Head title="로그인" />

       <div class="mb-10 text-center">
            <div class="mb-6 inline-flex items-center justify-center">
                <div class="relative mt-6">
                    <div class="absolute inset-0 bg-indigo-500 blur-2xl opacity-20 animate-pulse"></div>
                    <Link href="/" class="inline-block">
                        <span class="text-3xl font-black text-gray-900 group-hover:text-indigo-600 transition-all duration-300">
                            trend<span class="text-indigo-600 group-hover:text-gray-900">Vibe</span>
                        </span>
                        <span class="ml-1 w-1.5 h-1.5 bg-indigo-600 rounded-full animate-pulse"></span>
                    </Link>
                </div>
            </div>
            <p class="text-gray-400 font-bold text-[10px] uppercase tracking-[0.2em] mt-3">Log in to explore your vibes</p>
        </div>

        <div v-if="status" class="mb-6 p-4 bg-green-50 rounded-2xl text-xs font-bold text-green-600 border border-green-100">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-6 mb-4">
            <div>
                <InputLabel for="email" value="EMAIL ADDRESS" class="text-[10px] font-black text-gray-400 tracking-widest ml-1 mb-2" />
                <TextInput
                    id="email"
                    type="email"
                    class="block w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-indigo-600 transition-all shadow-sm"
                    v-model="form.email"
                    required
                    autofocus
                    placeholder="Enter your email"
                    autocomplete="username"
                />
                <InputError class="mt-2 ml-1" :message="form.errors.email" />
            </div>

            <div>
                <div class="flex items-center justify-between ml-1 mb-2">
                    <InputLabel for="password" value="PASSWORD" class="text-[10px] font-black text-gray-400 tracking-widest" />
                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-[10px] font-black text-indigo-600 hover:text-indigo-800 uppercase tracking-tighter"
                    >
                        Forgot?
                    </Link>
                </div>
                <TextInput
                    id="password"
                    type="password"
                    class="block w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-indigo-600 transition-all shadow-sm"
                    v-model="form.password"
                    required
                    placeholder="••••••••"
                    autocomplete="current-password"
                />
                <InputError class="mt-2 ml-1" :message="form.errors.password" />
            </div>

            <div class="flex items-center justify-between px-1">
                <label class="flex items-center cursor-pointer group">
                    <Checkbox name="remember" v-model:checked="form.remember" class="rounded-md border-gray-200 text-indigo-600 focus:ring-indigo-500 w-4 h-4" />
                    <span class="ms-2 text-[11px] font-bold text-gray-500 group-hover:text-gray-700 transition-colors uppercase tracking-tighter">Remember me</span>
                </label>
            </div>

            <div class="pt-2">
                <button
                    class="w-full py-4 bg-gray-900 text-white rounded-2xl font-black text-sm shadow-xl shadow-gray-200 hover:bg-indigo-600 hover:-translate-y-1 active:scale-[0.98] transition-all uppercase tracking-widest flex items-center justify-center gap-2"
                    :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                    Log In Now
                </button>
            </div>

            <div class="text-center pt-4">
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-tighter">
                    Don't have an account? 
                    <Link :href="route('register')" class="text-indigo-600 hover:text-indigo-800 ml-1 underline underline-offset-4">
                        Create one free
                    </Link>
                </p>
            </div>
        </form>
    </GuestLayout>
</template>