<template>
  <AppLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">X Post Dashboard</h2>
        <div class="flex space-x-2">
          <Link 
            :href="route('x-post.settings')" 
            class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-secondary text-secondary-foreground hover:bg-secondary/80 h-10 px-4 py-2"
          >
            ⚙️ Settings
          </Link>
        </div>
      </div>
    </template>

    <div class="py-6">
      <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
        <div class="space-y-6">
            
            <!-- Tweet Generation Card -->
            <div class="bg-card text-card-foreground shadow-sm rounded-lg border mb-6">
              <div class="p-6">
                <h3 class="text-xl font-bold mb-6 flex items-center">
                  🚀 Generate X Posts
                  <span v-if="isGenerating" class="ml-3 text-sm text-blue-600 animate-pulse">Generating...</span>
                </h3>
                
                <form @submit.prevent="generateTweet" class="space-y-6">
                  
                  <!-- Main Input Row -->
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                      <label class="text-sm font-medium">Context</label>
                      <select
                        v-model="generateForm.context_id"
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                      >
                        <option v-for="context in settings.contexts" :key="context.id" :value="context.id">
                          {{ context.name }}
                        </option>
                      </select>
                    </div>
                    
                    <div class="space-y-2">
                      <label class="text-sm font-medium">Community (Optional)</label>
                      <select
                        v-model="generateForm.community_id"
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                      >
                        <option value="">No Community</option>
                        <option v-for="community in activeCommunities" :key="community.id" :value="community.id">
                          {{ community.name }}
                        </option>
                      </select>
                    </div>
                  </div>

                  <!-- Prompt Input -->
                  <div class="space-y-2">
                    <label class="text-sm font-medium">What would you like to tweet about?</label>
                    <textarea
                      v-model="generateForm.instructions"
                      rows="3"
                      placeholder="e.g., Share a productivity tip for developers, or ask about AI legal challenges..."
                      class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                    ></textarea>
                    <div class="text-xs text-muted-foreground">
                      Character count: {{ generateForm.instructions.length }}
                    </div>
                  </div>

                  <!-- Advanced Options -->
                  <div class="border rounded-lg p-4 bg-muted/20">
                    <h4 class="text-sm font-semibold mb-3">🎛️ Generation Options</h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                      
                      <!-- Include Hashtags -->
                      <div class="flex items-center space-x-2">
                        <input
                          type="checkbox"
                          id="include_hashtags"
                          v-model="generateForm.include_hashtags"
                          class="h-4 w-4 rounded border-gray-300"
                        >
                        <label for="include_hashtags" class="text-sm font-medium">Include Hashtags</label>
                      </div>

                      <!-- Number of Variations -->
                      <div class="space-y-1">
                        <label class="text-sm font-medium">Variations</label>
                        <select
                          v-model="generateForm.variations"
                          class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm"
                        >
                          <option :value="1">1 Tweet</option>
                          <option :value="2">2 Tweets</option>
                          <option :value="3">3 Tweets</option>
                          <option :value="5">5 Tweets</option>
                        </select>
                      </div>

                      <!-- Tone -->
                      <div class="space-y-1">
                        <label class="text-sm font-medium">Tone</label>
                        <select
                          v-model="generateForm.tone"
                          class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm"
                        >
                          <option value="professional">Professional</option>
                          <option value="casual">Casual</option>
                          <option value="engaging">Engaging</option>
                          <option value="educational">Educational</option>
                        </select>
                      </div>

                      <!-- Max Emojis -->
                      <div class="space-y-1">
                        <label class="text-sm font-medium">Max Emojis 😊</label>
                        <select
                          v-model="generateForm.max_emojis"
                          class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm"
                        >
                          <option :value="0">None (0)</option>
                          <option :value="1">Minimal (1)</option>
                          <option :value="2">Few (2)</option>
                          <option :value="3">Some (3)</option>
                          <option :value="5">Many (5+)</option>
                        </select>
                      </div>

                      <!-- Max Lines -->
                      <div class="space-y-1">
                        <label class="text-sm font-medium">Max Lines 📝</label>
                        <select
                          v-model="generateForm.max_lines"
                          class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm"
                        >
                          <option :value="1">Single (1)</option>
                          <option :value="2">Two (2)</option>
                          <option :value="3">Three (3)</option>
                          <option :value="4">Four (4)</option>
                          <option :value="5">Five (5)</option>
                        </select>
                      </div>

                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                      
                      <!-- Writing Style -->
                      <div class="space-y-1">
                        <label class="text-sm font-medium">Writing Style</label>
                        <select
                          v-model="generateForm.writing_style"
                          class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm"
                        >
                          <option value="normal">Normal</option>
                          <option value="thread">Thread-style</option>
                          <option value="question">Question format</option>
                          <option value="list">List format</option>
                          <option value="story">Story/anecdote</option>
                        </select>
                      </div>

                      <!-- Call to Action -->
                      <div class="space-y-1">
                        <label class="text-sm font-medium">Call to Action</label>
                        <select
                          v-model="generateForm.cta_type"
                          class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm"
                        >
                          <option value="none">None</option>
                          <option value="engage">Engage (like/comment)</option>
                          <option value="share">Share experiences</option>
                          <option value="follow">Follow for more</option>
                          <option value="question">Ask question</option>
                        </select>
                      </div>

                    </div>
                  </div>

                  <!-- Generate Button -->
                  <button
                    type="submit"
                    :disabled="isGenerating || !generateForm.instructions.trim()"
                    class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white font-bold py-3 px-6 rounded-lg text-lg transition-colors"
                  >
                    <span v-if="isGenerating" class="flex items-center justify-center">
                      <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                      </svg>
                      Generating {{ generateForm.variations }} Tweet{{ generateForm.variations > 1 ? 's' : '' }}...
                    </span>
                    <span v-else>
                      🚀 Generate {{ generateForm.variations }} Tweet{{ generateForm.variations > 1 ? 's' : '' }}
                    </span>
                  </button>
                </form>
              </div>
            </div>

            <!-- Live Tweets List -->
            <div class="bg-card text-card-foreground shadow-sm rounded-lg border">
              <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                  <h3 class="text-lg font-bold">📝 Your Generated Tweets</h3>
                  <span class="text-sm text-muted-foreground">{{ tweets.data?.length || 0 }} tweets</span>
                </div>
                
                <div class="space-y-4 max-h-96 overflow-y-auto">
                  <div v-for="tweet in tweets.data" :key="tweet.id" class="border rounded-lg p-4 hover:bg-accent/20 transition-colors">
                    <div class="flex justify-between items-start mb-2">
                      <div class="flex items-center space-x-2">
                        <span v-if="tweet.community" class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                          {{ tweet.community.name }}
                        </span>
                        <span :class="[
                          tweet.is_sent ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800',
                          'px-2 py-1 text-xs rounded-full'
                        ]">
                          {{ tweet.is_sent ? 'Sent' : 'Draft' }}
                        </span>
                      </div>
                      <span class="text-xs text-muted-foreground">{{ formatDate(tweet.created_at) }}</span>
                    </div>
                    
                    <p class="text-sm mb-3">{{ tweet.content }}</p>
                    
                    <div class="flex justify-between items-center">
                      <div class="text-xs text-muted-foreground">
                        {{ tweet.content.length }} characters
                      </div>
                      <div class="flex flex-wrap gap-2">
                        <!-- Copy Tweet Button -->
                        <button
                          @click="copyTweet(tweet)"
                          class="text-xs bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded transition-colors"
                          title="Copy tweet to clipboard"
                        >
                          📋 Copy
                        </button>
                        
                        <!-- Edit Tweet Button -->
                        <button
                          @click="editTweet(tweet)"
                          class="text-xs bg-purple-600 hover:bg-purple-700 text-white px-3 py-1 rounded transition-colors"
                          title="Edit tweet content"
                        >
                          ✏️ Edit
                        </button>
                        
                        <!-- Status Toggle Button -->
                        <button
                          @click="toggleTweetStatus(tweet)"
                          :disabled="isToggling === tweet.id"
                          :class="[
                            tweet.is_sent 
                              ? 'bg-orange-600 hover:bg-orange-700' 
                              : 'bg-green-600 hover:bg-green-700',
                            'text-xs text-white px-3 py-1 rounded transition-colors'
                          ]"
                          :title="tweet.is_sent ? 'Mark as Draft' : 'Mark as Sent'"
                        >
                          {{ isToggling === tweet.id 
                            ? 'Updating...' 
                            : tweet.is_sent 
                              ? '📝 Mark Draft' 
                              : '🚀 Mark Sent' 
                          }}
                        </button>
                        
                        <!-- Auto Send Button (if Twitter API is configured) -->
                        <button
                          v-if="!tweet.is_sent"
                          @click="sendTweet(tweet)"
                          :disabled="isSending === tweet.id"
                          class="text-xs bg-gray-600 hover:bg-gray-700 text-white px-3 py-1 rounded transition-colors"
                          title="Send via Twitter API (if configured)"
                        >
                          {{ isSending === tweet.id ? 'Sending...' : '🔗 API Send' }}
                        </button>
                        
                        <!-- Delete Button -->
                        <button
                          @click="deleteTweet(tweet)"
                          :disabled="isDeleting === tweet.id"
                          class="text-xs bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded transition-colors"
                          title="Delete tweet"
                        >
                          {{ isDeleting === tweet.id ? 'Deleting...' : '🗑️ Delete' }}
                        </button>
                      </div>
                    </div>
                  </div>

                  <div v-if="!tweets.data?.length" class="text-center py-8 text-muted-foreground">
                    <p>No tweets generated yet.</p>
                    <p class="text-sm">Generate your first tweet above! 👆</p>
                  </div>
                </div>

                <!-- Pagination -->
                <div v-if="tweets.data?.length" class="flex justify-between items-center mt-4 pt-4 border-t border-border">
                  <div class="text-sm text-muted-foreground">
                    Showing {{ tweets.from || 0 }} to {{ tweets.to || 0 }} of {{ tweets.total || 0 }} tweets
                  </div>
                  <div class="flex space-x-1">
                    <Link
                      v-for="(link, index) in tweets.links || []"
                      :key="index"
                      :href="link.url"
                      preserve-scroll
                      preserve-state
                      class="px-3 py-2 text-sm rounded-md flex items-center justify-center transition-all duration-200 ease-in-out"
                      :class="[
                        link.active 
                          ? 'bg-blue-600 text-white shadow-md' 
                          : link.url 
                            ? 'bg-gray-100 hover:bg-blue-100 text-gray-700 hover:text-blue-700' 
                            : 'bg-gray-50 text-gray-400 cursor-not-allowed',
                        'font-medium'
                      ]"
                      v-html="link.label"
                      :disabled="!link.url"
                    />
                  </div>
                </div>
              </div>
            </div>

        </div>
      </div>
    </div>

    <!-- Edit Tweet Modal -->
    <div v-if="showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-bold">✏️ Edit Tweet</h3>
          <button @click="cancelEdit" class="text-gray-500 hover:text-gray-700">
            <span class="text-xl">×</span>
          </button>
        </div>
        
        <div class="space-y-4">
          <!-- Tweet Content -->
          <div>
            <label class="text-sm font-medium block mb-2">Tweet Content</label>
            <textarea
              v-model="editForm.content"
              rows="6"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
              placeholder="Write your tweet content..."
              maxlength="800"
            ></textarea>
            <div class="flex justify-between items-center mt-1">
              <span class="text-xs text-muted-foreground">
                {{ editForm.content.length }}/800 characters
              </span>
              <span v-if="editForm.content.length > 280" class="text-xs text-orange-600">
                Note: Standard X limit is 280 chars
              </span>
            </div>
          </div>

          <!-- Community Info (if applicable) -->
          <div v-if="editingTweet?.community" class="p-3 bg-blue-50 rounded-md">
            <div class="flex items-center space-x-2">
              <span class="text-sm font-medium text-blue-800">🌐 Community:</span>
              <span class="text-sm text-blue-700">{{ editingTweet.community.name }}</span>
            </div>
            <p class="text-xs text-blue-600 mt-1">{{ editingTweet.community.description }}</p>
          </div>

          <!-- Action Buttons -->
          <div class="flex space-x-3 pt-4">
            <button
              @click="saveEdit"
              :disabled="isSaving || !editForm.content.trim()"
              class="bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white px-6 py-2 rounded-md transition-colors"
            >
              {{ isSaving ? 'Saving...' : '💾 Save Changes' }}
            </button>
            
            <button
              @click="copyEditedTweet"
              :disabled="!editForm.content.trim()"
              class="bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white px-6 py-2 rounded-md transition-colors"
            >
              📋 Copy & Close
            </button>
            
            <button
              @click="cancelEdit"
              class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-md transition-colors"
            >
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>

  </AppLayout>
</template>

<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Link, useForm, router } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useToast } from "vue-toastification";

const toast = useToast();

const props = defineProps({
  settings: {
    type: Object,
    required: true
  },
  tweets: {
    type: Object,
    required: true
  }
});

// Generation State
const isGenerating = ref(false);
const isSending = ref(null);
const isDeleting = ref(null);
const isToggling = ref(null);
const isSaving = ref(false);
const pollingInterval = ref(null);

// Edit Modal State
const showEditModal = ref(false);
const editingTweet = ref(null);
const editForm = ref({ content: '' });

const getDefaultContext = () => {
  const defaultContext = props.settings.contexts?.find(context => context.is_default);
  return defaultContext?.id || props.settings.contexts?.[0]?.id || '';
};

const generateForm = useForm({
  context_id: getDefaultContext(),
  community_id: '',
  instructions: '',
  include_hashtags: true,
  variations: 1,
  tone: 'professional',
  max_emojis: 2,
  max_lines: 2,
  writing_style: 'normal',
  cta_type: 'none'
});

// Computed
const activeCommunities = computed(() => {
  return props.settings.communities?.filter(c => c.is_active) || [];
});

// Methods
const formatDate = (date) => new Date(date).toLocaleString();

const startPolling = () => {
  pollingInterval.value = setInterval(async () => {
    try {
      const response = await fetch(route('x-post.check-generation-status'));
      const data = await response.json();
      
      if (!data.is_queued) {
        clearInterval(pollingInterval.value);
        isGenerating.value = false;
        toast.success("Tweets generated successfully!", { timeout: 2000 });
        // Refresh the page to show new tweets
        router.reload({ only: ['tweets'] });
      }
    } catch (error) {
      console.error('Error checking generation status:', error);
      clearInterval(pollingInterval.value);
      isGenerating.value = false;
    }
  }, 3000);
};

const generateTweet = () => {
  if (isGenerating.value) return;

  isGenerating.value = true;
  generateForm.post(route('x-post.generate'), {
    onSuccess: () => {
      startPolling();
      // Clear the instructions for next use
      generateForm.instructions = '';
    },
    onError: () => {
      isGenerating.value = false;
      toast.error("Failed to generate tweet", { timeout: 2000 });
    }
  });
};

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

// Copy tweet to clipboard
const copyTweet = async (tweet) => {
  try {
    await navigator.clipboard.writeText(tweet.content);
    toast.success('Tweet copied to clipboard!', { timeout: 2000 });
  } catch (err) {
    // Fallback for older browsers
    const textArea = document.createElement('textarea');
    textArea.value = tweet.content;
    document.body.appendChild(textArea);
    textArea.select();
    document.execCommand('copy');
    document.body.removeChild(textArea);
    toast.success('Tweet copied to clipboard!', { timeout: 2000 });
  }
};

// Toggle tweet status (Draft ↔ Sent)
const toggleTweetStatus = async (tweet) => {
  if (isToggling.value) return;
  
  isToggling.value = tweet.id;
  try {
    await router.patch(route('x-post.tweets.toggle-status', tweet.id), {}, {
      onSuccess: () => {
        const newStatus = tweet.is_sent ? 'Draft' : 'Sent';
        toast.success(`Tweet marked as ${newStatus}`, { timeout: 2000 });
      }
    });
  } finally {
    isToggling.value = null;
  }
};

// Edit tweet
const editTweet = (tweet) => {
  editingTweet.value = tweet;
  editForm.value.content = tweet.content;
  showEditModal.value = true;
};

// Save edited tweet
const saveEdit = async () => {
  if (isSaving.value || !editForm.value.content.trim()) return;
  
  isSaving.value = true;
  try {
    await router.patch(route('x-post.tweets.update', editingTweet.value.id), {
      content: editForm.value.content.trim()
    }, {
      onSuccess: () => {
        toast.success('Tweet updated successfully!', { timeout: 2000 });
        showEditModal.value = false;
        editingTweet.value = null;
        editForm.value.content = '';
      }
    });
  } finally {
    isSaving.value = false;
  }
};

// Copy edited tweet and close modal
const copyEditedTweet = async () => {
  await copyTweet({ content: editForm.value.content });
  cancelEdit();
};

// Cancel edit
const cancelEdit = () => {
  showEditModal.value = false;
  editingTweet.value = null;
  editForm.value.content = '';
};

onMounted(() => {
  isGenerating.value = props.settings.x_post_job_queued;
  if (isGenerating.value) {
    startPolling();
  }
});

onUnmounted(() => {
  if (pollingInterval.value) {
    clearInterval(pollingInterval.value);
  }
});
</script>