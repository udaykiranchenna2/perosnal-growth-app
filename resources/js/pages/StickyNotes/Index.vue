<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="min-h-screen  dark:from-gray-900 dark:to-gray-800 p-6">
      <div class=" mx-auto">
        <div class="flex justify-between items-center mb-8">
          <div>
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">My Sticky Notes</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Stay organized with your digital notes</p>
          </div>
          <button
            @click="addNewNote"
            class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 transition-all shadow-md hover:shadow-lg flex items-center gap-2"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            Add New Note
          </button>
        </div>

        <div v-if="notes.length === 0" class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-10 text-center">
          <div class="flex justify-center mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
          </div>
          <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-2">No notes yet</h3>
          <p class="text-gray-500 dark:text-gray-400 mb-6">Create your first note to get started</p>
          <button
            @click="addNewNote"
            class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 transition-all"
          >
            Create a Note
          </button>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <draggable
            v-model="notes"
            item-key="id"
            handle=".note-handle"
            ghost-class="ghost"
            @end="onDragEnd"
            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 col-span-full"
          >
            <template #item="{ element }">
              <div
                class="w-full rounded-xl shadow-lg transition-all duration-300 transform hover:-translate-y-1 hover:shadow-xl overflow-hidden"
                :style="{ backgroundColor: element.color }"
              >
                <div class="p-5">
                  <div class="flex items-start justify-between mb-4">
                    <div class="note-handle cursor-move p-2 rounded-lg hover:bg-white/20 dark:hover:bg-black/20 -ml-2">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600 dark:text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                      </svg>
                    </div>
                    <div class="flex items-center gap-2">
                      <div class="flex space-x-1 bg-white/30 dark:bg-black/30 rounded-full p-1">
                        <button
                          v-for="color in colors"
                          :key="color"
                          :style="{ backgroundColor: color }"
                          class="w-5 h-5 rounded-full border border-gray-300 dark:border-gray-600 hover:scale-110 transition-transform"
                          @click="changeColor(element, color)"
                        ></button>
                      </div>
                      <button
                        v-if="element.shared_url"
                        @click="deleteSharedContent(element)"
                        class="text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-500 p-2 rounded-full hover:bg-white/20 dark:hover:bg-black/20"
                        title="Delete Shared URL"
                      >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                          <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                      </button>
                      <button
                        @click="shareNote(element)"
                        class="text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-500 p-2 rounded-full hover:bg-white/20 dark:hover:bg-black/20"
                        title="Share Note"
                      >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                          <path d="M15 8a3 3 0 10-2.977-2.63l-4.94 2.47a3 3 0 100 4.319l4.94 2.47a3 3 0 10.895-1.789l-4.94-2.47a3.027 3.027 0 000-.74l4.94-2.47C13.456 7.68 14.19 8 15 8z" />
                        </svg>
                      </button>
                      <button
                        @click="deleteNote(element)"
                        class="text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-500 p-2 rounded-full hover:bg-white/20 dark:hover:bg-black/20"
                      >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                          <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                      </button>
                    </div>
                  </div>
                  <div>
                    <input
                      v-model="element.title"
                      class="text-xl font-semibold text-gray-800 dark:text-gray-400 bg-transparent border-b border-gray-300 dark:border-gray-600 focus:border-indigo-500 dark:focus:border-indigo-400 focus:outline-none w-full mb-3 pb-1 transition-colors"
                      placeholder="Note title"
                      @blur="updateNote(element)"
                    />
                    <textarea
                      v-model="element.content"
                      class="w-full h-32 bg-transparent text-gray-700 dark:text-gray-400 border-none focus:outline-none resize-none"
                      placeholder="Write your note here..."
                      @blur="updateNote(element)"
                    ></textarea>
                  </div>
                </div>
                <div class="bg-black/5 dark:bg-white/5 px-5 py-2 text-xs text-gray-500 dark:text-gray-400 flex justify-between items-center">
                  <span>Last updated: {{ formatDate(element.updated_at || new Date()) }}</span>
                  <span>{{ element.content.length }} characters</span>
                </div>
              </div>
            </template>
          </draggable>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, computed, onMounted } from 'vue';
import draggable from 'vuedraggable';
import axios from 'axios';

const props = defineProps({
  notes: {
    type: Array,
    required: true,
  },
  breadcrumbs: {
    type: Array,
    default: () => [
      { name: 'Dashboard', href: '/dashboard' },
      { name: 'Sticky Notes', href: '/sticky-notes' }
    ]
  }
});

const notes = ref(props.notes);

const colors = ref([
  '#fff3cd', // Yellow
  '#d1e7dd', // Green
  '#f8d7da', // Red
  '#cff4fc', // Blue
  '#e2e3e5', // Gray
  '#f8f9fa', // White
  '#ede9fe', // Light Purple
  '#ffedd5', // Light Orange
  '#1a1a1a', // Dark Gray
  '#2d3748', // Dark Blue
]);

const addNewNote = async () => {
  try {
    const response = await axios.post('/sticky-notes', {
      title: 'New Note',
      content: 'Write your note here...',
      color: colors.value[Math.floor(Math.random() * colors.value.length)],
      order: notes.value.length,
    });
    notes.value.push(response.data);
  } catch (error) {
    console.error('Error creating note:', error);
  }
};

const updateNote = async (note) => {
  try {
    await axios.put(`/sticky-notes/${note.id}`, note);
  } catch (error) {
    console.error('Error updating note:', error);
  }
};

const deleteNote = async (note) => {
  if (confirm('Are you sure you want to delete this note?')) {
    try {
      await axios.delete(`/sticky-notes/${note.id}`);
      notes.value = notes.value.filter((n) => n.id !== note.id);
    } catch (error) {
      console.error('Error deleting note:', error);
    }
  }
};

const changeColor = async (note, color) => {
  note.color = color;
  await updateNote(note);
};

const onDragEnd = async () => {
  try {
    // Prepare the notes data with their new order
    const notesData = notes.value.map((note, index) => ({
      id: note.id,
      order: index
    }));

    // Send a single request to update all notes' order
    await axios.post('/sticky-notes/update-order', {
      notes: notesData
    });
  } catch (error) {
    console.error('Error updating note order:', error);
  }
};

const formatDate = (date) => {
  const d = new Date(date);
  return d.toLocaleDateString(undefined, { month: 'short', day: 'numeric' });
};

const shareNote = async (note) => {
  try {
    // If note already has a shared URL, just copy it
    if (note.shared_url) {
      await navigator.clipboard.writeText(note.shared_url);
      alert('Shared URL copied to clipboard!');
      return;
    }

    // Create new shared content
    const response = await axios.post('/shared-content', {
      sticky_note_id: note.id
    });

    // Store the shared URL in the note and save to database
    note.shared_url = response.data.url;
    await updateNote(note);
    
    // Copy the URL to clipboard
    await navigator.clipboard.writeText(response.data.url);
    alert('Note shared! URL copied to clipboard.');
  } catch (error) {
    console.error('Error sharing note:', error);
    alert('Failed to share note. Please try again.');
  }
};

const deleteSharedContent = async (note) => {
  if (confirm('Are you sure you want to delete this shared content? This action cannot be undone.')) {
    try {
      const slug = note.shared_url.split('/').pop();
      await axios.delete(`/shared-content/${slug}`);
      
      // Remove shared URL from note and save to database
      note.shared_url = null;
      await updateNote(note);
      
      alert('Shared content deleted successfully.');
    } catch (error) {
      console.error('Error deleting shared content:', error);
      alert('Failed to delete shared content. Please try again.');
    }
  }
};
</script>

<style scoped>
.ghost {
  opacity: 0.5;
  background: #e0e7ff;
  box-shadow: 0 0 15px rgba(99, 102, 241, 0.4);
}

.note-handle {
  cursor: move;
  user-select: none;
}

textarea,
input {
  color: #1f2937; /* Tailwind gray-900 */
}

textarea:focus,
input:focus {
  box-shadow: none;
}

input::placeholder,
textarea::placeholder {
  color: #9ca3af; /* Tailwind gray-400 */
}

/* Animation for new notes */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

.draggable-item {
  animation: fadeIn 0.3s ease-out;
}

/* Dark mode styles */
@media (prefers-color-scheme: dark) {
  textarea,
  input {
    color: #f3f4f6; /* Tailwind gray-100 */
  }

  input::placeholder,
  textarea::placeholder {
    color: #6b7280; /* Tailwind gray-500 */
  }
}
</style>