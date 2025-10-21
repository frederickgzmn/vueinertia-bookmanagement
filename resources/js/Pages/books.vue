<script setup>
    import { Link, router } from '@inertiajs/vue3';
    import { ref } from 'vue';
    import axios from 'axios';
    
    // Token on every request to the api server.
    axios.interceptors.request.use((config) => {
        const token = localStorage.getItem('token');
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
    });

    const search_title = ref('');
    const search_results = ref([]);
    const search_done = ref(null);
    const book_list = ref([]);
    const isLoading = ref(false);
    const errorMessages = ref(null);
    const logged = ref(false);
    const sortColumn = ref('');
    const sortOrder = ref('asc');

    // lIst of books from the api server
    const props = defineProps({
        books: Array,
        auth: Object,
    });

    const sortTable = (column) => {
        if (sortColumn.value === column) {
            // Toggle sort order if the same column is clicked
            sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc';
        } else {
            // Set the new column and default to ascending order
            sortColumn.value = column;
            sortOrder.value = 'asc';
        }

        // Sort the book_list array
        book_list.value.sort((a, b) => {
            const valueA = a[column] ?? ''; // Handle null or undefined values
            const valueB = b[column] ?? '';

            // Check if the values are numbers
            if (!isNaN(valueA) && !isNaN(valueB)) {
                return sortOrder.value === 'asc' ? valueA - valueB : valueB - valueA;
            }

            // Otherwise, compare as strings
            const stringA = valueA.toString().toLowerCase();
            const stringB = valueB.toString().toLowerCase();

            if (stringA < stringB) return sortOrder.value === 'asc' ? -1 : 1;
            if (stringA > stringB) return sortOrder.value === 'asc' ? 1 : -1;
            return 0;
        });
    };

    // Initializing result load
    book_list.value = props.books;

    // registration request to the api server
    const search_books = async () => {
        isLoading.value = true;
        errorMessages.value = null;

        try {
            // Validate search field
            if ( ! search_title.value ) {
                return;
            }

            // Sending the ajax request to the api server
            const response = await axios.post('/api/v1/auth/book_search',
                {
                    name: search_title.value
                },
                {
                    headers: { Authorization: "Bearer " + localStorage.getItem('access_token'),
                }
            });

            // Mapping the result variables.
            search_results.value = response.data.result;
            search_done.value = true;

            // capture the errors.
            if ( response.data.error ) {
                errorMessages.value = response.data.error;
            }

            if ( search_results.value.length > 0 ) {
                book_list.value = search_results.value;
            } else {
                // All books
                book_list.value = props.books;
            }

        } catch (error) {
            console.error( 'Server error, try again later:', error );
        } finally {
            isLoading.value = false;
        }
    };

    const logout = async () => {
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        localStorage.clear();

        router.visit('/login');
    };

    const delete_book = async ( book_id ) => {
        isLoading.value = true;
        errorMessages.value = null;

        try {
            // Sending the ajax request to the api server
            const response = await axios.post('/api/v1/auth/book_delete',
                {
                    book_id: book_id,
                });

            // capture the errors.
            if ( response.data.error ) {
                errorMessages.value = response.data.error;
            }

            // If a name is assigned the registration was successful.
            if ( response.data.success ) {
                console.log('Book deleted:', response.data.success);
                router.visit('/books');
            }

        } catch (error) {
            console.error( 'Server error, try again later:', error );
        } finally {
            isLoading.value = false;
        }
    };

    const book_return = async ( book_id ) => {
        try {
            // Sending the ajax request to the api server
            const response = await axios.post('/api/v1/auth/book_return', {
                book_id: book_id,
            });

            // capture the errors.
            if ( response.data.error ) {
                errorMessages.value = response.data.error;
            }

            if ( response.data.success ) {
                router.reload();
            }

        } catch (error) {
            console.error( 'Server error, try again later:', error );
        }
    };

    const make_checkout_book = async ( book_id ) => {
        try {
            // Sending the ajax request to the api server
            const response = await axios.post('/api/v1/auth/book_checkout', {
                book_id: book_id,
            });

            // capture the errors.
            if ( response.data.error ) {
                errorMessages.value = response.data.error;
            }

            if ( response.data.success ) {
                router.reload();
            }

        } catch (error) {
            console.error( 'Server error, try again later:', error );
        }
    };
</script>

<template>
    <div>
        <h1 class="text-2xl font-bold mb-4 flex flex-col items-center mt-8">Search Books</h1>
        <form @submit.prevent="search_books" class="space-y-4 flex flex-col items-center">
            <div class="w-full max-w-md">
            <label for="search_title" class="block text-sm font-medium text-gray-700">Search Title:</label>
            <input 
                type="text" 
                id="search_title" 
                v-model="search_title" 
                required 
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-full shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"/>
            </div>
            <button type="submit" 
            class="px-4 py-2 bg-blue-500 text-white font-semibold rounded-full shadow hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
            Search </button>
        </form>

        <div v-if="search_results.length > 0 && search_results.length != 0" class="mt-4 text-green-600 flex flex-col items-center">
            <span>Search Results ( {{ search_results.length }} )</span>
        </div>

        <div v-if="search_results.length == 0 && search_done" class="mt-4 text-red-600 flex flex-col items-center">
            <span>Not found</span>
        </div>

        <div v-if="book_list.length > 0" class="mt-6">
            <h2 class="text-xl font-semibold mb-4">Book List</h2>
            <table class="min-w-full border-collapse border border-gray-300">
            <thead class="bg-gray-100">
                <tr>
                <th class="border border-gray-300 px-4 py-2 text-left">
                    <button @click="sortTable('title')" class="text-blue-500 vue_inertia_color hover:underline">Title</button>
                </th>
                <th class="border border-gray-300 px-4 py-2 text-left vue_inertia_alt_color">Publisher</th>
                <th class="border border-gray-300 px-4 py-2 text-left">
                    <button @click="sortTable('author_name')" class="text-blue-500 hover:underline vue_inertia_color">Author</button>
                </th>
                <th class="border border-gray-300 px-4 py-2 text-left">
                    <button @click="sortTable('status')" class="text-blue-500 hover:underline vue_inertia_color">Status</button>
                </th>
                <th class="border border-gray-300 px-4 py-2 text-left vue_inertia_color">Category</th>
                <th class="border border-gray-300 px-4 py-2 text-left vue_inertia_color">Cover Image</th>
                <th class="border border-gray-300 px-4 py-2 text-left vue_inertia_color">Average Rating</th>
                <th class="border border-gray-300 px-4 py-2 text-left vue_inertia_color">Action</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="book in book_list" :key="book.id" class="hover:bg-orange-800">
                <td class="border border-gray-300 px-4 py-2">{{ book.title }}</td>
                <td class="border border-gray-300 px-4 py-2">{{ book.publisher_name }}</td>
                <td class="border border-gray-300 px-4 py-2">{{ book.author_name }}</td>
                <td class="border border-gray-300 px-4 py-2">{{ book.status }}</td>
                <td class="border border-gray-300 px-4 py-2">{{ book.category_name }}</td>
                <td class="border border-gray-300 px-4 py-2">
                    <img :src="'/storage/' + book.cover_image" v-if="book.cover_image" alt="Cover Image" class="w-12 h-12 object-cover" />
                </td>
                <td class="border border-gray-300 px-4 py-2">{{ book.average_rating }}</td>
                <td class="border border-gray-300 px-4 py-2 space-x-2">
                    <Link :href="'/books/' + book.id + '/show'" class="text-blue-500 hover:underline">View</Link>
                    <Link v-if="auth.user.permissions.original.includes(2)" :href="'/books/' + book.id + '/edit'" class="text-green-500 hover:underline">Edit</Link>
                    <button @click="delete_book(book.id)" v-if="auth.user.permissions.original.includes(3)" class="text-red-500 hover:underline">Delete</button>
                </td>
                </tr>
            </tbody>
            </table>
        </div>

        <div v-if="book_list.length > 0" class="mt-6">
            <h2 class="text-xl font-semibold mb-4 vue_inertia_color">Books Available and Borrowed with Due date</h2>
            <table class="min-w-full border-collapse border border-gray-300">
            <thead class="bg-gray-100">
                <tr>
                <th class="border border-gray-300 px-4 py-2 text-left">
                    <p class="text-blue-500 vue_inertia_color hover:underline">Title</p>
                </th>
                <th class="border border-gray-300 px-4 py-2 text-left vue_inertia_color">Publisher</th>
                <th class="border border-gray-300 px-4 py-2 text-left">
                    <p class="text-blue-500 vue_inertia_color hover:underline">Author</p>
                </th>
                <th class="border border-gray-300 px-4 py-2 text-left">
                    <p class="text-blue-500 vue_inertia_color hover:underline">Status</p>
                </th>
                <th class="border border-gray-300 px-4 py-2 text-left vue_inertia_color">Category</th>
                <th class="border border-gray-300 px-4 py-2 text-left vue_inertia_color">Cover Image</th>
                <th class="border border-gray-300 px-4 py-2 text-left vue_inertia_color">Average Rating</th>
                <th class="border border-gray-300 px-4 py-2 text-left vue_inertia_color">Due days</th>
                <th class="border border-gray-300 px-4 py-2 text-left vue_inertia_color">Action</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="book in props.books" :key="book.id" class="hover:bg-orange-800">
                <td class="border border-gray-300 px-4 py-2">{{ book.title }}</td>
                <td class="border border-gray-300 px-4 py-2">{{ book.publisher_name }}</td>
                <td class="border border-gray-300 px-4 py-2">{{ book.author_name }}</td>
                <td class="border border-gray-300 px-4 py-2">{{ book.status }}</td>
                <td class="border border-gray-300 px-4 py-2">{{ book.category_name }}</td>
                <td class="border border-gray-300 px-4 py-2">
                    <img :src="'/storage/' + book.cover_image" v-if="book.cover_image" alt="Cover Image" class="w-12 h-12 object-cover" />
                </td>
                <td class="border border-gray-300 px-4 py-2">{{ book.average_rating }}</td>
                <td class="border border-gray-300 px-4 py-2">{{ book.due_days }}</td>
                <td class="border border-gray-300 px-4 py-2">
                    <button @click="make_checkout_book(book.id)" v-if="book.status != 'borrowed'" class="px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">Mark Checked Out</button>
                    <button @click="book_return(book.id)" v-if="auth.user.permissions.original.includes(2)" class="px-2 py-1 ml-8 bg-blue-500 text-white rounded hover:bg-blue-600">Mark As Returned</button>
                    <p v-if="book.status == 'borrowed'" class="text-red-500">Unavailable(Check the due days)</p>
                </td>
                </tr>
            </tbody>
            </table>
        </div>

        <div class="mt-4">
            <!-- add a new book is the ID 1 -->
            <Link :href="'/books/create'" v-if="auth.user.permissions.original.includes(1)" class="px-4 py-2 bg-blue-500 text-white font-semibold rounded-full shadow hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">Add a new book</Link>
            <Link :href="'/logout'" @click="logout()" class="px-4 py-2 ml-8 bg-red-500 text-white font-semibold rounded-full shadow hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">Log out</Link>
        </div>


    </div>
</template>