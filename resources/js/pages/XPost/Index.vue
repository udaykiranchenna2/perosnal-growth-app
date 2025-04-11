<template>
    <AppLayout>
      <template #header>
        <div class="flex justify-between items-center">
          <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">X Post Settings</h2>
          <Link :href="route('x-post.tweets')" class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2">
            <span>View Tweets</span>
          </Link>
        </div>
      </template>

      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
          <!-- Profile Settings Card -->
          <div class="bg-card text-card-foreground shadow-sm rounded-lg border">
            <div class="p-6">
              <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium">Profile Settings</h3>
                <button
                  type="submit"
                  class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2"
                  @click="submitForm"
                >
                  Update Profile
                </button>
              </div>

              <form @submit.prevent="submitForm" class="space-y-4">
                <div class="space-y-2">
                  <label for="profile_name" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Profile Name</label>
                  <input
                    type="text"
                    id="profile_name"
                    v-model="form.profile_name"
                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                  >
                  <p v-if="form.errors.profile_name" class="text-sm text-destructive">{{ form.errors.profile_name }}</p>
                </div>

                <div class="space-y-2">
                  <label for="about_me" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">About Me</label>
                  <textarea
                    id="about_me"
                    v-model="form.about_me"
                    rows="12"
                    class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                  ></textarea>
                  <p v-if="form.errors.about_me" class="text-sm text-destructive">{{ form.errors.about_me }}</p>
                </div>

                <div class="space-y-2">
                  <label for="personality" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Personality</label>
                  <textarea
                    id="personality"
                    v-model="form.personality"
                    rows="12"
                    class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                  ></textarea>
                  <p v-if="form.errors.personality" class="text-sm text-destructive">{{ form.errors.personality }}</p>
                </div>

                <div class="space-y-2">
                  <label for="max_tweet_length" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Maximum Tweet Length</label>
                  <input
                    type="number"
                    id="max_tweet_length"
                    v-model="form.max_tweet_length"
                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                  >
                  <p v-if="form.errors.max_tweet_length" class="text-sm text-destructive">{{ form.errors.max_tweet_length }}</p>
                </div>
              </form>
            </div>
          </div>

          <!-- Tweet Contexts Card -->
          <div class="bg-card text-card-foreground shadow-sm rounded-lg border">
            <div class="p-6">
              <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-medium">Tweet Contexts</h3>
                <button
                  @click="showNewContextForm = true"
                  class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-secondary text-secondary-foreground hover:bg-secondary/80 h-10 px-4 py-2"
                >
                  <span class="mr-2">+</span> Add New Context
                </button>
              </div>

              <!-- New Context Form -->
              <div v-if="showNewContextForm" class="mb-6 p-4 border rounded-lg bg-muted/50">
                <h4 class="text-md font-medium mb-4">{{ editingContext ? 'Edit Context' : 'Add New Context' }}</h4>
                <form @submit.prevent="submitContextForm" class="space-y-4">
                  <div class="space-y-2">
                    <label for="context_name" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Context Name</label>
                    <input
                      type="text"
                      id="context_name"
                      v-model="contextForm.name"
                      class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                    <p v-if="contextForm.errors.name" class="text-sm text-destructive">{{ contextForm.errors.name }}</p>
                  </div>

                  <div class="space-y-2">
                    <label for="context_text" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Context</label>
                    <textarea
                      id="context_text"
                      v-model="contextForm.context"
                      rows="16"
                      class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                    ></textarea>
                    <p v-if="contextForm.errors.context" class="text-sm text-destructiv`e">{{ contextForm.errors.context }}</p>
                  </div>

                  <div class="flex items-center space-x-2">
                    <input
                      type="checkbox"
                      id="is_default"
                      v-model="contextForm.is_default"
                      class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary"
                    >
                    <label for="is_default" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Set as default context</label>
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
                    <div class="space-y-2">
                      <div class="flex items-center space-x-2">
                        <h4 class="font-medium">{{ context.name }}</h4>
                        <span v-if="context.is_default" class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 border-transparent bg-green-100 text-green-800">
                          Default
                        </span>
                      </div>
                      <p class="text-sm text-muted-foreground">{{ context.context }}</p>
                    </div>
                    <div class="flex space-x-2">
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

          <!-- Generate Tweet Card -->
          <div class="bg-card text-card-foreground shadow-sm rounded-lg border">
            <div class="p-6">
              <h3 class="text-lg font-medium mb-4">Generate Tweet</h3>
              <form @submit.prevent="generateTweet" class="space-y-4">
                <div class="space-y-2">
                  <label for="context_id" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Select Context</label>
                  <select
                    id="context_id"
                    v-model="generateForm.context_id"
                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                  >
                    <option v-for="context in settings.contexts" :key="context.id" :value="context.id">
                      {{ context.name }}
                    </option>
                  </select>
                </div>
                <button
                  type="submit"
                  class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-blue-500 text-white hover:bg-blue-600 h-10 px-4 py-2"
                >
                  Generate Tweet
                </button>
              </form>
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

  const props = defineProps({
    settings: {
      type: Object,
      required: true
    }
  });

  const showNewContextForm = ref(false);
  const editingContext = ref(null);

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

  const generateForm = useForm({
    context_id: props.settings.defaultContext?.id || ''
  });

  const submitForm = () => {
    form.put(route('x-post.update'));
  };

  const submitContextForm = () => {
    if (editingContext.value) {
      contextForm.put(route('x-post.contexts.update', editingContext.value.id), {
        onSuccess: () => {
          showNewContextForm.value = false;
          editingContext.value = null;
          contextForm.reset();
        },
        onError: (errors) => {
          console.error('Error updating context:', errors);
        }
      });
    } else {
      contextForm.post(route('x-post.contexts.store'), {
        onSuccess: () => {
          showNewContextForm.value = false;
          contextForm.reset();
        },
        onError: (errors) => {
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
          // Context will be automatically removed from the list due to Inertia
        },
        onError: (errors) => {
          console.error('Error deleting context:', errors);
        }
      });
    }
  };

  const generateTweet = () => {
    generateForm.post(route('x-post.generate'));
  };
  </script>
