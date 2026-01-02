<template>
  <div v-if="show" class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="close"></div>
      <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-6xl sm:w-full">
        <form @submit.prevent="submit">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-lg font-medium text-gray-900" id="modal-title">
                Edit Email Template
              </h3>
              <button type="button" @click="close" class="text-gray-400 hover:text-gray-500">
                <i class="material-icons-outlined">close</i>
              </button>
            </div>

            <div v-if="template" class="space-y-4">
              <div>
                <div class="text-sm font-medium text-gray-700 mb-1">{{ template.title }}</div>
                <div class="text-xs text-gray-500">{{ template.filename }}</div>
              </div>

              <div v-if="loadingContent" class="text-center py-8">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary-700"></div>
                <p class="mt-2 text-gray-600">Loading template...</p>
              </div>

              <div v-else>
                <!-- Tabs -->
                <div class="border-b border-gray-200 mb-4">
                  <nav class="-mb-px flex space-x-8">
                    <button
                      type="button"
                      @click="activeTab = 'edit'"
                      :class="[
                        activeTab === 'edit'
                          ? 'border-primary-500 text-primary-600'
                          : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                        'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm'
                      ]">
                      <i class="material-icons-outlined text-base align-middle mr-1">code</i>
                      Edit
                    </button>
                    <button
                      type="button"
                      @click="activeTab = 'preview'; loadPreview()"
                      :class="[
                        activeTab === 'preview'
                          ? 'border-primary-500 text-primary-600'
                          : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                        'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm'
                      ]">
                      <i class="material-icons-outlined text-base align-middle mr-1">visibility</i>
                      Preview
                    </button>
                  </nav>
                </div>

                <!-- Edit Tab -->
                <div v-show="activeTab === 'edit'">
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Template Content
                  </label>
                  
                  <!-- Toolbar -->
                  <div class="bg-gray-50 border border-gray-300 border-b-0 rounded-t-md px-3 py-2 flex items-center space-x-2">
                    <button
                      type="button"
                      @click="undo"
                      title="Undo"
                      class="p-1.5 text-gray-600 hover:text-gray-900 hover:bg-gray-200 rounded">
                      <i class="material-icons-outlined text-base">undo</i>
                    </button>
                    <button
                      type="button"
                      @click="redo"
                      title="Redo"
                      class="p-1.5 text-gray-600 hover:text-gray-900 hover:bg-gray-200 rounded">
                      <i class="material-icons-outlined text-base">redo</i>
                    </button>
                    <div class="border-l border-gray-300 h-6"></div>
                    <button
                      type="button"
                      @click="formatCode"
                      title="Format HTML"
                      class="p-1.5 text-gray-600 hover:text-gray-900 hover:bg-gray-200 rounded">
                      <i class="material-icons-outlined text-base">auto_fix_high</i>
                    </button>
                    <button
                      type="button"
                      @click="openSearch"
                      title="Find/Replace"
                      class="p-1.5 text-gray-600 hover:text-gray-900 hover:bg-gray-200 rounded">
                      <i class="material-icons-outlined text-base">search</i>
                    </button>
                    <div class="border-l border-gray-300 h-6"></div>
                    <button
                      type="button"
                      @click="insertSnippet('{{ }}')"
                      title="Insert Variable"
                      class="px-2 py-1 text-xs font-mono text-gray-600 hover:text-gray-900 hover:bg-gray-200 rounded"
                      v-text="'{{ }}'">
                    </button>
                    <button
                      type="button"
                      @click="insertSnippet('@if()\n\n@endif')"
                      title="Insert If Statement"
                      class="px-2 py-1 text-xs font-mono text-gray-600 hover:text-gray-900 hover:bg-gray-200 rounded">
                      @if
                    </button>
                    <button
                      type="button"
                      @click="insertSnippet('@foreach()\n\n@endforeach')"
                      title="Insert Foreach Loop"
                      class="px-2 py-1 text-xs font-mono text-gray-600 hover:text-gray-900 hover:bg-gray-200 rounded">
                      @foreach
                    </button>
                  </div>
                  
                  <div ref="editorContainer" class="border border-gray-300 rounded-b-md"></div>
                  <p class="mt-1 text-xs text-gray-500">
                    You can use Blade syntax. A backup will be created automatically before saving.
                  </p>
                </div>

                <!-- Preview Tab -->
                <div v-show="activeTab === 'preview'">
                  <div v-if="loadingPreview" class="text-center py-8">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary-700"></div>
                    <p class="mt-2 text-gray-600">Rendering preview...</p>
                  </div>
                  <div v-else-if="previewError" class="bg-red-50 border border-red-200 rounded-md p-4">
                    <p class="text-sm text-red-800">{{ previewError }}</p>
                  </div>
                  <iframe
                    v-else
                    :srcdoc="previewHtml"
                    class="w-full border border-gray-300 rounded-md"
                    style="min-height: 600px;"
                  ></iframe>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              type="submit"
              :disabled="saving || !form.content"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed">
              <span v-if="!saving">Save Template</span>
              <span v-else class="flex items-center">
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Saving...
              </span>
            </button>
            <button
              type="button"
              @click="close"
              :disabled="saving"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed">
              Cancel
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { EditorView, basicSetup } from 'codemirror';
import { html } from '@codemirror/lang-html';
import { undo, redo } from '@codemirror/commands';
import { openSearchPanel } from '@codemirror/search';
import { html_beautify } from 'js-beautify';

export default {
  props: {
    show: {
      type: Boolean,
      default: false
    },
    template: {
      type: Object,
      default: null
    }
  },

  data() {
    return {
      form: {
        content: ''
      },
      loadingContent: false,
      saving: false,
      activeTab: 'edit',
      previewHtml: '',
      loadingPreview: false,
      previewError: null,
      editor: null,
    };
  },

  watch: {
    show(newValue) {
      if (newValue && this.template) {
        this.loadTemplate();
      } else {
        this.form.content = '';
        this.activeTab = 'edit';
        this.previewHtml = '';
        if (this.editor) {
          this.editor.destroy();
          this.editor = null;
        }
      }
    }
  },

  methods: {
    async loadTemplate() {
      this.loadingContent = true;
      try {
        const response = await this.$axios.get(`/settings/email-templates/${this.template.name}`);
        this.form.content = response.data.content;
        
        // Initialize CodeMirror after content is loaded
        this.$nextTick(() => {
          this.initEditor();
        });
      } catch (error) {
        console.error('Error loading template:', error);
        alert('Failed to load template content');
      } finally {
        this.loadingContent = false;
      }
    },

    initEditor() {
      if (this.editor) {
        this.editor.destroy();
      }

      this.editor = new EditorView({
        doc: this.form.content,
        extensions: [
          basicSetup,
          html(),
          EditorView.lineWrapping,
          EditorView.updateListener.of((update) => {
            if (update.docChanged) {
              this.form.content = update.state.doc.toString();
            }
          }),
          EditorView.theme({
            "&": { height: "500px" },
            ".cm-scroller": { overflow: "auto" }
          })
        ],
        parent: this.$refs.editorContainer
      });
    },

    undo() {
      if (this.editor) {
        undo(this.editor);
      }
    },

    redo() {
      if (this.editor) {
        redo(this.editor);
      }
    },

    openSearch() {
      if (this.editor) {
        openSearchPanel(this.editor);
      }
    },

    formatCode() {
      if (this.editor) {
        try {
          const currentContent = this.editor.state.doc.toString();
          const formatted = html_beautify(currentContent, {
            indent_size: 4,
            indent_char: ' ',
            max_preserve_newlines: 2,
            preserve_newlines: true,
            wrap_line_length: 120,
            wrap_attributes: 'auto',
            end_with_newline: true
          });
          
          // Replace the entire document content
          this.editor.dispatch({
            changes: {
              from: 0,
              to: this.editor.state.doc.length,
              insert: formatted
            }
          });
        } catch (error) {
          console.error('Error formatting code:', error);
          alert('Failed to format HTML');
        }
      }
    },

    insertSnippet(snippet) {
      if (this.editor) {
        const cursorPos = this.editor.state.selection.main.head;
        this.editor.dispatch({
          changes: { from: cursorPos, insert: snippet },
          selection: { anchor: cursorPos + snippet.indexOf(')') }
        });
        this.editor.focus();
      }
    },

    async loadPreview() {
      if (this.previewHtml && this.activeTab === 'preview') {
        return; // Already loaded
      }

      this.loadingPreview = true;
      this.previewError = null;
      
      try {
        const response = await this.$axios.post(`/settings/email-templates/${this.template.name}/preview`, {
          content: this.form.content
        });
        this.previewHtml = response.data.html;
      } catch (error) {
        console.error('Error loading preview:', error);
        this.previewError = error.response?.data?.message || 'Failed to render preview';
      } finally {
        this.loadingPreview = false;
      }
    },

    async submit() {
      this.saving = true;
      try {
        await this.$axios.put(`/settings/email-templates/${this.template.name}`, {
          content: this.form.content
        });
        
        alert('Template saved successfully! A backup has been created.');
        this.$emit('close');
        
        // Reload the page to refresh templates
        this.$inertia.reload({ only: [] });
      } catch (error) {
        console.error('Error saving template:', error);
        alert(error.response?.data?.message || 'Failed to save template');
      } finally {
        this.saving = false;
      }
    },

    close() {
      if (!this.saving) {
        this.$emit('close');
      }
    }
  },

  beforeUnmount() {
    if (this.editor) {
      this.editor.destroy();
    }
  }
};
</script>

<style scoped>
.cm-editor {
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
  font-size: 13px;
}

.cm-focused {
  outline: 2px solid #3b82f6;
  outline-offset: 2px;
}
</style>
