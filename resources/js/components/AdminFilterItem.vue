<template>
  <div class="flex flex-col">
    <label :for="id" class="block text-sm font-medium text-gray-700 mb-1">{{ label }}</label>
    
    <!-- Input text -->
    <input
      v-if="type === 'text'"
      :id="id"
      v-model="modelValue"
      type="text"
      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
      :placeholder="placeholder"
    />
    
    <!-- Select -->
    <select
      v-else-if="type === 'select'"
      :id="id"
      v-model="modelValue"
      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
    >
      <option v-if="placeholder" value="">{{ placeholder }}</option>
      <option v-for="option in options" :key="option.value" :value="option.value">
        {{ option.label }}
      </option>
    </select>
    
    <!-- Date -->
    <input
      v-else-if="type === 'date'"
      :id="id"
      v-model="modelValue"
      type="date"
      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
    />
    
    <!-- Number -->
    <input
      v-else-if="type === 'number'"
      :id="id"
      v-model="modelValue"
      type="number"
      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
      :placeholder="placeholder"
      :min="min"
      :max="max"
      :step="step"
    />
    
    <!-- Checkbox -->
    <div v-else-if="type === 'checkbox'" class="flex items-center h-full">
      <input
        :id="id"
        v-model="modelValue"
        type="checkbox"
        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
      />
      <label :for="id" class="ml-2 block text-sm text-gray-900">{{ checkboxLabel || label }}</label>
    </div>
    
    <!-- Radio group -->
    <div v-else-if="type === 'radio'" class="flex flex-col space-y-2">
      <div v-for="option in options" :key="option.value" class="flex items-center">
        <input
          :id="`${id}-${option.value}`"
          v-model="modelValue"
          type="radio"
          :value="option.value"
          class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
        />
        <label :for="`${id}-${option.value}`" class="ml-2 block text-sm text-gray-900">
          {{ option.label }}
        </label>
      </div>
    </div>
    
    <!-- Default input -->
    <input
      v-else
      :id="id"
      v-model="modelValue"
      :type="type"
      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
      :placeholder="placeholder"
    />
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  modelValue: {
    type: [String, Number, Boolean, Array],
    default: ''
  },
  id: {
    type: String,
    required: true
  },
  label: {
    type: String,
    required: true
  },
  type: {
    type: String,
    default: 'text',
    validator: (value) => ['text', 'select', 'date', 'number', 'checkbox', 'radio', 'email', 'tel', 'password'].includes(value)
  },
  placeholder: {
    type: String,
    default: ''
  },
  options: {
    type: Array,
    default: () => []
  },
  min: {
    type: [Number, String],
    default: null
  },
  max: {
    type: [Number, String],
    default: null
  },
  step: {
    type: [Number, String],
    default: null
  },
  checkboxLabel: {
    type: String,
    default: ''
  }
})

const emit = defineEmits(['update:modelValue'])

const modelValue = computed({
  get() {
    return props.modelValue
  },
  set(value) {
    emit('update:modelValue', value)
  }
})
</script> 