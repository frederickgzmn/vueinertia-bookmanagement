<script setup>
    import { Link, router } from '@inertiajs/vue3';
    import { ref } from 'vue';
    import axios from 'axios';

    const book_title = ref('');
    const book_author = ref('');
    const book_description = ref('');
    const book_publication_date = ref('');
    const book_category = ref('');
    const book_isbn = ref('');
    const book_page_count = ref('');
    const book_cover_image = ref(null);
    const book_publisher = ref('');

    // lIst of books from the api server
    const props = defineProps({
        book: Array,
        customer_reviews: Array,
        auth: Object,
    });

    book_title.value = props.book.title;
    book_author.value = props.book.author_id;
    book_description.value = props.book.description;
    book_publisher.value = props.book.publisher_id;
    book_publication_date.value = props.book.publication_date;
    book_category.value = props.book.category_id;
    book_isbn.value = props.book.isbn;
    book_page_count.value = props.book.page_count;
    book_cover_image.value = '/storage/' + props.book.cover_image;

    // Review variables.
    const review_title = ref('');
    const review_comment = ref('');
    const review_rating = ref('');

    const submit_review = async () => {
        try {
            // Validate input fields
            if (!review_title.value || !review_comment.value || !review_rating.value) {
                return;
            }

            // Sending the ajax request to the api server
            const response = await axios.post('/api/v1/auth/review_create', {
                book_id: props.book.id,
                title: review_title.value,
                comment: review_comment.value,
                rating_stars: review_rating.value,
            });

            if (response.data.message) {
                // Reset the review form fields
                review_title.value = '';
                review_comment.value = '';
                review_rating.value = '';

                router.reload();
            }
        } catch (error) {
            console.error('Error submitting review:', error);
        }
    };
</script>
<template>
    <div class="container mx-auto p-4">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-black">Book Details</h1>
        </div>
        <div class="bg-white shadow-md rounded-lg p-6 mb-8 flex flex-wrap items-start">
            <div class="w-full md:w-1/2 pr-4">
            <h2 class="text-2xl font-semibold mb-4 text-black">{{ book_title }}</h2>
            <p class="mb-2 text-gray-700"><strong>Author:</strong> {{ book_author }}</p>
            <p class="mb-2 text-gray-700"><strong>Description:</strong> {{ book_description }}</p>
            <p class="mb-2 text-gray-700"><strong>Publication Date:</strong> {{ book_publication_date }}</p>
            <p class="mb-2 text-gray-700"><strong>Category:</strong> {{ book_category }}</p>
            <p class="mb-2 text-gray-700"><strong>ISBN:</strong> {{ book_isbn }}</p>
            <p class="mb-2 text-gray-700"><strong>Page Count:</strong> {{ book_page_count }}</p>
            </div>
            <div class="w-full md:w-1/2 flex justify-center">
            <img class="w-full max-w-xs rounded-lg" :src="book_cover_image" alt="Book Cover" />
            </div>
        </div>
        <div class="bg-white shadow-md rounded-lg p-6 mb-8" v-if="auth.user.permissions.original.includes(5)">
            <h2 class="text-2xl font-semibold mb-4 text-black">Customer Reviews</h2>
            <ul v-if="customer_reviews.length > 0" class="space-y-4">
                <li v-for="review in customer_reviews" :key="review.id" class="border-b pb-4">
                    <p class="font-semibold text-black">{{ review.title }} by {{ review.user_name }}</p>
                    <p class="text-gray-600">{{ review.comment }}</p>
                    <p class="text-gray-700"><strong>Rating:</strong> {{ review.rating_stars }}</p>
                </li>
            </ul>
            <p v-else class="text-gray-500">No reviews available.</p>
        </div>
        <div class="bg-white shadow-md rounded-lg p-6 mb-8">
            <h2 class="text-2xl font-semibold mb-4 text-black">Write a Review</h2>
            <form @submit.prevent="submit_review" class="space-y-4">
                <div>
                    <label for="review_title" class="block font-medium mb-1 text-gray-700">Title:</label>
                    <input type="text" id="review_title" v-model="review_title" required class="w-full border rounded-lg px-3 py-2 text-gray-700" />
                </div>
                <div>
                    <label for="review_comment" class="block font-medium mb-1 text-gray-700">Comment:</label>
                    <textarea id="review_comment" v-model="review_comment" required class="w-full border rounded-lg px-3 py-2 text-gray-700"></textarea>
                </div>
                <div>
                    <label for="review_rating" class="block font-medium mb-1 text-gray-700">Rating:</label>
                    <select id="review_rating" v-model="review_rating" required class="w-full border rounded-lg px-3 py-2 text-gray-700">
                        <option value="" disabled>Select a rating</option>
                        <option v-for="rating in 5" :key="rating" :value="rating">{{ rating }}</option>
                    </select>
                </div>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">Submit Review</button>
            </form>
        </div>
        <div class="text-center">
            <Link v-if="auth.user.permissions.original.includes(2)" :href="'/books/' + book.id + '/edit'" class="mr-4 bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">Edit</Link>
            <Link href="/books" class="bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">Back to Book List</Link>
        </div>
    </div>
</template>