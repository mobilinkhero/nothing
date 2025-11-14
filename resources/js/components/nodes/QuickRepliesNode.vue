<template>
  <div class="quick-replies-node">
    <NodeWrapper
      :id="id"
      :selected="selected"
      title="Quick Replies"
      :icon="IconComponent"
      :data="nodeData"
    >
      <template #content>
        <div class="space-y-3">
          <!-- Message Text -->
          <div>
            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
              Message Text *
            </label>
            <textarea
              v-model="nodeData.message"
              @input="updateNode"
              rows="3"
              class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white resize-none focus:ring-2 focus:ring-blue-500"
              placeholder="Enter your question or message... (Required for WhatsApp)"
              required
            ></textarea>
            <p v-if="!nodeData.message.trim()" class="text-xs text-red-600 dark:text-red-400 mt-1">
              ⚠️ Message text is required - WhatsApp doesn't allow empty messages
            </p>
          </div>

          <!-- Header (Optional) -->
          <div>
            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
              Header (Optional)
            </label>
            <input
              v-model="nodeData.header"
              @input="updateNode"
              type="text"
              class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500"
              placeholder="e.g., Quick Question"
            />
          </div>

          <!-- Quick Reply Options -->
          <div>
            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
              Quick Reply Options (Max 13)
            </label>
            
            <div class="space-y-2">
              <div 
                v-for="(reply, index) in nodeData.replies" 
                :key="index"
                class="flex items-center gap-2"
              >
                <input
                  v-model="reply.text"
                  @input="updateNode"
                  type="text"
                  :placeholder="`Option ${index + 1}`"
                  class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500"
                  maxlength="20"
                />
                <button
                  @click="removeReply(index)"
                  v-if="nodeData.replies.length > 1"
                  class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900 rounded-md transition-colors"
                  title="Remove reply"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                  </svg>
                </button>
              </div>
            </div>

            <button
              @click="addReply"
              v-if="nodeData.replies.length < 13"
              class="mt-2 w-full px-3 py-2 text-sm text-blue-600 border border-blue-300 rounded-md hover:bg-blue-50 dark:hover:bg-blue-900 dark:border-blue-600 dark:text-blue-400 transition-colors"
            >
              + Add Reply Option
            </button>
            <p v-if="nodeData.replies.length >= 13" class="mt-2 text-xs text-amber-600 dark:text-amber-400">
              Maximum 13 quick replies reached (WhatsApp limit)
            </p>
          </div>

          <!-- Footer (Optional) -->
          <div>
            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
              Footer (Optional)
            </label>
            <input
              v-model="nodeData.footer"
              @input="updateNode"
              type="text"
              class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500"
              placeholder="e.g., Reply to continue"
            />
          </div>

          <!-- Settings -->
          <div class="border-t border-gray-200 dark:border-gray-600 pt-3">
            <div class="flex items-center justify-between">
              <label class="text-xs font-medium text-gray-700 dark:text-gray-300">
                Track Analytics
              </label>
              <input
                v-model="nodeData.trackAnalytics"
                @change="updateNode"
                type="checkbox"
                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
              />
            </div>
          </div>

          <!-- Preview -->
          <div class="border-t border-gray-200 dark:border-gray-600 pt-3">
            <p class="text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Preview</p>
            <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-md border border-gray-200 dark:border-gray-600">
              <p v-if="nodeData.header" class="text-xs font-semibold text-gray-900 dark:text-white mb-1">
                {{ nodeData.header }}
              </p>
              <p class="text-xs text-gray-700 dark:text-gray-300 mb-2">
                {{ nodeData.message || 'Enter your message...' }}
              </p>
              <div class="flex flex-wrap gap-1">
                <span
                  v-for="(reply, index) in nodeData.replies.filter(r => r.text)"
                  :key="index"
                  class="inline-block px-2 py-1 text-xs bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-full text-gray-700 dark:text-gray-300"
                >
                  {{ reply.text }}
                </span>
              </div>
              <p v-if="nodeData.footer" class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                {{ nodeData.footer }}
              </p>
            </div>
          </div>

          <!-- Info -->
          <div class="bg-blue-50 dark:bg-blue-900 p-2 rounded-md">
            <p class="text-xs text-blue-700 dark:text-blue-300">
              <strong>Quick Replies:</strong> Fast text-based responses that appear above the keyboard. 
              Different from interactive buttons - these disappear after selection.
            </p>
          </div>
        </div>
      </template>

      <template #handles>
        <Handle
          id="input"
          type="target"
          position="left"
          class="w-3 h-3 !bg-gray-400"
        />
        <!-- Dynamic handles for each reply option -->
        <Handle
          v-for="(reply, index) in nodeData.replies.filter(r => r.text)"
          :key="`reply-${index}`"
          :id="`reply-${index}`"
          type="source"
          position="right"
          :style="{ top: `${20 + (index * (60 / Math.max(nodeData.replies.filter(r => r.text).length, 1)))}%` }"
          class="w-3 h-3 !bg-purple-500"
        />
      </template>
    </NodeWrapper>
  </div>
</template>

<script setup>
import { reactive, watch, h } from 'vue'
import { Handle } from '@vue-flow/core'
import NodeWrapper from '../ui/NodeWrapper.vue'
import { BsChatRightQuote } from '@kalimahapps/vue-icons'

const IconComponent = {
  render: () => h(BsChatRightQuote, { class: 'w-5 h-5' })
}

const props = defineProps({
  id: String,
  data: Object,
  selected: Boolean,
})

const emit = defineEmits(['update-node'])

// Initialize node data
const nodeData = reactive({
  message: props.data?.message || '',
  header: props.data?.header || '',
  footer: props.data?.footer || '',
  replies: props.data?.replies || [
    { text: '', id: 'reply-0' },
    { text: '', id: 'reply-1' },
    { text: '', id: 'reply-2' }
  ],
  trackAnalytics: props.data?.trackAnalytics ?? true,
  isValid: true
})

// Add reply option
function addReply() {
  if (nodeData.replies.length < 13) {
    nodeData.replies.push({
      text: '',
      id: `reply-${nodeData.replies.length}`
    })
    updateNode()
  }
}

// Remove reply option
function removeReply(index) {
  if (nodeData.replies.length > 1) {
    nodeData.replies.splice(index, 1)
    updateNode()
  }
}

// Validate and update
function updateNode() {
  // Validate that message is not empty and at least one reply has text
  const hasMessage = nodeData.message.trim().length > 0
  const hasReplies = nodeData.replies.some(r => r.text.trim().length > 0)
  
  nodeData.isValid = hasMessage && hasReplies

  // Show validation feedback
  if (!hasMessage) {
    console.warn('Quick Replies: Message text is required')
  }
  if (!hasReplies) {
    console.warn('Quick Replies: At least one reply option is required')
  }

  console.log('QuickReplies: Updating node data', {
    message: nodeData.message,
    hasMessage,
    hasReplies,
    isValid: nodeData.isValid
  })

  emit('update-node', {
    id: props.id,
    data: { ...nodeData }
  })
}

// Watch for external data changes (only when not user editing)
watch(() => props.data, (newData) => {
  if (newData && newData !== nodeData) {
    // Only update if it's genuinely different external data
    // Avoid overwriting during user input
    const hasChanges = Object.keys(newData).some(key => {
      if (key === 'replies') {
        return JSON.stringify(newData[key]) !== JSON.stringify(nodeData[key])
      }
      return newData[key] !== nodeData[key]
    })
    
    if (hasChanges) {
      console.log('QuickReplies: Updating from external data', newData)
      Object.assign(nodeData, newData)
    }
  }
}, { deep: true, immediate: false })
</script>

<style scoped>
.quick-replies-node {
  min-width: 320px;
}
</style>
