@extends('layouts.app-main-admin')

@section('content')
    @php
        use Illuminate\Support\Collection;
        if (!isset($forms) || $forms === null) {
            $forms = collect();
        }
        $forms = $forms instanceof Collection ? $forms : collect($forms);
    @endphp

    <div x-data="{
        formData: {
            voter_code: '',
            full_name: '',
            dob: '',
            email: '',
            phone: '',
            form_id: 'all',
            registration_status: 'approved',
        },
        errors: {},
        loading: false,
        showSuccess: false,

        generateVoterCode() {
            // simple client-side preview id: V + timestamp fragment + random
            this.formData.voter_code = 'V' + String(Date.now()).slice(-8) + (Math.floor(Math.random()*9000)+1000);
        },

        validate() {
            this.errors = {};
            if (!this.formData.full_name) this.errors.full_name = ['Name is required'];
            if (!this.formData.dob) this.errors.dob = ['Date of birth is required'];
            if (!this.formData.email) this.errors.email = ['Email is required'];
            return Object.keys(this.errors).length === 0;
        },

        submit() {
            if (!this.validate()) return;
            this.loading = true;
            const payload = new FormData();
            payload.append('voter_code', this.formData.voter_code);
            payload.append('full_name', this.formData.full_name);
            payload.append('dob', this.formData.dob);
            payload.append('email', this.formData.email);
            payload.append('phone', this.formData.phone);
            payload.append('form_id', this.formData.form_id);
            payload.append('registration_status', this.formData.registration_status);
            payload.append('_token', document.querySelector('input[name=_token]').value);

            fetch('{{ route('admin.voters.store') }}', {
                method: 'POST',
                body: payload,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    this.showSuccess = true;
                } else {
                    this.errors = data.errors || { general: ['Unable to create voter'] };
                }
            })
            .catch(() => {
                this.errors = { general: ['Server error. Try again.'] };
            })
            .finally(() => this.loading = false);
        }
    }"
         x-init="generateVoterCode()"
         class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50 flex">

        <x-admin-sidebar />

        <main class="flex-1">
            <!-- Topbar -->
            <div class="bg-white/80 backdrop-blur-sm border-b sticky top-0 z-40">
                <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-600 to-blue-500 flex items-center justify-center shadow">
                            <i class="ri-user-add-line text-white text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-semibold text-gray-900">Create Voter</h1>
                            <p class="text-sm text-gray-500">Create an auto-approved registered voter</p>
                        </div>
                    </div>
                    <nav class="text-sm text-gray-600">
                        <a href="{{ route('admin.voters.index') }}" class="hover:underline">Voters</a>
                        <span class="mx-2">/</span>
                        <span class="text-gray-900 font-medium">Create</span>
                    </nav>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-6 py-8">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    <!-- Form column -->
                    <section class="lg:col-span-8">
                        <form @submit.prevent="submit()" class="space-y-6">
                            @csrf

                            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                                <div class="px-6 py-5 bg-gradient-to-r from-white via-indigo-50 to-white border-b">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h2 class="text-lg font-bold text-gray-900">Voter Details</h2>
                                            <p class="text-sm text-gray-500">Enter the voter's information. Registration will be auto-approved.</p>
                                        </div>
                                        <div class="text-sm text-gray-500">Status: <span class="font-semibold text-emerald-600">Auto Approved</span></div>
                                    </div>
                                </div>

                                <div class="p-6 space-y-6">
                                    <!-- Voter ID (auto-generated preview) -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Voter ID (preview)</label>
                                        <div class="flex gap-3 items-center">
                                            <input type="text" x-model="formData.voter_code" readonly
                                                   class="flex-1 px-4 py-3 border border-gray-200 rounded-lg bg-gray-50 text-gray-700">
                                            <button type="button" @click="generateVoterCode()"
                                                    class="inline-flex items-center px-4 py-2 bg-white border rounded-lg text-sm text-gray-700 hover:bg-gray-50">
                                                <i class="ri-refresh-line mr-2"></i> Regenerate
                                            </button>
                                        </div>
                                        <p class="text-xs text-gray-400 mt-2">Server may assign final ID on creation. This is a client preview.</p>
                                    </div>

                                    <!-- Name -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Full Name <span class="text-red-500">*</span></label>
                                        <input type="text" x-model="formData.full_name" placeholder="e.g., Maria Clara"
                                               :class="errors.full_name ? 'border-red-300 bg-red-50' : 'border-gray-200'"
                                               class="w-full px-4 py-3 rounded-lg border focus:ring-2 focus:ring-indigo-500">
                                        <div x-show="errors.full_name" class="text-red-500 text-sm mt-1" x-text="errors.full_name?.[0]"></div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <!-- DOB -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth <span class="text-red-500">*</span></label>
                                            <input type="date" x-model="formData.dob"
                                                   :class="errors.dob ? 'border-red-300 bg-red-50' : 'border-gray-200'"
                                                   class="w-full px-3 py-3 rounded-lg border focus:ring-2 focus:ring-indigo-500">
                                            <div x-show="errors.dob" class="text-red-500 text-sm mt-1" x-text="errors.dob?.[0]"></div>
                                        </div>

                                        <!-- Email -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                                            <input type="email" x-model="formData.email" placeholder="contact@example.com"
                                                   :class="errors.email ? 'border-red-300 bg-red-50' : 'border-gray-200'"
                                                   class="w-full px-3 py-3 rounded-lg border focus:ring-2 focus:ring-indigo-500">
                                            <div x-show="errors.email" class="text-red-500 text-sm mt-1" x-text="errors.email?.[0]"></div>
                                        </div>

                                        <!-- Phone -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                                            <input type="tel" x-model="formData.phone" placeholder="+1 (555) 123-4567"
                                                   class="w-full px-3 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:ring-indigo-500">
                                        </div>
                                    </div>

                                    <!-- Registered Form -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Registered In Form</label>
                                        <select x-model="formData.form_id" class="w-full px-4 py-3 border rounded-lg border-gray-200 bg-white focus:ring-2 focus:ring-indigo-500">
                                            <option value="all">All / Not assigned</option>
                                            @foreach($forms as $form)
                                                <option value="{{ $form->id }}">{{ $form->title ?? $form->name ?? 'Form #' . $form->id }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Extra notes -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Notes (optional)</label>
                                        <textarea x-model="formData.notes" rows="3" placeholder="Optional admin notes"
                                                  class="w-full px-4 py-3 border rounded-lg border-gray-200 focus:ring-2 focus:ring-indigo-500 bg-white"></textarea>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="px-6 py-4 bg-white border-t flex items-center justify-between">
                                    <div class="text-sm text-gray-600">
                                        <span class="font-medium text-gray-900">Auto-approval</span> — created voters will be approved immediately.
                                    </div>

                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('admin.voters.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-200 rounded-lg bg-white text-gray-700 hover:bg-gray-50">
                                            Cancel
                                        </a>

                                        <button type="submit"
                                                :disabled="loading"
                                                class="inline-flex items-center px-5 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-60">
                                            <span x-show="!loading"><i class="ri-add-line mr-2"></i>Create Voter</span>
                                            <span x-show="loading"><i class="ri-loader-4-line animate-spin mr-2"></i>Creating...</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <!-- server/general errors -->
                        <div x-show="Object.keys(errors).length && errors.general" class="mt-4 rounded-md bg-red-50 border-l-4 border-red-600 p-4">
                            <p class="text-sm text-red-700" x-text="errors.general?.[0]"></p>
                        </div>
                    </section>

                    <!-- Right column: sticky helpers -->
                    <aside class="lg:col-span-4">
                        <div class="sticky top-28 space-y-6">
                            <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Form Summary</h3>
                                <div class="text-sm text-gray-600 mb-4">Quick overview of the created voter</div>
                                <ul class="space-y-2 text-sm text-gray-700">
                                    <li class="flex justify-between"><span class="text-gray-500">Voter ID</span><span x-text="formData.voter_code" class="font-medium"></span></li>
                                    <li class="flex justify-between"><span class="text-gray-500">Name</span><span x-text="formData.full_name || '-'"></span></li>
                                    <li class="flex justify-between"><span class="text-gray-500">Email</span><span x-text="formData.email || '-'"></span></li>
                                    <li class="flex justify-between"><span class="text-gray-500">Phone</span><span x-text="formData.phone || '-'"></span></li>
                                    <li class="flex justify-between"><span class="text-gray-500">Form</span>
                                        <span class="font-medium" x-text="(function(){ const id = formData.form_id; return id === 'all' ? 'All / Not assigned' : (document.querySelector('#form-select option[value=\"'+id+'\"]')?.textContent || id) })()"></span>
                                    </li>
                                </ul>
                            </div>

                            <div class="bg-gradient-to-br from-indigo-50 to-white rounded-2xl border border-gray-100 p-6">
                                <h4 class="text-md font-semibold text-gray-900 mb-2">Pro Tips</h4>
                                <ul class="text-sm text-gray-600 space-y-3">
                                    <li class="flex items-start gap-3"><i class="ri-check-line text-green-500 mt-1"></i><span>Use a valid email for notifications.</span></li>
                                    <li class="flex items-start gap-3"><i class="ri-shield-check-line text-indigo-500 mt-1"></i><span>Phone numbers improve contactability.</span></li>
                                </ul>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>

            <!-- Success modal -->
            <div x-show="showSuccess" x-transition class="fixed inset-0 bg-black/60 flex items-center justify-center p-4 z-50" @click.self="showSuccess = false">
                <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border">
                    <div class="text-center">
                        <div class="w-20 h-20 mx-auto rounded-full bg-emerald-50 flex items-center justify-center mb-4">
                            <i class="ri-checkbox-circle-line text-emerald-600 text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Voter Created</h3>
                        <p class="text-sm text-gray-600 mb-6">The voter was created and auto-approved successfully.</p>

                        <div class="flex gap-3">
                            <button @click="showSuccess = false; window.location.href='{{ route('admin.voters.index') }}'"
                                    class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg">View Voters</button>
                            <button @click="showSuccess = false; $nextTick(()=>generateVoterCode()); formData.full_name=''; formData.email=''; formData.phone=''; formData.dob='';"
                                    class="flex-1 px-4 py-2 border border-gray-200 rounded-lg">Create Another</button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection
