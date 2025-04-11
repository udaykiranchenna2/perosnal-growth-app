<template>
  <AppLayout title="Generated Tweets">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Generated Tweets
        </h2>
        <Link :href="route('x-post.index')" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
          Back to Settings
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6">
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Content
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Status
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Created At
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Actions
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="tweet in tweets" :key="tweet.id">
                    <td class="px-6 py-4 whitespace-pre-wrap">
                      <div class="text-sm text-gray-900">{{ tweet.content }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span :class="[
                        tweet.is_sent ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800',
                        'px-2 inline-flex text-xs leading-5 font-semibold rounded-full'
                      ]">
                        {{ tweet.is_sent ? 'Sent' : 'Pending' }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ formatDate(tweet.created_at) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                      <button
                        v-if="!tweet.is_sent"
                        @click="sendTweet(tweet)"
                        class="text-indigo-600 hover:text-indigo-900 mr-4"
                        :disabled="isSending"
                      >
                        <span v-if="isSending">Sending...</span>
                        <span v-else>Send</span>
                      </button>
                      <button
                        @click="deleteTweet(tweet)"
                        class="text-red-600 hover:text-red-900"
                        :disabled="isDeleting"
                      >
                        <span v-if="isDeleting">Deleting...</span>
                        <span v-else>Delete</span>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

import { router } from '@inertiajs/vue3';

const props = defineProps({
  tweets: {
    type: Array,
    required: true
  }
});

const isSending = ref(false);
const isDeleting = ref(false);

const formatDate = (date) => {
  return new Date(date).toLocaleString();
};

const sendTweet = async (tweet) => {
  if (isSending.value) return;

  isSending.value = true;
  try {
    await router.post(route('x-post.tweets.mark-sent', tweet.id));
  } finally {
    isSending.value = false;
  }
};

const deleteTweet = async (tweet) => {
  if (isDeleting.value) return;

  if (confirm('Are you sure you want to delete this tweet?')) {
    isDeleting.value = true;
    try {
      await router.delete(route('x-post.tweets.destroy', tweet.id));
    } finally {
      isDeleting.value = false;
    }
  }
};
</script>
