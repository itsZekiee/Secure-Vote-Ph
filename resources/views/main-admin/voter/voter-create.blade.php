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
            email: '',
            phone: '',
            form_id: '',
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
            const namePattern = /^[A-Za-z]+(?:\.?[A-Za-z]+)*(?:\s[A-Za-z]+(?:\.?[A-Za-z]+)*)*(?:\s(Jr\.|Sr\.|I|II|III|IV|V|VI|VII|VIII|IX|X))?$/;

                if (!this.formData.full_name) {
                    this.errors.full_name = ['Name is required'];
                } else if (!namePattern.test(this.formData.full_name.trim())) {
                    this.errors.full_name = [
                        'Only letters, spaces, periods, and valid Roman numeral suffixes are allowed'
                    ];
                }

            if (!this.formData.email) this.errors.email = ['Email is required'];
            if (!this.formData.form_id) this.errors.form_id = ['Election selection is required'];
            return Object.keys(this.errors).length === 0;
        },

        submit() {
            if (!this.validate()) return;
            if (!confirm('Are you sure you want to submit and create this voter?')) {
                return;
            }
            this.loading = true;
            const payload = new FormData();
            payload.append('voter_code', this.formData.voter_code);
            payload.append('full_name', this.formData.full_name);
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
         class="min-h-screen bg-slate-50 flex">

        <x-admin-sidebar />

        <main class="flex-1">
            <x-admin-header title="Registration" />

            <!-- Mobile Header -->
            <header class="lg:hidden bg-white shadow-sm border-b px-4 py-3 flex items-center justify-between">
                <button @click="collapsed = false"
                        class="p-2 rounded-lg text-slate-600 hover:bg-slate-100">
                    <i class="ri-menu-fold-line text-lg rotate-180"></i>
                </button>
                <h1 class="text-lg font-semibold text-slate-800">New Voter</h1>
                <div class="w-10"></div>
            </header>

            <div class="max-w-6xl mx-auto px-6 py-8">
                <!-- Page Title -->
                <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-indigo-600 to-blue-600 rounded-2xl flex items-center justify-center shadow-xl shadow-indigo-100">
                            <i class="ri-user-add-fill text-white text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-black text-slate-900 uppercase tracking-tight">Create Voter</h1>
                            <p class="text-[11px] text-slate-500 font-bold tracking-widest uppercase mt-0.5">Register a new verified voter</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    <!-- Form column -->
                    <section class="lg:col-span-8">
                        <form @submit.prevent="submit()" class="space-y-6">
                            @csrf

                            <div class="bg-white rounded-3xl border border-slate-200/60 shadow-2xl shadow-slate-200/50 overflow-hidden">
                                <div class="px-8 py-5 bg-slate-50/50 border-b border-slate-100 flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center text-indigo-600 shadow-sm">
                                            <i class="ri-information-line text-lg"></i>
                                        </div>
                                        <h3 class="text-base font-black text-slate-800 uppercase tracking-tight">Voter Details</h3>
                                    </div>
                                    <span class="px-3 py-1 bg-emerald-50 text-emerald-600 rounded-full text-[9px] font-black uppercase tracking-widest border border-emerald-100">Auto Approved</span>
                                </div>

                                <div class="p-8 space-y-8">
                                    <!-- Voter ID -->
                                    <div class="space-y-1.5">
                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Voter ID (Preview)</label>
                                        <div class="flex gap-3 items-center">
                                            <div class="relative flex-1 group">
                                                <i class="ri-fingerprint-line absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                                <input type="text" x-model="formData.voter_code" readonly
                                                       class="w-full bg-slate-50 border-none rounded-xl px-12 py-3 text-xs font-bold text-slate-500 cursor-not-allowed">
                                            </div>
                                            <button type="button" @click="generateVoterCode()"
                                                    class="px-5 py-3 bg-white text-slate-600 border border-slate-200 rounded-xl font-black text-[10px] tracking-widest hover:bg-slate-50 transition-all uppercase whitespace-nowrap">
                                                <i class="ri-refresh-line mr-1.5"></i> Regenerate
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Name -->
                                    <div class="space-y-1.5">
                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Full Name <span class="text-rose-500">*</span></label>
                                        <div class="relative group">
                                            <i class="ri-user-line absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors"></i>
                                            <input
                                                    type="text"
                                                    x-model="formData.full_name"
                                                    @input="formData.full_name = formData.full_name.replace(/\s{2,}/g, ' ')" placeholder="e.g., Maria Clara"
                                                   :class="errors.full_name ? 'ring-2 ring-rose-500' : ''"
                                                   class="w-full bg-slate-50 border-none rounded-xl px-12 py-3 text-xs font-bold text-slate-700 focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all shadow-sm">
                                        </div>
                                        <div x-show="errors.full_name" class="text-rose-500 text-[9px] font-bold uppercase tracking-wider mt-1 ml-1" x-text="errors.full_name?.[0]"></div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                        <!-- Email -->
                                        <div class="space-y-1.5">
                                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Email Address <span class="text-rose-500">*</span></label>
                                            <div class="relative group">
                                                <i class="ri-mail-line absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors"></i>
                                                <input type="email" x-model="formData.email" placeholder="contact@example.com"
                                                       :class="errors.email ? 'ring-2 ring-rose-500' : ''"
                                                       class="w-full bg-slate-50 border-none rounded-xl px-12 py-3 text-xs font-bold text-slate-700 focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all shadow-sm">
                                            </div>
                                            <div x-show="errors.email" class="text-rose-500 text-[9px] font-bold uppercase tracking-wider mt-1 ml-1" x-text="errors.email?.[0]"></div>
                                        </div>

                                        <!-- Phone -->
                                        <div class="space-y-1.5">
                                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Phone Number</label>
                                            <div class="relative group">
                                                <i class="ri-phone-line absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors"></i>
                                                <input type="tel" x-model="formData.phone" placeholder="09XXXXXXXXX"
                                                       class="w-full bg-slate-50 border-none rounded-xl px-12 py-3 text-xs font-bold text-slate-700 focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all shadow-sm">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Registered Form -->
                                    <div class="space-y-1.5">
                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Assigned Election <span class="text-rose-500">*</span></label>
                                        <div class="relative group">
                                            <i class="ri-qr-code-line absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors z-10"></i>
                                            <select id="form-select" x-model="formData.form_id"
                                                    :class="errors.form_id ? 'ring-2 ring-rose-500' : ''"
                                                    class="w-full bg-slate-50 border-none rounded-xl px-12 py-3 text-xs font-bold text-slate-700 focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all shadow-sm appearance-none">
                                                <option value="">Select an election form</option>
                                                @foreach($forms as $form)
                                                    <option value="{{ $form->id }}">{{ $form->title ?? $form->name ?? 'Form #' . $form->id }}</option>
                                                @endforeach
                                            </select>
                                            <i class="ri-arrow-down-s-line absolute right-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                        </div>
                                        <div x-show="errors.form_id" class="text-rose-500 text-[9px] font-bold uppercase tracking-wider mt-1 ml-1" x-text="errors.form_id?.[0]"></div>
                                    </div>

                                    <!-- Notes -->
                                    <div class="space-y-1.5">
                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Notes (Optional)</label>
                                        <textarea x-model="formData.notes" rows="2" placeholder="Additional admin notes..."
                                                  class="w-full bg-slate-50 border-none rounded-xl px-5 py-3 text-xs font-bold text-slate-700 focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all shadow-sm"></textarea>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="px-8 py-6 bg-slate-50/50 border-t border-slate-100 flex items-center justify-between">
                                    <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">New voters are automatically verified.</p>
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('admin.voters.index') }}"
                                           class="px-6 py-3 bg-white text-slate-600 rounded-xl font-black text-[10px] tracking-widest hover:bg-slate-50 transition-all border border-slate-200 uppercase">
                                            Cancel
                                        </a>
                                        <button type="submit"
                                                :disabled="loading"
                                                class="px-10 py-3 bg-slate-900 text-white rounded-xl font-black text-[10px] tracking-widest hover:bg-indigo-600 hover:shadow-2xl hover:shadow-indigo-200 transition-all uppercase disabled:opacity-50">
                                            <span x-show="!loading">Create Voter</span>
                                            <span x-show="loading" class="flex items-center gap-2">
                                                <i class="ri-loader-4-line animate-spin"></i> Processing...
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <!-- General Errors -->
                        <div x-show="Object.keys(errors).length && errors.general" x-transition class="mt-4 p-3.5 bg-rose-50 border border-rose-100 rounded-xl flex items-center gap-3 text-rose-600">
                            <i class="ri-error-warning-fill text-lg"></i>
                            <p class="text-[10px] font-bold uppercase tracking-wider" x-text="errors.general?.[0]"></p>
                        </div>
                    </section>

                    <!-- Right column: Summary -->
                    <aside class="lg:col-span-4">
                        <div class="sticky top-28 space-y-6">
                            <!-- Preview Card -->
                            <div class="bg-white rounded-3xl border border-slate-200/60 shadow-xl shadow-slate-200/40 p-6">
                                <div class="flex items-center gap-3 mb-6">
                                    <div class="w-8 h-8 bg-indigo-50 rounded-lg flex items-center justify-center text-indigo-600">
                                        <i class="ri-eye-line text-lg"></i>
                                    </div>
                                    <h3 class="text-base font-black text-slate-800 uppercase tracking-tight">Voter Preview</h3>
                                </div>

                                <div class="space-y-5">
                                    <div class="flex flex-col items-center py-5 mb-5 bg-slate-50/50 rounded-2xl border border-slate-100">
                                        <div class="w-16 h-16 bg-white rounded-xl flex items-center justify-center text-slate-300 shadow-sm border border-slate-100 mb-3">
                                            <i class="ri-user-3-line text-2xl"></i>
                                        </div>
                                        <p class="text-base font-black text-slate-800 tracking-tight" x-text="formData.full_name || 'Full Name'"></p>
                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mt-0.5" x-text="formData.voter_code"></p>
                                    </div>

                                    <div class="space-y-3">
                                        <div class="flex justify-between items-center px-1">
                                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Email</span>
                                            <span class="text-[11px] font-bold text-slate-700 truncate max-w-[120px]" x-text="formData.email || '—'"></span>
                                        </div>
                                        <div class="flex justify-between items-center px-1">
                                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Phone</span>
                                            <span class="text-[11px] font-bold text-slate-700" x-text="formData.phone || '—'"></span>
                                        </div>
                                        <div class="flex justify-between items-center px-1">
                                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Election</span>
                                            <span class="text-[11px] font-bold text-slate-700 text-right truncate max-w-[120px]" x-text="formData.form_id ? (document.querySelector('#form-select option[value=\'' + formData.form_id + '\']')?.textContent || 'Selected') : 'Not selected'"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tips -->
                            <div class="bg-gradient-to-br from-indigo-600 to-blue-600 rounded-3xl p-6 text-white shadow-xl shadow-indigo-200">
                                <h4 class="text-base font-black uppercase tracking-tight mb-3 flex items-center gap-2">
                                    <i class="ri-lightbulb-line"></i> Pro Tips
                                </h4>
                                <ul class="space-y-3">
                                    <li class="flex items-start gap-2.5">
                                        <i class="ri-checkbox-circle-line text-emerald-400 mt-0.5"></i>
                                        <p class="text-[11px] font-bold text-indigo-50 leading-relaxed">Ensure the email address is correct for automatic invitation delivery.</p>
                                    </li>
                                    <li class="flex items-start gap-2.5">
                                        <i class="ri-checkbox-circle-line text-emerald-400 mt-0.5"></i>
                                        <p class="text-[11px] font-bold text-indigo-50 leading-relaxed">Voter codes are unique identifiers used for security during voting.</p>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>

            <!-- Success modal -->
            <div x-show="showSuccess"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 z-[100]"
                 @click.self="showSuccess = false">
                <div class="bg-white rounded-3xl max-w-md w-full p-8 shadow-2xl border border-slate-100 text-center">
                    <div class="w-20 h-20 mx-auto rounded-2xl bg-emerald-50 flex items-center justify-center mb-6 shadow-inner">
                        <i class="ri-checkbox-circle-fill text-emerald-500 text-4xl"></i>
                    </div>
                    <h3 class="text-2xl font-black text-slate-900 uppercase tracking-tight mb-2">Voter Created</h3>
                    <p class="text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-8">Profile has been registered and verified successfully.</p>

                    <div class="grid grid-cols-2 gap-3">
                        <button @click="showSuccess = false; window.location.href='{{ route('admin.voters.index') }}'"
                                class="px-5 py-3 bg-slate-100 text-slate-600 rounded-xl font-black text-[10px] tracking-widest hover:bg-slate-200 transition-all uppercase">
                            View Directory
                        </button>
                        <button @click="showSuccess = false; $nextTick(()=>generateVoterCode()); formData.full_name=''; formData.email=''; formData.phone=''; formData.form_id='';"
                                class="px-5 py-3 bg-indigo-600 text-white rounded-xl font-black text-[10px] tracking-widest hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition-all uppercase">
                            Create Another
                        </button>
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection