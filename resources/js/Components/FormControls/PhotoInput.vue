<template>
  <div>
    <div class="flex justify-between items-baseline">
      <app-label v-show="label" :id="$attrs.id" :name="label"/>
      <slot name="top-right"/>
    </div>

      <label :for="$attrs.id">
        <div class="mt-1 form-control relative sm:p-0 overflow-hidden" :class="{'border-red-600': error}">
          <img :src="imagePreview" alt="" :id="`${$attrs.id}_preview`">
          <div class="inset-0 bg-gray-900 border-white absolute opacity-0 text-white flex items-center justify-center bg-opacity-0 hover:bg-opacity-40 hover:opacity-100">
            <div class="material-icons-outlined cursor-pointer" title="Select Image">
              add_a_photo
            </div>
          </div>
        </div>

      </label>
      <input
        v-bind="$attrs"
        :id="$attrs.id"
        class="hidden"
        type="file"
        @change="$emit('update:modelValue', $event.target.files[0])"
      />

    <app-errors :error="error"/>
  </div>
</template>

<script>

import AppLabel from "@/Components/FormControls/Label";
import AppErrors from "@/Components/FormControls/Errors";

export default {
  name: "AppPhotoInput",

  inheritAttrs: false,

  emits: ['update:modelValue'],

  components: {
    AppErrors,
    AppLabel,
  },

  props: {
    modelValue: {
      default: null
    },
    label: {
      type: String
    },
    error: {
      type: String,
    },
    defaultImagePreview: {
      type: String,
      default: 'data:image/png;base64,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'
      // default: 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" height="100px" width="100%" fill="%23848484" viewBox="0 0 100 100"><path d="M96,17H4V83H96Zm-4,4V69.59L72.37,49.44,58.94,61,36.86,43.72,8,72V21ZM8,79V77.58L37.15,49,59.06,66.16,72.13,54.93,92,75.32V79Z"/><path d="M78.33,45a10,10,0,1,0-10-10A10,10,0,0,0,78.33,45Zm0-16a6,6,0,1,1-6,6A6,6,0,0,1,78.33,29Z"/></svg>'
    },
  },
  data() {
    return {
      imagePreview: null,
    }
  },

  watch: {
    modelValue: {
      handler(val) {
        if (val instanceof File) {
          const reader = new FileReader();

          reader.onload = (e) => {
            this.imagePreview = e.target.result
          };

          reader.readAsDataURL(val);
        } else {
          this.imagePreview = val || this.defaultImagePreview
        }
      },
      immediate: true
    }
  },
}
</script>
