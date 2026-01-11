<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Create Account" />

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
            <p class="text-gray-400 font-bold text-[10px] uppercase tracking-[0.2em] mt-3">Join the community of trend mixers</p>
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            <div>
                <InputLabel for="name" value="User Name" class="text-[10px] font-black text-gray-400 tracking-widest ml-1 mb-2 uppercase" />
                <TextInput
                    id="name"
                    type="text"
                    class="block w-full px-5 py-3.5 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-indigo-600 transition-all"
                    v-model="form.name"
                    required
                    autofocus
                    placeholder="Your Stage Name"
                    autocomplete="name"
                />
                <InputError class="mt-2 ml-1" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="Email Address" class="text-[10px] font-black text-gray-400 tracking-widest ml-1 mb-2 uppercase" />
                <TextInput
                    id="email"
                    type="email"
                    class="block w-full px-5 py-3.5 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-indigo-600 transition-all"
                    v-model="form.email"
                    required
                    placeholder="email@example.com"
                    autocomplete="username"
                />
                <InputError class="mt-2 ml-1" :message="form.errors.email" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <InputLabel for="password" value="Password" class="text-[10px] font-black text-gray-400 tracking-widest ml-1 mb-2 uppercase" />
                    <TextInput
                        id="password"
                        type="password"
                        class="block w-full px-5 py-3.5 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-indigo-600 transition-all"
                        v-model="form.password"
                        required
                        placeholder="••••••••"
                        autocomplete="new-password"
                    />
                    <InputError class="mt-2 ml-1" :message="form.errors.password" />
                </div>

                <div>
                    <InputLabel for="password_confirmation" value="Confirm" class="text-[10px] font-black text-gray-400 tracking-widest ml-1 mb-2 uppercase" />
                    <TextInput
                        id="password_confirmation"
                        type="password"
                        class="block w-full px-5 py-3.5 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-indigo-600 transition-all"
                        v-model="form.password_confirmation"
                        required
                        placeholder="••••••••"
                        autocomplete="new-password"
                    />
                    <InputError class="mt-2 ml-1" :message="form.errors.password_confirmation" />
                </div>
            </div>

            <div class="pt-4">
                <button
                    class="w-full py-4 bg-gray-900 text-white rounded-2xl font-black text-sm shadow-xl shadow-gray-200 hover:bg-indigo-600 hover:-translate-y-1 active:scale-[0.98] transition-all uppercase tracking-widest flex items-center justify-center gap-2"
                    :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                    Start Mixing Now
                </button>
            </div>

            <div class="text-center pt-4">
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-tighter">
                    Already part of the trend? 
                    <Link :href="route('login')" class="text-indigo-600 hover:text-indigo-800 ml-1 underline underline-offset-4">
                        Login here
                    </Link>
                </p>
            </div>
        </form>
    </GuestLayout>
</template>