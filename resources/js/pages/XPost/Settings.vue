<template>
  <AppLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">X Post Settings</h2>
        <Link 
          :href="route('x-post.index')" 
          class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2"
        >
          ← Back to Dashboard
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
        
        <!-- Profile Settings Card -->
        <div class="bg-card text-card-foreground shadow-sm rounded-lg border">
          <div class="p-6">
            <h3 class="text-lg font-medium mb-6">👤 Profile Settings</h3>
            <form @submit.prevent="submitForm" class="space-y-4">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-2">
                  <label for="profile_name" class="text-sm font-medium leading-none">Profile Name</label>
                  <input
                    type="text"
                    id="profile_name"
                    v-model="form.profile_name"
                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                  >
                  <p v-if="form.errors.profile_name" class="text-sm text-destructive">{{ form.errors.profile_name }}</p>
                </div>

                <div class="space-y-2">
                  <label for="max_tweet_length" class="text-sm font-medium leading-none">Maximum Tweet Length</label>
                  <input
                    type="number"
                    id="max_tweet_length"
                    v-model="form.max_tweet_length"
                    min="100"
                    max="1200"
                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                  >
                  <p v-if="form.errors.max_tweet_length" class="text-sm text-destructive">{{ form.errors.max_tweet_length }}</p>
                </div>
              </div>

              <div class="space-y-2">
                <label for="about_me" class="text-sm font-medium leading-none">About Me</label>
                <textarea
                  id="about_me"
                  v-model="form.about_me"
                  rows="6"
                  placeholder="Tell us about yourself, your expertise, and what you do..."
                  class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                ></textarea>
                <p v-if="form.errors.about_me" class="text-sm text-destructive">{{ form.errors.about_me }}</p>
              </div>

              <div class="space-y-2">
                <label for="personality" class="text-sm font-medium leading-none">Personality & Tone</label>
                <textarea
                  id="personality"
                  v-model="form.personality"
                  rows="6"
                  placeholder="Describe your personality, tone, and how you want to communicate..."
                  class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                ></textarea>
                <p v-if="form.errors.personality" class="text-sm text-destructive">{{ form.errors.personality }}</p>
              </div>

              <button
                type="submit"
                class="w-full bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2 rounded-md text-sm font-medium"
              >
                💾 Update Profile
              </button>
            </form>
          </div>
        </div>

        <!-- Tweet Contexts Card -->
        <div class="bg-card text-card-foreground shadow-sm rounded-lg border">
          <div class="p-6">
            <div class="flex justify-between items-center mb-6">
              <h3 class="text-lg font-medium">📝 Tweet Contexts</h3>
              <button
                @click="showNewContextForm = true"
                class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-secondary text-secondary-foreground hover:bg-secondary/80 h-10 px-4 py-2"
              >
                <span class="mr-2">+</span> Add Context
              </button>
            </div>

            <!-- New Context Form -->
            <div v-if="showNewContextForm" class="mb-6 p-4 border rounded-lg bg-muted/50">
              <h4 class="text-md font-medium mb-4">{{ editingContext ? 'Edit Context' : 'Add New Context' }}</h4>
              <form @submit.prevent="submitContextForm" class="space-y-4">
                <div class="space-y-2">
                  <label for="context_name" class="text-sm font-medium leading-none">Context Name</label>
                  <input
                    type="text"
                    id="context_name"
                    v-model="contextForm.name"
                    placeholder="e.g., Laravel Tips, Startup Advice, Tech News"
                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                  >
                  <p v-if="contextForm.errors.name" class="text-sm text-destructive">{{ contextForm.errors.name }}</p>
                </div>

                <div class="space-y-2">
                  <label for="context_text" class="text-sm font-medium leading-none">Context Instructions</label>
                  <textarea
                    id="context_text"
                    v-model="contextForm.context"
                    rows="8"
                    placeholder="Describe the style, audience, tone, and format for this type of content..."
                    class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                  ></textarea>
                  <p v-if="contextForm.errors.context" class="text-sm text-destructive">{{ contextForm.errors.context }}</p>
                </div>

                <div class="flex items-center space-x-2">
                  <input
                    type="checkbox"
                    id="is_default"
                    v-model="contextForm.is_default"
                    class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary"
                  >
                  <label for="is_default" class="text-sm font-medium leading-none">Set as default context</label>
                </div>

                <div class="flex space-x-4 pt-2">
                  <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2"
                  >
                    {{ editingContext ? 'Update Context' : 'Save Context' }}
                  </button>
                  <button
                    type="button"
                    @click="cancelContextForm"
                    class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2"
                  >
                    Cancel
                  </button>
                </div>
              </form>
            </div>

            <!-- Contexts List -->
            <div class="space-y-4">
              <div v-for="context in settings.contexts" :key="context.id" class="border rounded-lg p-4 hover:bg-accent/20 transition-colors">
                <div class="flex justify-between items-start">
                  <div class="space-y-2 flex-1">
                    <div class="flex items-center space-x-2">
                      <h4 class="font-medium">{{ context.name }}</h4>
                      <span v-if="context.is_default" class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 border-transparent bg-green-100 text-green-800">
                        Default
                      </span>
                    </div>
                    <p class="text-sm text-muted-foreground">{{ context.context.substring(0, 200) }}{{ context.context.length > 200 ? '...' : '' }}</p>
                  </div>
                  <div class="flex space-x-2 ml-4">
                    <button
                      @click="editContext(context)"
                      class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 h-8 px-3 text-accent-foreground hover:underline"
                    >
                      Edit
                    </button>
                    <button
                      @click="deleteContext(context)"
                      class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 h-8 px-3 text-destructive hover:underline"
                    >
                      Delete
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- X Communities Card -->
        <div class="bg-card text-card-foreground shadow-sm rounded-lg border">
          <div class="p-6">
            <div class="flex justify-between items-center mb-6">
              <h3 class="text-lg font-medium">🌐 X Communities</h3>
              <button
                @click="showNewCommunityForm = true"
                class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-secondary text-secondary-foreground hover:bg-secondary/80 h-10 px-4 py-2"
              >
                <span class="mr-2">+</span> Add Community
              </button>
            </div>

            <!-- New Community Form -->
            <div v-if="showNewCommunityForm" class="mb-6 p-4 border rounded-lg bg-muted/50">
              <h4 class="text-md font-medium mb-4">{{ editingCommunity ? 'Edit Community' : 'Add New Community' }}</h4>
              <form @submit.prevent="submitCommunityForm" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div class="space-y-2">
                    <label for="community_name" class="text-sm font-medium leading-none">Community Name</label>
                    <input
                      type="text"
                      id="community_name"
                      v-model="communityForm.name"
                      placeholder="e.g., Build in Public"
                      class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                    <p v-if="communityForm.errors.name" class="text-sm text-destructive">{{ communityForm.errors.name }}</p>
                  </div>

                  <div class="space-y-2">
                    <label for="community_id_field" class="text-sm font-medium leading-none">Community ID</label>
                    <input
                      type="text"
                      id="community_id_field"
                      v-model="communityForm.community_id"
                      placeholder="X Community ID"
                      class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                    <p v-if="communityForm.errors.community_id" class="text-sm text-destructive">{{ communityForm.errors.community_id }}</p>
                  </div>
                </div>

                <div class="space-y-2">
                  <label for="community_description" class="text-sm font-medium leading-none">Description</label>
                  <textarea
                    id="community_description"
                    v-model="communityForm.description"
                    rows="3"
                    placeholder="Describe the community focus and audience..."
                    class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                  ></textarea>
                  <p v-if="communityForm.errors.description" class="text-sm text-destructive">{{ communityForm.errors.description }}</p>
                </div>

                <div class="flex items-center space-x-2">
                  <input
                    type="checkbox"
                    id="is_active"
                    v-model="communityForm.is_active"
                    class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary"
                  >
                  <label for="is_active" class="text-sm font-medium leading-none">Active (available for posting)</label>
                </div>

                <div class="flex space-x-4 pt-2">
                  <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2"
                  >
                    {{ editingCommunity ? 'Update Community' : 'Save Community' }}
                  </button>
                  <button
                    type="button"
                    @click="cancelCommunityForm"
                    class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2"
                  >
                    Cancel
                  </button>
                </div>
              </form>
            </div>

            <!-- Communities List -->
            <div class="space-y-4">
              <div v-for="community in settings.communities" :key="community.id" class="border rounded-lg p-4 hover:bg-accent/20 transition-colors">
                <div class="flex justify-between items-start">
                  <div class="space-y-2 flex-1">
                    <div class="flex items-center space-x-2">
                      <h4 class="font-medium">{{ community.name }}</h4>
                      <span v-if="community.is_active" class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 border-transparent bg-green-100 text-green-800">
                        Active
                      </span>
                      <span v-else class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 border-transparent bg-gray-100 text-gray-800">
                        Inactive
                      </span>
                    </div>
                    <p class="text-sm text-muted-foreground">ID: {{ community.community_id }}</p>
                    <p v-if="community.description" class="text-sm text-muted-foreground">{{ community.description }}</p>
                  </div>
                  <div class="flex space-x-2 ml-4">
                    <button
                      @click="editCommunity(community)"
                      class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 h-8 px-3 text-accent-foreground hover:underline"
                    >
                      Edit
                    </button>
                    <button
                      @click="deleteCommunity(community)"
                      class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 h-8 px-3 text-destructive hover:underline"
                    >
                      Delete
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useToast } from "vue-toastification";

const toast = useToast();

const props = defineProps({
  settings: {
    type: Object,
    required: true
  }
});

const showNewContextForm = ref(false);
const editingContext = ref(null);
const showNewCommunityForm = ref(false);
const editingCommunity = ref(null);

const form = useForm({
  profile_name: props.settings.profile_name,
  about_me: props.settings.about_me,
  personality: props.settings.personality,
  max_tweet_length: props.settings.max_tweet_length
});

const contextForm = useForm({
  name: '',
  context: '',
  is_default: false
});

const communityForm = useForm({
  name: '',
  community_id: '',
  description: '',
  is_active: true
});

const submitForm = () => {
  form.put(route('x-post.update'), {
    onSuccess: () => {
      toast.success("Profile updated successfully", {
        timeout: 2000
      });
    }
  });
};

const submitContextForm = () => {
  if (editingContext.value) {
    contextForm.put(route('x-post.contexts.update', editingContext.value.id), {
      onSuccess: () => {
        showNewContextForm.value = false;
        editingContext.value = null;
        contextForm.reset();
        toast.success("Context updated successfully", {
          timeout: 2000
        })
      },
      onError: (errors) => {
        toast.error("Failed to update context", {
          timeout: 2000
        })
        console.error('Error updating context:', errors);
      }
    });
  } else {
    contextForm.post(route('x-post.contexts.store'), {
      onSuccess: () => {
        toast.success("Context created successfully", {
          timeout: 2000
        })
        showNewContextForm.value = false;
        contextForm.reset();
      },
      onError: (errors) => {
        toast.error("Failed to create context", {
          timeout: 2000
        })
        console.error('Error creating context:', errors);
      }
    });
  }
};

const cancelContextForm = () => {
  showNewContextForm.value = false;
  editingContext.value = null;
  contextForm.reset();
};

const editContext = (context) => {
  editingContext.value = context;
  contextForm.name = context.name;
  contextForm.context = context.context;
  contextForm.is_default = context.is_default;
  showNewContextForm.value = true;
};

const deleteContext = (context) => {
  if (confirm('Are you sure you want to delete this context?')) {
    useForm().delete(route('x-post.contexts.destroy', context.id), {
      onSuccess: () => {
        toast.success("Context deleted successfully", {
          timeout: 2000
        })
      },
      onError: (errors) => {
        toast.error("Failed to delete context", {
          timeout: 2000
        })
        console.error('Error deleting context:', errors);
      }
    });
  }
};

const submitCommunityForm = () => {
  if (editingCommunity.value) {
    communityForm.put(route('x-post.communities.update', editingCommunity.value.id), {
      onSuccess: () => {
        showNewCommunityForm.value = false;
        editingCommunity.value = null;
        communityForm.reset();
        toast.success("Community updated successfully", {
          timeout: 2000
        })
      },
      onError: (errors) => {
        toast.error("Failed to update community", {
          timeout: 2000
        })
        console.error('Error updating community:', errors);
      }
    });
  } else {
    communityForm.post(route('x-post.communities.store'), {
      onSuccess: () => {
        toast.success("Community created successfully", {
          timeout: 2000
        })
        showNewCommunityForm.value = false;
        communityForm.reset();
      },
      onError: (errors) => {
        toast.error("Failed to create community", {
          timeout: 2000
        })
        console.error('Error creating community:', errors);
      }
    });
  }
};

const cancelCommunityForm = () => {
  showNewCommunityForm.value = false;
  editingCommunity.value = null;
  communityForm.reset();
};

const editCommunity = (community) => {
  editingCommunity.value = community;
  communityForm.name = community.name;
  communityForm.community_id = community.community_id;
  communityForm.description = community.description;
  communityForm.is_active = community.is_active;
  showNewCommunityForm.value = true;
};

const deleteCommunity = (community) => {
  if (confirm('Are you sure you want to delete this community?')) {
    useForm().delete(route('x-post.communities.destroy', community.id), {
      onSuccess: () => {
        toast.success("Community deleted successfully", {
          timeout: 2000
        })
      },
      onError: (errors) => {
        toast.error("Failed to delete community", {
          timeout: 2000
        })
        console.error('Error deleting community:', errors);
      }
    });
  }
};
</script>