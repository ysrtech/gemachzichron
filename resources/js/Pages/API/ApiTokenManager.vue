<template>
    <div>
        <!-- Generate API Token -->
        <app-form-section @submitted="createApiToken">
            <template #title>
                Create API Token
            </template>

            <template #description>
                API tokens allow third-party services to authenticate with our application on your behalf.
            </template>

            <template #form>
                <!-- Token Name -->
                <div class="col-span-6 sm:col-span-4">
                    <app-label for="name" value="Name" />
                    <app-input id="name" type="text" class="mt-1 block w-full" v-model="createApiTokenForm.name" autofocus />
                    <app-input-error :message="createApiTokenForm.error('name')" class="mt-2" />
                </div>

                <!-- Token Permissions -->
                <div class="col-span-6" v-if="availablePermissions.length > 0">
                    <app-label for="permissions" value="Permissions" />

                    <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div v-for="permission in availablePermissions">
                            <label class="flex items-center">
                                <input type="checkbox" class="form-checkbox" :value="permission" v-model="createApiTokenForm.permissions">
                                <span class="ml-2 text-sm text-gray-600">{{ permission }}</span>
                            </label>
                        </div>
                    </div>
                </div>
            </template>

            <template #actions>
                <app-action-message :on="createApiTokenForm.recentlySuccessful" class="mr-3">
                    Created.
                </app-action-message>

                <app-button type="submit" :processing="createApiTokenForm.processing">
                    Create
                </app-button>
            </template>
        </app-form-section>

        <div v-if="tokens.length > 0">
            <app-section-border />

            <!-- Manage API Tokens -->
            <div class="mt-10 sm:mt-0">
                <app-action-section>
                    <template #title>
                        Manage API Tokens
                    </template>

                    <template #description>
                        You may delete any of your existing tokens if they are no longer needed.
                    </template>

                    <!-- API Token List -->
                    <template #content>
                        <div class="space-y-6">
                            <div class="flex items-center justify-between" v-for="token in tokens">
                                <div>
                                    {{ token.name }}
                                </div>

                                <div class="flex items-center">
                                    <div class="text-sm text-gray-400" v-if="token.last_used_at">
                                        Last used {{ fromNow(token.last_used_at) }}
                                    </div>

                                    <button class="cursor-pointer ml-6 text-sm text-gray-400 underline focus:outline-none"
                                                @click="manageApiTokenPermissions(token)"
                                                v-if="availablePermissions.length > 0">
                                        Permissions
                                    </button>

                                    <button class="cursor-pointer ml-6 text-sm text-red-500 focus:outline-none" @click="confirmApiTokenDeletion(token)">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </app-action-section>
            </div>
        </div>

        <!-- Token Value Modal -->
        <app-dialog-modal :show="displayingToken" @close="displayingToken = false">
            <template #title>
                API Token
            </template>

            <template #content>
                <div>
                    Please copy your new API token. For your security, it won't be shown again.
                </div>

                <div class="mt-4 bg-gray-100 px-4 py-2 rounded font-mono text-sm text-gray-500" v-if="$page.props.jetstream.flash.token">
                    {{ $page.props.jetstream.flash.token }}
                </div>
            </template>

            <template #footer>
                <app-button color="secondary" @click.native="displayingToken = false">
                    Close
                </app-button>
            </template>
        </app-dialog-modal>

        <!-- API Token Permissions Modal -->
        <app-dialog-modal :show="managingPermissionsFor" @close="managingPermissionsFor = null">
            <template #title>
                API Token Permissions
            </template>

            <template #content>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div v-for="permission in availablePermissions">
                        <label class="flex items-center">
                            <input type="checkbox" class="form-checkbox" :value="permission" v-model="updateApiTokenForm.permissions">
                            <span class="ml-2 text-sm text-gray-600">{{ permission }}</span>
                        </label>
                    </div>
                </div>
            </template>

            <template #footer>
                <app-button color="secondary" @click.native="managingPermissionsFor = null">
                  Cancel
                </app-button>

                <app-button type="submit" class="ml-2" @click.native="updateApiToken" :processing="updateApiTokenForm.processing">
                    Save
                </app-button>
            </template>
        </app-dialog-modal>

        <!-- Delete Token Confirmation Modal -->
        <app-confirmation-modal :show="apiTokenBeingDeleted" @close="apiTokenBeingDeleted = null">
            <template #title>
                Delete API Token
            </template>

            <template #content>
                Are you sure you would like to delete this API token?
            </template>

            <template #footer>
                <app-button color="secondary" @click.native="apiTokenBeingDeleted = null">
                  Cancel
                </app-button>

                <app-button color="danger" class="ml-2" @click.native="deleteApiToken" :processing="deleteApiTokenForm.processing">
                    Delete
                </app-button>
            </template>
        </app-confirmation-modal>
    </div>
</template>

<script>
    import AppActionMessage from '../../Shared/ActionMessage'
    import AppActionSection from '../../Shared/ActionSection'
    import AppButton from '../../Shared/Button'
    import AppConfirmationModal from '../../Shared/ConfirmationModal'
    import AppDialogModal from '../../Shared/DialogModal'
    import AppFormSection from '../../Shared/FormSection'
    import AppInput from '../../Shared/Input'
    import AppInputError from '../../Shared/InputError'
    import AppLabel from '../../Shared/Label'
    import AppSectionBorder from '../../Shared/SectionBorder'

    export default {
        components: {
            AppActionMessage,
            AppActionSection,
            AppButton,
            AppConfirmationModal,
            AppDialogModal,
            AppFormSection,
            AppInput,
            AppInputError,
            AppLabel,
            AppSectionBorder,
        },

        props: [
            'tokens',
            'availablePermissions',
            'defaultPermissions',
        ],

        data() {
            return {
                createApiTokenForm: this.$inertia.form({
                    name: '',
                    permissions: this.defaultPermissions,
                }, {
                    bag: 'createApiToken',
                    resetOnSuccess: true,
                }),

                updateApiTokenForm: this.$inertia.form({
                    permissions: []
                }, {
                    resetOnSuccess: false,
                    bag: 'updateApiToken',
                }),

                deleteApiTokenForm: this.$inertia.form(),

                displayingToken: false,
                managingPermissionsFor: null,
                apiTokenBeingDeleted: null,
            }
        },

        methods: {
            createApiToken() {
              this.createApiTokenForm.post(route('api-tokens.store'), {
                preserveScroll: true,
                onSuccess: () => {
                  if (! this.createApiTokenForm.hasErrors()) {
                    this.displayingToken = true
                  }
                }
              })
            },

            manageApiTokenPermissions(token) {
                this.updateApiTokenForm.permissions = token.abilities

                this.managingPermissionsFor = token
            },

            updateApiToken() {
              this.updateApiTokenForm.put(route('api-tokens.update', this.managingPermissionsFor), {
                preserveScroll: true,
                preserveState: true,
                onSuccess: () => {
                  this.managingPermissionsFor = null
                }
              })
            },

            confirmApiTokenDeletion(token) {
                this.apiTokenBeingDeleted = token
            },

            deleteApiToken() {
              this.deleteApiTokenForm.delete(route('api-tokens.destroy', this.apiTokenBeingDeleted), {
                preserveScroll: true,
                preserveState: true,
                onSuccess: () => {
                  this.apiTokenBeingDeleted = null
                }
              })
            },

            fromNow(timestamp) {
                return moment(timestamp).local().fromNow()
            },
        },
    }
</script>
