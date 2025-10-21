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
        book: Array,
        miscellaneous: Array,
        permission: Array,
    });

    // Mapping selected book data to the input fields.
    title.value = props.book.title;
    author.value = props.book.author_id;
    description.value = props.book.description;
    publisher.value = props.book.publisher_id;
    publication_date.value = props.book.publication_date;
    category.value = props.book.category_id;
    isbn.value = props.book.isbn;
    page_count.value = props.book.page_count;
    

    // Handle file input change
    const onFileChange = (event) => {
        // console.log('File:', event.target.files[0]);
        const file = event.target.files[0];
        cover_image.value = file; // Store the selected file in the cover_image variable
    };

    const edit_book = async () => {
        isLoading.value = true;

        try {
            // Validate search field
            if ( ! title.value || ! author.value || ! description.value || ! publication_date.value || ! category.value || ! isbn.value || ! page_count.value ) {
                return;
            }

            // Sending the ajax request to the api server
            const response = await axios.post('/api/v1/auth/book_edit',
                {
                    book_id: props.book.id,
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
            <h1 class="text-2xl font-bold mb-4 text-center vue_inertia_color">Edit a New Book <strong>{{ book.id }}</strong></h1>
            <form @submit.prevent="edit_book" v-if="permission" class="space-y-4">
                <div class="flex flex-col">
                    <label for="title" class="text-sm font-medium mb-1 vue_inertia_color">Title:</label>
                    <input type="text" id="title" v-model="title" required class="border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>

                <div class="flex flex-col">
                    <label for="author" class="text-sm font-medium mb-1 vue_inertia_color">Author:</label>
                    <select id="author" v-model="author" required class="border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="" disabled>Select an author</option>
                        <option v-for="author in miscellaneous.author" :key="author.id" :value="author.id">{{ author.name }}</option>
                    </select>
                </div>

                <div class="flex flex-col">
                    <label for="description" class="text-sm font-medium vue_inertia_color mb-1">Description:</label>
                    <textarea id="description" v-model="description" required class="border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>

                <div class="flex flex-col">
                    <label for="cover_image" class="text-sm font-medium mb-1 vue_inertia_color">Cover Image:</label>
                    <input type="file" id="cover_image" @change="onFileChange" class="border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>

                <div class="flex flex-col">
                    <label for="publisher" class="text-sm font-medium mb-1 vue_inertia_color">Publisher:</label>
                    <select id="publisher" v-model="publisher" required class="border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="" disabled>Select a publisher</option>
                        <option v-for="publisher in miscellaneous.publisher" :key="publisher.id" :value="publisher.id">{{ publisher.name }}</option>
                    </select>
                </div>

                <div class="flex flex-col">
                    <label for="publication_date" class="text-sm font-medium mb-1 vue_inertia_color">Publication Date:</label>
                    <input type="date" id="publication_date" v-model="publication_date" required class="border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>

                <div class="flex flex-col">
                    <label for="category" class="text-sm font-medium mb-1 vue_inertia_color">Category:</label>
                    <select id="category" v-model="category" required class="border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="" disabled>Select a category</option>
                        <option v-for="category in miscellaneous.category" :key="category.id" :value="category.id">{{ category.name }}</option>
                    </select>
                </div>

                <div class="flex flex-col">
                    <label for="isbn" class="text-sm font-medium mb-1 vue_inertia_color">ISBN:</label>
                    <input type="text" id="isbn" v-model="isbn" required class="border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>

                <div class="flex flex-col">
                    <label for="page_count" class="text-sm font-medium mb-1 vue_inertia_color">Page Count:</label>
                    <input type="number" id="page_count" v-model="page_count" required class="border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>

                <div class="m-auto">
                    <button type="submit" class="w-30 mr-1 bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">Edit Book</button>
                    <Link :href="'/books'" class="px-4 py-2 bg-red-700 text-white font-semibold ml-4 rounded-lg shadow hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">Cancel</Link>
                </div>
            </form>

            <!-- Display validation errors -->
            <div v-if="errorMessages.length" class="error-messages mt-4">
                <ul>
                    <li v-for="(error, index) in errorMessages" :key="index">{{ error }}</li>
                </ul>
            </div>

            <!-- Display success message -->
            <div v-if="notificationSuccess" class="success-message mt-4">
                <p class="text-green-600">{{ notificationSuccess }}</p>
            </div>
        </div>
    </div>
</template>

<style>
    .error-messages {
        color: red;
    }
</style>