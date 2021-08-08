<template>
  <div class="rounded-md bg-white shadow overflow-hidden max-w-4xl mx-auto">
    <h3 class="text-lg font-medium text-gray-900 p-6">Member Details</h3>
    <form @submit.prevent="submit">
      <div class="px-6 py-4 grid sm:grid-cols-12 gap-6">

        <div class="col-span-2">
          <app-select
            native
            id="title"
            v-model="form.title"
            :error="form.errors.title"
            label="Title"
            :options="['Rabbi & Mrs.', 'Mr. & Mrs.', 'Rabbi & Reb.', 'Rabbi', 'Mr.', 'Mrs.', 'Reb.']"
            @update:model-value="form.clearErrors('title')"
          />
        </div>

        <div class="col-span-4">
          <app-input
            id="first_name"
            v-model="form.first_name"
            :error="form.errors.first_name"
            label="First Name"
            type="text"
            @update:model-value="form.clearErrors('first_name')"
          />
        </div>

        <div class="col-span-4">
          <app-input
            id="last_name"
            v-model="form.last_name"
            :error="form.errors.last_name"
            label="Last Name"
            type="text"
            @update:model-value="form.clearErrors('last_name')"
          />
        </div>

        <div class="col-span-4 col-start-1">
          <app-input
            id="hebrew_first_name"
            v-model="form.hebrew_first_name"
            :error="form.errors.hebrew_first_name"
            label="Hebrew First Name"
            type="text"
            @update:model-value="form.clearErrors('hebrew_first_name')"
          />
        </div>

        <div class="col-span-4">
          <app-input
            id="hebrew_last_name"
            v-model="form.hebrew_last_name"
            :error="form.errors.hebrew_last_name"
            label="Hebrew Last Name"
            type="text"
            @update:model-value="form.clearErrors('hebrew_last_name')"
          />
        </div>

        <div class="col-span-4 col-start-1">
          <app-input
            id="wife_name"
            v-model="form.wife_name"
            :error="form.errors.wife_name"
            label="Wife's Name"
            type="text"
            @update:model-value="form.clearErrors('wife_name')"
          />
        </div>

        <div class="col-span-5 col-start-1">
          <app-input
            id="address"
            v-model="form.address"
            :error="form.errors.address"
            label="Address"
            type="text"
            @update:model-value="form.clearErrors('address')"
          />
        </div>

        <div class="col-span-3">
          <app-input
            id="city"
            v-model="form.city"
            :error="form.errors.city"
            label="City"
            type="text"
            @update:model-value="form.clearErrors('city')"
          />
        </div>

        <div class="col-span-3">
          <app-input
            id="postal_code"
            v-model="form.postal_code"
            :error="form.errors.postal_code"
            label="Postal Code"
            type="text"
            @update:model-value="form.clearErrors('postal_code')"
          />
        </div>

        <div class="col-span-4 col-start-1">
          <app-input
            id="email"
            v-model="form.email"
            :error="form.errors.email"
            label="Email"
            type="email"
            @update:model-value="form.clearErrors('email')"
          />
        </div>

        <div class="col-span-3 col-start-1">
          <app-input
            id="home_phone"
            v-model="form.home_phone"
            :error="form.errors.home_phone"
            label="Home Phone"
            type="text"
            @update:model-value="form.clearErrors('home_phone')"
          />
        </div>

        <div class="col-span-3">
          <app-input
            id="cell_phone"
            v-model="form.cell_phone"
            :error="form.errors.cell_phone"
            label="Cellphone"
            type="text"
            @update:model-value="form.clearErrors('cell_phone')"
          />
        </div>

        <div class="col-span-3">
          <app-input
            id="wife_cell_phone"
            v-model="form.wife_cell_phone"
            :error="form.errors.wife_cell_phone"
            label="Wife's Cellphone"
            type="text"
            @update:model-value="form.clearErrors('wife_cell_phone')"
          />
        </div>

        <div class="col-span-4 col-start-1">
          <app-input
            id="father"
            v-model="form.father"
            :error="form.errors.father"
            label="Father"
            type="text"
            @update:model-value="form.clearErrors('father')"
          />
        </div>

        <div class="col-span-4">
          <app-input
            id="father_in_law"
            v-model="form.father_in_law"
            :error="form.errors.father_in_law"
            label="Father In Law"
            type="text"
            @update:model-value="form.clearErrors('father_in_law')"
          />
        </div>

        <div class="col-span-3">
          <app-input
            id="shtibel"
            v-model="form.shtibel"
            :error="form.errors.shtibel"
            label="Shtibel"
            type="text"
            @update:model-value="form.clearErrors('shtibel')"
          />
        </div>

      </div>

      <div class="p-6 flex items-center justify-end space-x-2">
        <inertia-link :href="member ? $route('members.show', member.id) : $route('members.index')"><app-button color="secondary">Cancel</app-button></inertia-link>
        <app-button :processing="form.processing" color="primary" type="submit">Submit</app-button>
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
import AppInput from "@/Components/FormControls/Input"
import AppSelect from "@/Components/FormControls/Select";

export default {
  components: {AppSelect, AppInput},

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
        cell_phone: this.member?.cell_phone || '',
        wife_cell_phone: this.member?.wife_cell_phone || '',
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
