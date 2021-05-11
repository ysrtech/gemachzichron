<template>
  <div class="rounded-md bg-white shadow overflow-hidden max-w-4xl mx-auto">
    <h3 class="text-lg font-medium text-gray-900 p-6">Member Details</h3>
    <form @submit.prevent="submit">
      <div class="px-6 py-4 grid sm:grid-cols-12 gap-6">

        <div class="col-span-2">
          <app-input
            id="title"
            v-model="form.title"
            :error="form.errors.title"
            label="Title"
            type="select"
            @input="form.clearErrors('title')">
            <template #options>
              <option v-for="title in ['Rabbi & Mrs.', 'Mr. & Mrs.', 'Rabbi & Reb.', 'Rabbi', 'Mr.', 'Mrs.', 'Reb.']">
                {{ title }}
              </option>
            </template>
          </app-input>
        </div>

        <div class="col-span-4">
          <app-input
            id="first_name"
            v-model="form.first_name"
            :error="form.errors.first_name"
            label="First Name"
            type="text"
            @input="form.clearErrors('first_name')"
          />
        </div>

        <div class="col-span-4">
          <app-input
            id="last_name"
            v-model="form.last_name"
            :error="form.errors.last_name"
            label="Last Name"
            type="text"
            @input="form.clearErrors('last_name')"
          />
        </div>

        <div class="col-span-4 col-start-1">
          <app-input
            id="hebrew_first_name"
            v-model="form.hebrew_first_name"
            :error="form.errors.hebrew_first_name"
            label="Hebrew First Name"
            type="text"
            @input="form.clearErrors('hebrew_first_name')"
          />
        </div>

        <div class="col-span-4">
          <app-input
            id="hebrew_last_name"
            v-model="form.hebrew_last_name"
            :error="form.errors.hebrew_last_name"
            label="Hebrew Last Name"
            type="text"
            @input="form.clearErrors('hebrew_last_name')"
          />
        </div>

        <div class="col-span-4 col-start-1">
          <app-input
            id="wife_name"
            v-model="form.wife_name"
            :error="form.errors.wife_name"
            label="Wife's Name"
            type="text"
            @input="form.clearErrors('wife_name')"
          />
        </div>

        <div class="col-span-5 col-start-1">
          <app-input
            id="address"
            v-model="form.address"
            :error="form.errors.address"
            label="Address"
            type="text"
            @input="form.clearErrors('address')"
          />
        </div>

        <div class="col-span-3">
          <app-input
            id="city"
            v-model="form.city"
            :error="form.errors.city"
            label="City"
            type="text"
            @input="form.clearErrors('city')"
          />
        </div>

        <div class="col-span-3">
          <app-input
            id="postal_code"
            v-model="form.postal_code"
            :error="form.errors.postal_code"
            label="Postal Code"
            type="text"
            @input="form.clearErrors('postal_code')"
          />
        </div>

        <div class="col-span-4 col-start-1">
          <app-input
            id="email"
            v-model="form.email"
            :error="form.errors.email"
            label="Email"
            type="email"
            @input="form.clearErrors('email')"
          />
        </div>

        <div class="col-span-3 col-start-1">
          <app-input
            id="home_phone"
            v-model="form.home_phone"
            :error="form.errors.home_phone"
            label="Home Phone"
            type="text"
            @input="form.clearErrors('home_phone')"
          />
        </div>

        <div class="col-span-3">
          <app-input
            id="mobile_phone"
            v-model="form.mobile_phone"
            :error="form.errors.mobile_phone"
            label="Cellphone"
            type="text"
            @input="form.clearErrors('mobile_phone')"
          />
        </div>

        <div class="col-span-3">
          <app-input
            id="wife_mobile_phone"
            v-model="form.wife_mobile_phone"
            :error="form.errors.wife_mobile_phone"
            label="Wife's Cellphone"
            type="text"
            @input="form.clearErrors('wife_mobile_phone')"
          />
        </div>

        <div class="col-span-4 col-start-1">
          <app-input
            id="father"
            v-model="form.father"
            :error="form.errors.father"
            label="Father"
            type="text"
            @input="form.clearErrors('father')"
          />
        </div>

        <div class="col-span-4">
          <app-input
            id="father_in_law"
            v-model="form.father_in_law"
            :error="form.errors.father_in_law"
            label="Father In Law"
            type="text"
            @input="form.clearErrors('father_in_law')"
          />
        </div>

        <div class="col-span-3">
          <app-input
            id="shtibel"
            v-model="form.shtibel"
            :error="form.errors.shtibel"
            label="Shtibel"
            type="text"
            @input="form.clearErrors('shtibel')"
          />
        </div>

      </div>

      <div class="p-6 flex items-center justify-end">
        <app-button :processing="form.processing" class="ml-2" color="primary" type="submit">Submit</app-button>
      </div>
    </form>

    <teleport v-if="isMounted" to="#header">
      {{ header }}
    </teleport>
  </div>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import IsMounted from "@/Mixins/IsMounted";
import AppInput from "@/Components/UI/Input";

export default {
  components: {AppInput},

  layout: AppLayout,

  mixins: [IsMounted],

  data() {
    return {
      form: this.freshForm()
    }
  },

  props: {
    member: Object,
  },

  computed: {
    header() {
      if (this.member) {
        return `Edit Member - ${this.member.first_name} ${this.member.last_name}`
      }
      return 'Create New Member'
    }
  },

  methods: {
    freshForm() {
      return this.$inertia.form({
        title: this.member?.title || '',
        first_name: this.member?.first_name || '',
        last_name: this.member?.last_name || '',
        hebrew_first_name: this.member?.hebrew_first_name || '',
        hebrew_last_name: this.member?.hebrew_last_name || '',
        wife_name: this.member?.wife_name || '',
        address: this.member?.address || '',
        city: this.member?.city || '',
        postal_code: this.member?.postal_code || '',
        email: this.member?.email || '',
        home_phone: this.member?.home_phone || '',
        mobile_phone: this.member?.mobile_phone || '',
        wife_mobile_phone: this.member?.wife_mobile_phone || '',
        shtibel: this.member?.shtibel || '',
        father: this.member?.father || '',
        father_in_law: this.member?.father_in_law || '',
      })
    },
    submit() {
      if (this.member) {
        this.form.put(this.$route('members.update', this.member.id))
      } else {
        this.form.post(this.$route('members.store'))
      }
    },
  },
}
</script>
