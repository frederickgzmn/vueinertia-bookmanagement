<script setup>
    import { Link, router } from '@inertiajs/vue3';
    import { ref } from 'vue';
    import axios from 'axios';
    import Login from './login.vue';

    const name = ref('');
    const email = ref('');
    const password = ref('');
    const role = ref('');
    const password_confirmation = ref('');
    const errorMessages = ref(null);
    const notificationSuccess = ref(null);
    const isLoading = ref(false);

    // lIst of roles from the api server
    defineProps({
        roles: Array,
    });

    // registration request to the api server
    const register_method = async () => {
        isLoading.value = true;
        errorMessages.value = null;

        try {
            // Validate input fields
            if ( !name.value || !email.value || !password.value || !password_confirmation.value ) {
                errorMessages.value = { general: ['All fields are required'] };
                isLoading.value = false;
                return;
            }

            // The passwords aren't matching
            if ( password.value !== password_confirmation.value ) {
                errorMessages.value = { password: 'Passwords do not match' };
                isLoading.value = false;
                return;
            }

            // Sending the ajax request to the api server
            const response = await axios.post('/api/v1/auth/register', {
                name: name.value,
                email: email.value,
                password: password.value,
                password_confirmation : password_confirmation.value,
                role_id: role.value,
            });

            // capture the errors.
            if ( response.data.error ) {
                errorMessages.value = response.data.error;
            }

            // If a name is assigned the registration was successful.
            if ( response.data.name ) {
                console.log('Registration successful:', response.data.name);
                notificationSuccess.value = response.data.name + ' registered successfully.';
                isLoading.value = false;

                // Redirect to login page after 2 secnds.
                setTimeout(() => {
                    router.visit('/login');
                }, 2000 );
            }

        } catch (error) {
            console.error('Registration failed:', error);
        } finally {
            isLoading.value = false;
        }
    };
</script>

<template>  
    <div class="p-6 max-w-md mx-auto bg-white rounded-lg shadow-md mt-10">
        <h1 class="text-2xl font-bold mb-4 vue_inertia_color">Registration</h1>
        <form @submit.prevent="register_method" class="space-y-4">
            <div>
                <label for="name" class="block text-sm font-medium text-black">Name:</label>
                <input type="text" id="name" v-model="name" required 
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-black" />
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-black">Email:</label>
                <input type="email" id="email" v-model="email" required 
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-black" />
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-black">Password:</label>
                <input type="password" id="password" v-model="password" required 
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-black" />
            </div>
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-black">Confirm Password:</label>
                <input type="password" id="password_confirmation" v-model="password_confirmation" required 
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-black" />
            </div>
            <div>
                <label for="role" class="block text-sm font-medium text-black">Role:</label>
                <select id="role" v-model="role" required 
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-black">
                    <option value="" selected="true">Select a role</option>
                    <option v-for="role in roles" :key="role.id" :value="role.id">{{ role.name }}</option>
                </select>
            </div>
            <button type="submit" :disabled="isLoading" 
                class="w-full py-2 px-4 rounded-md shadow-md bg-gray-500 font-bold vue_inertia_color disabled:opacity-50">
                Register
            </button>
        </form>

        <!-- Display error messages -->
        <div v-if="errorMessages" class="mt-4 p-4 bg-red-100 text-red-700 rounded-md">
            <p v-for="(message, index) in errorMessages" :key="index">{{ message }}</p>
        </div>

        <!-- Display success message -->
        <div v-if="notificationSuccess" class="mt-4 p-4 bg-green-100 text-green-700 rounded-md">
            <p>{{ notificationSuccess }}</p>
        </div>

        <!-- Link to login page -->
        <div class="mt-4 text-center">
            <Link href="/login" class="vue_inertia_color hover:underline">Already have an account? Login here</Link>
        </div>

        <div class="mt-4 text-center">
            <Link href="/" class="bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">Cancel</Link>
        </div>

    </div>
</template>