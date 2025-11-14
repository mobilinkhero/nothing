<template>
  <div class="ai-assistant-node">
    <NodeWrapper
      :id="id"
      :selected="selected"
      :type="'aiAssistant'"
      :title="'AI Assistant'"
      :icon="FlFilledLightbulbFilament"
      :color="'#F59E0B'"
    >
      <template #content>
        <div class="space-y-4">
          <!-- AI Model Selection -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              AI Model
            </label>
            <select
              v-model="nodeData.aiModel"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
              @change="updateNodeData"
            >
              <option value="gpt-3.5-turbo">GPT-3.5 Turbo</option>
              <option value="gpt-3.5-turbo-16k">GPT-3.5 Turbo (16k)</option>
              <option value="gpt-4">GPT-4</option>
              <option value="gpt-4-turbo">GPT-4 Turbo</option>
              <option value="gpt-4o-mini">GPT-4o Mini</option>
            </select>
          </div>

          <!-- Context Type -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Context Type
            </label>
            <select
              v-model="nodeData.contextType"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
              @change="updateNodeData"
            >
              <option value="message">Single Message</option>
              <option value="conversation">Full Conversation</option>
              <option value="flow">Flow Context</option>
            </select>
          </div>

          <!-- System Prompt -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              System Prompt
            </label>
            <textarea
              v-model="nodeData.prompt"
              rows="4"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white resize-none"
              placeholder="You are a helpful customer service assistant. Respond professionally and helpfully to customer inquiries..."
              @input="updateNodeData"
            />
            <p class="text-xs text-gray-500 mt-1">
              Define how the AI should behave and respond. Use variables like {{contact_name}}, {{contact_phone}}, etc.
            </p>
          </div>

          <!-- Advanced Settings -->
          <div class="border-t border-gray-200 dark:border-gray-600 pt-3">
            <details class="group">
              <summary class="cursor-pointer text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-blue-600">
                Advanced Settings
              </summary>
              <div class="mt-3 space-y-3">
                <!-- Temperature -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Temperature (Creativity): {{ nodeData.temperature }}
                  </label>
                  <input
                    v-model.number="nodeData.temperature"
                    type="range"
                    min="0"
                    max="1"
                    step="0.1"
                    class="w-full"
                    @input="updateNodeData"
                  />
                  <div class="flex justify-between text-xs text-gray-500">
                    <span>Focused (0)</span>
                    <span>Creative (1)</span>
                  </div>
                </div>

                <!-- Max Tokens -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Max Response Length
                  </label>
                  <input
                    v-model.number="nodeData.maxTokens"
                    type="number"
                    min="50"
                    max="4000"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                    @input="updateNodeData"
                  />
                  <p class="text-xs text-gray-500 mt-1">
                    Maximum tokens for AI response (50-4000)
                  </p>
                </div>
              </div>
            </details>
          </div>

          <!-- Status Indicator -->
          <div class="flex items-center space-x-2 text-sm">
            <div class="flex items-center">
              <div :class="aiEnabled ? 'bg-green-500' : 'bg-red-500'" class="w-2 h-2 rounded-full mr-2"></div>
              <span :class="aiEnabled ? 'text-green-600' : 'text-red-600'">
                {{ aiEnabled ? 'AI Enabled' : 'AI Disabled' }}
              </span>
            </div>
          </div>

          <!-- Warning if AI not enabled -->
          <div v-if="!aiEnabled" class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-3">
            <p class="text-sm text-yellow-800 dark:text-yellow-200">
              <strong>Warning:</strong> AI Assistant module is not enabled. This node will not function until AI integration is configured in settings.
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
        <Handle
          id="output"
          type="source"
          position="right"
          class="w-3 h-3 !bg-yellow-500"
        />
      </template>
    </NodeWrapper>
  </div>
</template>

<script setup>
import { reactive, computed, watch } from 'vue'
import { Handle, Position } from '@vue-flow/core'
import NodeWrapper from '../ui/NodeWrapper.vue'
import { FlFilledLightbulbFilament } from '@kalimahapps/vue-icons'

const props = defineProps({
  id: String,
  data: Object,
  selected: Boolean,
})

const emit = defineEmits(['update-node'])

// Check if AI is enabled (from global window variable)
const aiEnabled = computed(() => {
  return window.isAiAssistantModuleEnabled || false
})

// Initialize node data with default structure
const nodeData = reactive({
  aiModel: props.data?.aiModel || 'gpt-3.5-turbo',
  contextType: props.data?.contextType || 'message',
  prompt: props.data?.prompt || 'You are a helpful customer service assistant. Respond professionally and helpfully to customer inquiries. Keep responses concise and relevant.',
  temperature: props.data?.temperature || 0.7,
  maxTokens: props.data?.maxTokens || 500,
})

// Update node data when changes occur
const updateNodeData = () => {
  emit('update-node', {
    id: props.id,
    data: { ...nodeData }
  })
}

// Watch for external data changes
watch(() => props.data, (newData) => {
  if (newData) {
    Object.assign(nodeData, {
      aiModel: newData.aiModel || 'gpt-3.5-turbo',
      contextType: newData.contextType || 'message',
      prompt: newData.prompt || nodeData.prompt,
      temperature: newData.temperature || 0.7,
      maxTokens: newData.maxTokens || 500,
    })
  }
}, { deep: true })
</script>

<style scoped>
.ai-assistant-node {
  min-width: 350px;
}

details[open] summary {
  margin-bottom: 0.75rem;
}
</style>
