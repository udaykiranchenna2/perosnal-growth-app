<template>
    <AppLayout title="Generated Tweets">
      <template #header>
        <div class="flex justify-between items-center">
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">Generated Tweets</h2>
          <Link :href="route('x-post.index')" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Back to Settings
          </Link>
        </div>
      </template>

      <div class="py-8 max-w-7xl mx-auto">
        <div class="bg-white shadow-lg rounded-2xl p-4 overflow-x-auto">
          <Table>
            <TableCaption>List of your generated tweets</TableCaption>
            <TableHeader>
              <TableRow>
                <TableHead>Content</TableHead>
                <TableHead>Community</TableHead>
                <TableHead>Status</TableHead>
                <TableHead>Created At</TableHead>
                <TableHead class="text-right">Actions</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow v-for="tweet in tweets.data" :key="tweet.id">
                <TableCell>{{ tweet.content }}</TableCell>
                <TableCell>
                  <span v-if="tweet.community" class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 border-transparent bg-blue-100 text-blue-800">
                    {{ tweet.community.name }}
                  </span>
                  <span v-else class="text-sm text-muted-foreground">No Community</span>
                </TableCell>
                <TableCell>
                  <span :class="[
                    tweet.is_sent ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800',
                    'px-2 inline-flex text-xs leading-5 font-semibold rounded-full'
                  ]">
                    {{ tweet.is_sent ? 'Sent' : 'Pending' }}
                  </span>
                </TableCell>
                <TableCell>{{ formatDate(tweet.created_at) }}</TableCell>
                <TableCell class="text-right space-x-2">
                  <button
                    v-if="!tweet.is_sent"
                    @click="() => sendTweet(tweet)"
                    class="text-indigo-600 hover:text-indigo-900 text-sm"
                    :disabled="isSending === tweet.id"
                  >
                    <span v-if="isSending === tweet.id">Sending...</span>
                    <span v-else>Send</span>
                  </button>
                  <button
                    @click="() => deleteTweet(tweet)"
                    class="text-red-600 hover:text-red-900 text-sm"
                    :disabled="isDeleting === tweet.id"
                  >
                    <span v-if="isDeleting === tweet.id">Deleting...</span>
                    <span v-else>Delete</span>
                  </button>
                </TableCell>
              </TableRow>
            </TableBody>
            <TableFooter>
              <div class="flex justify-end mt-6 space-x-2">
                <Link
                  v-for="(link, index) in tweets.links || []"
                  :key="index"
                  :href="link.url"
                  preserve-scroll
                  preserve-state
                  class="px-4 py-2 text-sm rounded-lg flex items-center justify-center transition-all duration-200 ease-in-out"
                  :class="[
                    link.active ? 'bg-blue-600 text-white shadow-lg' : 'bg-gray-200 hover:bg-blue-100 text-gray-700',
                    'font-semibold'
                  ]"
                  v-html="link.label"
                />
              </div>
            </TableFooter>
          </Table>
        </div>
      </div>
    </AppLayout>
  </template>

  <script setup>
  import { ref, computed } from 'vue';
  import { Link, router } from '@inertiajs/vue3';
  import AppLayout from '@/layouts/AppLayout.vue';
  import { useToast } from 'vue-toastification';

  import Table from '@/components/ui/table/Table.vue';
  import TableBody from '@/components/ui/table/TableBody.vue';
  import TableCaption from '@/components/ui/table/TableCaption.vue';
  import TableCell from '@/components/ui/table/TableCell.vue';
  import TableHead from '@/components/ui/table/TableHead.vue';
  import TableHeader from '@/components/ui/table/TableHeader.vue';
  import TableRow from '@/components/ui/table/TableRow.vue';
  import TableFooter from '@/components/ui/table/TableFooter.vue';

  const props = defineProps({
    tweets: Array
  });

  const toast = useToast();
  const isSending = ref(null);
  const isDeleting = ref(null);

  const formatDate = (date) => new Date(date).toLocaleString();

  const sendTweet = async (tweet) => {
    if (isSending.value) return;
    isSending.value = tweet.id;
    try {
      await router.post(route('x-post.tweets.mark-sent', tweet.id), {}, {
        onSuccess: () => toast.success('Tweet sent successfully', { timeout: 2000 })
      });
    } finally {
      isSending.value = null;
    }
  };

  const deleteTweet = async (tweet) => {
    if (isDeleting.value) return;
    if (!confirm('Are you sure you want to delete this tweet?')) return;
    isDeleting.value = tweet.id;
    try {
      await router.delete(route('x-post.tweets.destroy', tweet.id), {
        onSuccess: () => toast.success('Tweet deleted successfully', { timeout: 2000 })
      });
    } finally {
      isDeleting.value = null;
    }
  };

  // Pagination
  const page = ref(1);
  const perPage = 10;
  </script>
