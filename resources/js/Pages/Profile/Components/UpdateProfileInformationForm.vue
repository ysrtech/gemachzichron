<template>
    <app-form-section @submitted="updateProfileInformation">
        <template #title>
            Profile Information
        </template>

        <template #description>
            Update your account's profile information and email address.
        </template>

        <template #form>
            <!-- Profile Photo -->
            <div class="col-span-6 sm:col-span-4" v-if="$page.props.jetstream.managesProfilePhotos">
                <!-- Profile Photo File Input -->
                <input type="file" class="hidden"
                            ref="photo"
                            @change="updatePhotoPreview">

                <app-label for="photo" value="Photo" />

                <!-- Current Profile Photo -->
                <div class="mt-2" v-show="! photoPreview">
                    <img :src="user.profile_photo_url" alt="Current Profile Photo" class="rounded-full h-20 w-20 object-cover">
                </div>

                <!-- New Profile Photo Preview -->
                <div class="mt-2" v-show="photoPreview">
                    <span class="block rounded-full w-20 h-20"
                          :style="'background-size: cover; background-repeat: no-repeat; background-position: center center; background-image: url(\'' + photoPreview + '\');'">
                    </span>
                </div>

                <app-button color="secondary" class="mt-2 mr-2" type="button" @click.native.prevent="selectNewPhoto">
                    Select A New Photo
                </app-button>

                <app-button color="secondary" type="button" class="mt-2" @click.native.prevent="deletePhoto" v-if="user.profile_photo_path">
                    Remove Photo
                </app-button>

                <app-input-error :message="form.error('photo')" class="mt-2" />
            </div>

            <!-- Name -->
            <div class="col-span-6 sm:col-span-4">
                <app-label for="name" value="Name" />
                <app-input id="name" type="text" class="mt-1 block w-full" v-model="form.name" autocomplete="name" />
                <app-input-error :message="form.error('name')" class="mt-2" />
            </div>

            <!-- Email -->
            <div class="col-span-6 sm:col-span-4">
                <app-label for="email" value="Email" />
                <app-input id="email" type="email" class="mt-1 block w-full" v-model="form.email" />
                <app-input-error :message="form.error('email')" class="mt-2" />
            </div>
        </template>

        <template #actions>
            <app-action-message :on="form.recentlySuccessful" class="mr-3">
                Saved.
            </app-action-message>

            <app-button type="submit" :processing="form.processing">
                Save
            </app-button>
        </template>
    </app-form-section>
</template>

<script>
    import AppButton from '../../../Shared/Button'
    import AppFormSection from '../../../Shared/FormSection'
    import AppInput from '../../../Shared/Input'
    import AppInputError from '../../../Shared/InputError'
    import AppLabel from '../../../Shared/Label'
    import AppActionMessage from '../../../Shared/ActionMessage'

    export default {
        components: {
            AppActionMessage,
            AppButton,
            AppFormSection,
            AppInput,
            AppInputError,
            AppLabel,
        },

        props: ['user'],

        data() {
            return {
                form: this.$inertia.form({
                    '_method': 'PUT',
                    name: this.user.name,
                    email: this.user.email,
                    photo: null,
                }, {
                    bag: 'updateProfileInformation',
                    resetOnSuccess: false,
                }),

                photoPreview: null,
            }
        },

        methods: {
            updateProfileInformation() {
                if (this.$refs.photo) {
                    this.form.photo = this.$refs.photo.files[0]
                }

                this.form.post(this.$route('user-profile-information.update'), {
                    preserveScroll: true
                });
            },

            selectNewPhoto() {
                this.$refs.photo.click();
            },

            updatePhotoPreview() {
                const reader = new FileReader();

                reader.onload = (e) => {
                    this.photoPreview = e.target.result;
                };

                reader.readAsDataURL(this.$refs.photo.files[0]);
            },

            deletePhoto() {
                this.$inertia.delete(this.$route('current-user-photo.destroy'), {
                  preserveScroll: true,
                  onSuccess: () => {
                    this.photoPreview = null
                  }
                });
            },
        },
    }
</script>
