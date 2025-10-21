<template>
    <div class="text-center py-10 text-white">
        <h1 class="text-4xl font-extrabold vue_inertia_color vue_inertia_bg_color mb-4">Vue Inertia Book Management</h1>
        <h2 class="text-lg text-white">Welcome to the Book Management System</h2>
    </div>
    <div class="flex justify-center space-x-4 mt-6">
        <Link href="/books" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition">
            Book Dashboard
        </Link>
        <Link href="/login" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition">
            Get Log In
        </Link>
        <Link href="/registration" class="px-4 py-2 bg-purple-500 text-white rounded hover:bg-purple-600 transition">
            Sign Up
        </Link>
    </div>
</template>
<script setup>
    import { Link } from '@inertiajs/vue3';
    import { ref } from 'vue';
    import axios from 'axios';
</script>