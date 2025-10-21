<script setup>
    import { ref } from 'vue';
    import { Link, router } from '@inertiajs/vue3';
    import axios from 'axios';

    const title = ref('');
    const author = ref('');
    const description = ref('');
    const publication_date = ref('');
    const category = ref('');
    const isbn = ref('');
    const page_count = ref('');
    const cover_image = ref(null);
    const publisher = ref('');
    const isLoading = ref(false);
    const errorMessages = ref([]);
    const notificationSuccess = ref(null);

    // lIst of books from the api server
    const props = defineProps({
        miscellaneous: Array,
        permission: Array,
    });

    // Handle file input change
    const onFileChange = (event) => {
        // console.log('File:', event.target.files[0]);
        const file = event.target.files[0];
        cover_image.value = file; // Store the selected file in the cover_image variable
    };

    const create_book = async () => {
        isLoading.value = true;

        try {
            // Validate search field
            if ( ! title.value || ! author.value || ! description.value || ! publication_date.value || ! category.value || ! isbn.value || ! page_count.value ) {
                return;
            }

            // Sending the ajax request to the api server
            const response = await axios.post('/api/v1/auth/book_create',
                {
                    title: title.value,
                    author: author.value,
                    description: description.value,
                    cover_image: cover_image.value,
                    publisher: publisher.value,
                    publication_date: publication_date.value,
                    category: category.value,
                    isbn: isbn.value,
                    page_count: page_count.value,
                }, {
                    headers: { Authorization: "Bearer " + localStorage.getItem('access_token'), 'Content-Type': 'multipart/form-data', },
                });

                if ( response.data.success ) {
                    notificationSuccess.value = response.data.success;

                    // Redirect to login page after 2 secnds.
                    setTimeout(() => {
                        router.visit('/books');
                    }, 2000 );

                }

            // capture the errors.
            if ( response.data.error ) {
                errorMessages.value = response.data.error;
            }

        } catch (error) {
            console.error( 'Server error, try again later:', error );
        } finally {
            isLoading.value = false;
        }
    };
</script>

<template>
    <div class="flex justify-center items-center min-h-screen text-black">
        <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-lg">
            <h1 class="text-3xl font-bold mb-6 text-center vue_inertia_color">Create a New Book</h1>
            <form @submit.prevent="create_book" v-if="permission" class="space-y-6">
                <div>
                    <label for="title" class="block text-sm font-medium mb-2 vue_inertia_color">Title:</label>
                    <input type="text" id="title" v-model="title" required class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>

                <div>
                    <label for="author" class="block text-sm font-medium mb-2 vue_inertia_color">Author:</label>
                    <select id="author" v-model="author" required class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="" disabled>Select an author</option>
                        <option v-for="author in miscellaneous.author" :key="author.id" :value="author.id">{{ author.name }}</option>
                    </select>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium mb-2 vue_inertia_color">Description:</label>
                    <textarea id="description" v-model="description" required class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>

                <div>
                    <label for="cover_image" class="block text-sm font-medium mb-2 vue_inertia_color">Cover Image:</label>
                    <input type="file" id="cover_image" @change="onFileChange" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>

                <div>
                    <label for="publisher" class="block text-sm font-medium mb-2 vue_inertia_color">Publisher:</label>
                    <select id="publisher" v-model="publisher" required class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="" disabled>Select a publisher</option>
                        <option v-for="publisher in miscellaneous.publisher" :key="publisher.id" :value="publisher.id">{{ publisher.name }}</option>
                    </select>
                </div>

                <div>
                    <label for="publication_date" class="block text-sm font-medium mb-2 vue_inertia_color">Publication Date:</label>
                    <input type="date" id="publication_date" v-model="publication_date" required class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>

                <div>
                    <label for="category" class="block text-sm font-medium mb-2 vue_inertia_color">Category:</label>
                    <select id="category" v-model="category" required class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="" disabled>Select a category</option>
                        <option v-for="category in miscellaneous.category" :key="category.id" :value="category.id">{{ category.name }}</option>
                    </select>
                </div>

                <div>
                    <label for="isbn" class="block text-sm font-medium mb-2 vue_inertia_color">ISBN:</label>
                    <input type="text" id="isbn" v-model="isbn" required class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>

                <div>
                    <label for="page_count" class="block text-sm font-medium mb-2 vue_inertia_color">Page Count:</label>
                    <input type="number" id="page_count" v-model="page_count" required class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>

                <div class="text-center">
                    <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">Create Book</button>
                    <Link :href="'/books'" class="px-4 py-2 bg-red-700 text-white font-semibold ml-4 rounded-lg shadow hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">Cancel</Link>
                </div>
            </form>

            <div v-if="errorMessages.length" class="error-messages mt-6">
                <ul class="list-disc list-inside text-red-500">
                    <li v-for="(error, index) in errorMessages" :key="index">{{ error }}</li>
                </ul>
            </div>

            <div v-if="notificationSuccess" class="success-message mt-6 text-center text-green-500">
                <p>{{ notificationSuccess }}</p>
            </div>
        </div>
    </div>
</template>

<style>
    .error-messages {
        color: red;
    }
</style>