<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-100 to-indigo-200 dark:from-gray-900 dark:to-gray-800 p-4 sm:p-6">
    <div class="max-w-7xl mx-auto">
      <div class="bg-white dark:bg-gray-900 shadow-2xl rounded-2xl p-6 sm:p-10 transition-all duration-300">
        <div class="flex justify-between items-center mb-8">
          <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Create X Post Profile</h1>
          <Link
            :href="route('x-post.index')"
            class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 dark:bg-gray-500 dark:hover:bg-gray-600 transition"
          >
            Back to Profiles
          </Link>
        </div>

        <form @submit.prevent="submitForm" class="space-y-6">
          <div>
            <label for="profile_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
              Profile Name
            </label>
            <input
              type="text"
              id="profile_name"
              v-model="form.profile_name"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white"
              required
            />
            <div v-if="form.errors.profile_name" class="text-red-500 text-sm mt-1">
              {{ form.errors.profile_name }}
            </div>
          </div>

          <div>
            <label for="context" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
              Context
            </label>
            <textarea
              id="context"
              v-model="form.context"
              rows="4"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white"
              required
            ></textarea>
            <div v-if="form.errors.context" class="text-red-500 text-sm mt-1">
              {{ form.errors.context }}
            </div>
          </div>

          <div>
            <label for="max_tweet_length" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
              Maximum Tweet Length
            </label>
            <input
              type="number"
              id="max_tweet_length"
              v-model="form.max_tweet_length"
              min="1"
              max="280"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white"
              required
            />
            <div v-if="form.errors.max_tweet_length" class="text-red-500 text-sm mt-1">
              {{ form.errors.max_tweet_length }}
            </div>
          </div>

          <div class="flex justify-end">
            <button
              type="submit"
              class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 transition"
              :disabled="form.processing"
            >
              Create Profile
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';

const form = useForm({
  profile_name: '',
  context: '',
  max_tweet_length: 280
});

const submitForm = () => {
  form.post(route('x-post.store'), {
    onSuccess: () => {
      form.reset();
    }
  });
};
</script> 