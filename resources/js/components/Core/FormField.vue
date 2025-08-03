<template>
  <div class="form-field" :class="{ 'has-error': !!error }">
    <label v-if="label" :for="fieldId" class="block text-sm font-medium mb-1" :class="labelClass">
      {{ label }}
      <span v-if="required" class="text-red-500">*</span>
    </label>
    
    <!-- Input text, email, password, number, tel -->
    <input
      v-if="['text', 'email', 'password', 'number', 'tel', 'date', 'datetime-local'].includes(type)"
      :id="fieldId"
      :name="name"
      :type="type"
      :value="modelValue"
      @input="updateValue($event.target.value)"
      :placeholder="placeholder"
      :disabled="disabled"
      :maxlength="maxlength"
      :min="min"
      :max="max"
      :autocomplete="autocomplete"
      :class="[
        'w-full px-4 py-2 border rounded-xl',
        error ? 'border-red-500' : 'border-gray-300',
        disabled ? 'bg-gray-100 cursor-not-allowed' : 'bg-white',
        inputClass
      ]"
    />
    
    <!-- Textarea -->
    <textarea
      v-else-if="type === 'textarea'"
      :id="fieldId"
      :name="name"
      :value="modelValue"
      @input="updateValue($event.target.value)"
      :placeholder="placeholder"
      :disabled="disabled"
      :maxlength="maxlength"
      :rows="rows || 3"
      :class="[
        'w-full px-4 py-2 border rounded-xl',
        error ? 'border-red-500' : 'border-gray-300',
        disabled ? 'bg-gray-100 cursor-not-allowed' : 'bg-white',
        inputClass
      ]"
    ></textarea>
    
    <!-- Select -->
    <select
      v-else-if="type === 'select'"
      :id="fieldId"
      :name="name"
      :value="modelValue"
      @change="updateValue($event)"
      :disabled="disabled"
      :multiple="multiple"
      :class="[
        'w-full px-4 py-2 border rounded-xl',
        error ? 'border-red-500' : 'border-gray-300',
        disabled ? 'bg-gray-100 cursor-not-allowed' : 'bg-white',
        multiple ? 'min-h-[120px]' : '',
        inputClass
      ]"
    >
      <option v-if="placeholder && !multiple" value="">{{ placeholder }}</option>
      <option
        v-for="option in options"
        :key="option.value"
        :value="option.value"
        :selected="multiple && Array.isArray(modelValue) && modelValue.includes(option.value)"
      >
        {{ option.label }}
      </option>
    </select>
    
    <!-- Checkbox -->
    <div v-else-if="type === 'checkbox'" class="flex items-center">
      <input
        :id="fieldId"
        :name="name"
        type="checkbox"
        :checked="!!modelValue"
        @change="updateValue($event.target.checked)"
        :disabled="disabled"
        class="h-4 w-4 text-blue-600 border-gray-300 rounded"
      />
      <label :for="fieldId" class="ml-2 block text-sm text-gray-700">
        {{ checkboxLabel || label }}
      </label>
    </div>
    
    <!-- Radio group -->
    <div v-else-if="type === 'radio'" class="space-y-2">
      <div 
        v-for="option in options" 
        :key="option.value"
        class="flex items-center"
      >
        <input
          :id="`${fieldId}-${option.value}`"
          :name="name"
          type="radio"
          :value="option.value"
          :checked="modelValue === option.value"
          @change="updateValue(option.value)"
          :disabled="disabled"
          class="h-4 w-4 text-blue-600 border-gray-300"
        />
        <label :for="`${fieldId}-${option.value}`" class="ml-2 block text-sm text-gray-700">
          {{ option.label }}
        </label>
      </div>
    </div>
    
    <!-- Custom slot -->
    <slot v-else :error="error" :value="modelValue" :update-value="updateValue"></slot>
    
    <!-- Help text -->
    <div v-if="helpText" class="mt-1 text-sm text-gray-500">
      {{ helpText }}
    </div>
    
    <!-- Error message -->
    <div v-if="error" class="text-red-500 text-sm mt-1">{{ error }}</div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  // Field props
  modelValue: {
    type: [String, Number, Boolean, Array, Object],
    default: ''
  },
  label: {
    type: String,
    default: ''
  },
  type: {
    type: String,
    default: 'text'
  },
  name: {
    type: String,
    default: ''
  },
  id: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: ''
  },
  required: {
    type: Boolean,
    default: false
  },
  disabled: {
    type: Boolean,
    default: false
  },
  error: {
    type: String,
    default: ''
  },
  helpText: {
    type: String,
    default: ''
  },
  // Input specific props
  maxlength: {
    type: [Number, String],
    default: undefined
  },
  min: {
    type: [Number, String],
    default: undefined
  },
  max: {
    type: [Number, String],
    default: undefined
  },
  // Textarea specific props
  rows: {
    type: [Number, String],
    default: 3
  },
  // Select and Radio specific props
  options: {
    type: Array,
    default: () => []
  },
  multiple: {
    type: Boolean,
    default: false
  },
  // Checkbox specific props
  checkboxLabel: {
    type: String,
    default: ''
  },
  // Styling
  labelClass: {
    type: String,
    default: ''
  },
  inputClass: {
    type: String,
    default: ''
  },
  // Autocomplete
  autocomplete: {
    type: String,
    default: undefined
  }
})

const emit = defineEmits(['update:modelValue'])

// Generate ID if not provided
const fieldId = computed(() => {
  return props.id || `field-${props.name || Math.random().toString(36).substring(2, 9)}`
})

// Update value and emit event
function updateValue(event) {
  if (props.multiple && props.type === 'select') {
    // For multiple select, we need to handle the selected options
    const select = event.target
    const selectedValues = []
    for (let i = 0; i < select.options.length; i++) {
      if (select.options[i].selected) {
        selectedValues.push(select.options[i].value)
      }
    }
    emit('update:modelValue', selectedValues)
  } else {
    emit('update:modelValue', event.target.value)
  }
}
</script> 
