<script setup>
    import { Link, router } from '@inertiajs/vue3';
    import { ref } from 'vue';
    import axios from 'axios';
    
    // Variables to use.
    const email = ref('');
    const password = ref('');
    const errorMessages = ref(null);
    const isLoading = ref(false);

    axios.interceptors.request.use((config) => {
        const token = localStorage.getItem('token');
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
    });

    // Login method.
    const login_method = async () => {
        isLoading.value = true;
        errorMessages.value = null;

        try {
            // Send login request to the api.
            const response = await axios.post('/api/v1/auth/login', {
                email: email.value,
                password: password.value,
            });

            // Check if the login was successful.
            if ( response.data.token ) {
                // Storing the access token and token type.
                localStorage.setItem( 'user', JSON.stringify( response.data.user ) );
                localStorage.setItem( 'token', response.data.token );

                // redirect to dashboard.
                router.visit('/books');
            } else {
                errorMessages.value = response.data.error;
            }
            
        } catch (error) {
            // Handle error response.
            console.error( 'TWBM Internal error:', error );
        } finally {
            // Reset loading state.
            isLoading.value = false;
        }
    };

</script>

<template>
    <div class="flex items-center justify-center min-h-screen bg-gray-900 ">
        <div class="w-full max-w-md p-8 bg-white rounded shadow-md vue_inertia_bg_color">
            <h1 class="mb-6 text-2xl font-bold text-center vue_inertia_color">Login</h1>
            <form @submit.prevent="login_method" class="space-y-4 ">
                <div>
                    <label for="email" class="block mb-2 text-sm font-medium vue_inertia_color">Email:</label>
                    <input 
                        type="email" 
                        id="email" 
                        v-model="email" 
                        required 
                        class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                </div>
                <div>
                    <label for="password" class="block mb-2 text-sm font-medium vue_inertia_color">Password:</label>
                    <input 
                        type="password" 
                        id="password" 
                        v-model="password" 
                        required 
                        class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                </div>
                <button 
                    type="submit" 
                    :disabled="isLoading" 
                    class="w-full px-4 py-2 text-white bg-orange-500 rounded hover:bg-blue-600 disabled:opacity-50"
                >
                    Login
                </button>
                <p v-if="errorMessages" class="mt-2 text-sm text-red-500">{{ errorMessages }}</p>
            </form>
            <div class="mt-4 text-center">
                <Link href="/" class="vue_inertia_color hover:underline ">Go to Welcome Page</Link>
            </div>
        </div>
    </div>
</template>