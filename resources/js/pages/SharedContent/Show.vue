<template>
    <div class="min-h-screen bg-gradient-to-br from-blue-100 to-indigo-200 dark:from-gray-900 dark:to-gray-800 p-4 sm:p-6">
      <div class="max-w-7xl mx-auto">
        <div class="bg-white dark:bg-gray-900 shadow-2xl rounded-2xl p-6 sm:p-10 transition-all duration-300">
  
          <!-- Error Block -->
          <div v-if="error" class="text-center space-y-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-red-500 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-100">Note Not Found</h2>
            <p class="text-gray-600 dark:text-gray-400">{{ error }}</p>
            <a href="/" class="inline-block px-5 py-2 mt-4 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 transition-all">
              Return to Home
            </a>
          </div>
  
          <!-- Content Block -->
          <div v-else>
            <div class="flex flex-col sm:flex-row justify-between items-start gap-4 mb-6">
              <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white leading-tight">{{ content.title }}</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Shared {{ formatDate(content.updated_at) }}</p>
              </div>
              <button
                @click="copyUrl"
                class="flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 transition"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                  <path d="M8 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" />
                  <path d="M6 3a2 2 0 00-2 2v11a2 2 0 002 2h8a2 2 0 002-2V5a2 2 0 00-2-2 3 3 0 01-3 3H9a3 3 0 01-3-3z" />
                </svg>
                Copy URL
              </button>
            </div>
  
            <div class="prose dark:prose-invert prose-lg max-w-none whitespace-pre-wrap leading-relaxed">
              {{ content.content }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  
  const props = defineProps({
    content: Object,
    error: String
  });
  
  const formatDate = (date) => {
    const d = new Date(date);
    return d.toLocaleDateString(undefined, {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    });
  };
  
  const copyUrl = () => {
    const url = window.location.href;
    navigator.clipboard.writeText(url).then(() => {
      alert('URL copied to clipboard!');
    });
  };
  </script>
  
  <style>
  .prose pre {
    background-color: #1e293b;
    color: #f8fafc;
    padding: 1rem;
    border-radius: 0.5rem;
    overflow-x: auto;
  }
  
  .prose code {
    color: #f1f5f9;
    background-color: #334155;
    padding: 0.2rem 0.4rem;
    border-radius: 0.25rem;
  }
  
  .dark .prose pre {
    background-color: #334155;
  }
  
  .dark .prose code {
    background-color: #475569;
  }
  </style>
  