@extends('layouts.app-main-admin')

@section('content')
    <div x-data="candidateData()"
         x-init=""
         class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50 flex">

        <x-admin-sidebar />

        <main class="flex-1">
            <div class="bg-white/80 backdrop-blur-sm border-b sticky top-0 z-40">
                <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-600 to-blue-500 flex items-center justify-center shadow">
                            <i class="ri-user-add-line text-white text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-semibold text-gray-900">Create Candidate</h1>
                            <p class="text-sm text-gray-500">Add candidate details and assign position.</p>
                        </div>
                    </div>
                    <nav class="text-sm text-gray-600">
                        <a href="{{ route('admin.candidates.index') }}" class="hover:underline">Candidates</a>
                        <span class="mx-2">/</span>
                        <span class="text-gray-900 font-medium">Create</span>
                    </nav>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-6 py-8">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    <section class="lg:col-span-8">
                        <form @submit.prevent="submitForm()" class="space-y-6 bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                            @csrf
                            <div class="px-6 py-5 bg-gradient-to-r from-white via-indigo-50 to-white border-b">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h2 class="text-lg font-bold text-gray-900">Candidate Details</h2>
                                        <p class="text-sm text-gray-500">Provide candidate information and select/create the position.</p>
                                    </div>
                                    <div class="text-sm text-gray-500">Status: <span class="font-semibold text-emerald-600">Active by default</span></div>
                                </div>
                            </div>

                            <div class="p-6 space-y-6">
                                <!-- User input -->
                                <div>
                                    <label for="user-search" class="block text-sm font-medium text-gray-700 mb-2">User  <span class="text-red-500">*</span></label>
                                    <div class="flex gap-3">
                                        <input id="user-search" list="users-list" x-model="formData.user_search" @blur="resolveUser()" @keyup.enter="resolveUser()"
                                               placeholder="Start typing name, email or id"
                                               class="flex-1 px-4 py-3 border border-gray-200 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500">
                                        <button type="button" @click="formData.user_search=''; formData.user_id='';"
                                                class="inline-flex items-center px-4 py-2 bg-white border rounded-lg text-sm text-gray-700 hover:bg-gray-50">
                                            Clear
                                        </button>
                                    </div>
                                    <datalist id="users-list">
                                        <template x-for="u in users" :key="u.id">
                                            <option :value="(u.name || '') + (u.email ? ' <' + u.email + '>' : '')"></option>
                                        </template>
                                    </datalist>

                                <!-- Election & Partylist -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="election_id" class="block text-sm font-medium text-gray-700 mb-2">Election <span class="text-red-500">*</span></label>
                                        <select id="election_id" name="election_id" x-model="formData.election_id"
                                                class="w-full px-4 py-3 border rounded-lg border-gray-200 bg-white focus:ring-2 focus:ring-indigo-500">
                                            <option value="">Select Election</option>
                                            @foreach($elections as $e)
                                                <option value="{{ $e->id }}">{{ $e->name ?? $e->title ?? 'Election #' . $e->id }}</option>
                                            @endforeach
                                        </select>
                                        <div x-show="errors.election_id" class="text-red-500 text-sm mt-1" x-text="errors.election_id?.[0]"></div>
                                    </div>

                                    <div>
                                        <label for="partylist_id" class="block text-sm font-medium text-gray-700 mb-2">Partylist</label>
                                        <select id="partylist_id" name="partylist_id" x-model="formData.partylist_id"
                                                class="w-full px-4 py-3 border rounded-lg border-gray-200 bg-white focus:ring-2 focus:ring-indigo-500">
                                            <option value="">Independent / None</option>
                                            @foreach($partylists as $p)
                                                <option value="{{ $p->id }}">{{ $p->name ?? $p->title ?? 'Party #' . $p->id }}</option>
                                            @endforeach
                                        </select>
                                        <div x-show="errors.partylist_id" class="text-red-500 text-sm mt-1" x-text="errors.partylist_id?.[0]"></div>
                                    </div>
                                </div>

                                <!-- Position -->
                                <div>
                                    <label for="position_id" class="block text-sm font-medium text-gray-700 mb-2">Position <span class="text-red-500">*</span></label>
                                    <select id="position_id" name="position_id" x-model="formData.position_id"
                                            @change="if (String($event.target.value).startsWith('preset:')) { formData.new_position_name = String($event.target.value).replace(/^preset:/, ''); } else { formData.new_position_name = ''; }"
                                            class="w-full px-4 py-3 border rounded-lg border-gray-200 bg-white focus:ring-2 focus:ring-indigo-500">
                                        <option value="">Select Position</option>

                                        <optgroup label="Common positions">
                                            <template x-for="p in commonPositions" :key="p">
                                                <option :value="'preset:' + p" x-text="p"></option>
                                            </template>
                                        </optgroup>

                                        <optgroup label="Existing positions">
                                            <template x-for="p in existingPositions" :key="p.id">
                                                <option :value="p.id" x-text="p.name"></option>
                                            </template>
                                        </optgroup>

                                        <option value="new">+ Create New Position</option>
                                    </select>
                                    <div x-show="formData.position_id === 'new' || String(formData.position_id).startsWith('preset:')" class="mt-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-2" x-text="formData.position_id === 'new' ? 'New position name' : 'New position preview'"></label>
                                        <input type="text" x-model="formData.new_position_name"
                                               :placeholder="formData.position_id === 'new' ? 'e.g., Chief Technology Officer' : ''"
                                               :readonly="String(formData.position_id).startsWith('preset:')"
                                               class="w-full px-4 py-3 border rounded-lg border-gray-200 bg-white focus:ring-2 focus:ring-indigo-500">
                                        <div x-show="errors.new_position_name" class="text-red-500 text-sm mt-1" x-text="errors.new_position_name?.[0]"></div>
                                        <template x-if="String(formData.position_id).startsWith('preset:')">
                                            <div class="text-xs text-gray-500 mt-2">Preset selected — submitted as new position unless an existing position is chosen.</div>
                                        </template>
                                    </div>
                                    <div x-show="errors.position_id && !errors.new_position_name" class="text-red-500 text-sm mt-2" x-text="errors.position_id?.[0]"></div>
                                </div>

                                <!-- Platform & Photo -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Platform (optional)</label>
                                    <textarea x-model="formData.platform" rows="4"
                                              class="w-full px-4 py-3 border rounded-lg border-gray-200 bg-white focus:ring-2 focus:ring-indigo-500"></textarea>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-start">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Photo</label>
                                        <div class="border-2 border-dashed border-gray-200 rounded-lg p-4 text-center">
                                            <div x-show="!photoPreview" class="space-y-3">
                                                <div class="w-20 h-20 rounded-xl bg-gray-50 mx-auto flex items-center justify-center">
                                                    <i class="ri-image-line text-gray-400 text-2xl"></i>
                                                </div>
                                                <div class="text-sm text-gray-600">PNG, JPG up to 2MB</div>
                                                <input type="file" @change="handlePhotoUpload($event)" accept="image/*" class="hidden" id="photo-upload">
                                                <label for="photo-upload" class="mt-2 inline-flex items-center px-3 py-2 bg-indigo-600 text-white text-sm rounded-lg cursor-pointer hover:bg-indigo-700">Upload</label>
                                            </div>

                                            <div x-show="photoPreview" class="relative">
                                                <img :src="photoPreview" alt="Photo preview" class="mx-auto w-28 h-28 object-cover rounded-lg">
                                                <button type="button" @click="photoPreview = null; formData.photo = null" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center">×</button>
                                            </div>
                                        </div>
                                        <div x-show="errors.photo" class="text-red-500 text-sm mt-2" x-text="errors.photo?.[0]"></div>
                                    </div>

                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                        <select x-model="formData.status" class="w-56 px-4 py-3 border rounded-lg border-gray-200 bg-white focus:ring-2 focus:ring-indigo-500">
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                            <option value="disqualified">Disqualified</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="px-6 py-4 bg-white border-t flex items-center justify-between">
                                <a href="{{ route('admin.candidates.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-200 rounded-lg bg-white text-gray-700 hover:bg-gray-50">
                                    Cancel
                                </a>

                                <div class="flex items-center gap-3">
                                    <button type="submit" :disabled="loading" class="inline-flex items-center px-5 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-60">
                                        <span x-show="!loading"><i class="ri-add-line mr-2"></i>Create Candidate</span>
                                        <span x-show="loading"><i class="ri-loader-4-line animate-spin mr-2"></i>Creating...</span>
                                    </button>
                                </div>
                            </div>
                        </form>

                        <div x-show="Object.keys(errors).length && errors.general" class="mt-4 rounded-md bg-red-50 border-l-4 border-red-600 p-4">
                            <p class="text-sm text-red-700" x-text="errors.general?.[0]"></p>
                        </div>
                    </section>

                    <aside class="lg:col-span-4">
                        <div class="sticky top-28 space-y-6">
                            <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Form Summary</h3>
                                <div class="text-sm text-gray-600 mb-4">Quick overview of the candidate being created</div>
                                <ul class="space-y-2 text-sm text-gray-700">
                                    <li class="flex justify-between"><span class="text-gray-500">User</span><span class="font-medium" x-text="selectedUserDisplay || '-'"></span></li>
                                    <li class="flex justify-between"><span class="text-gray-500">Election</span><span class="font-medium" x-text="formData.election_id ? (document.querySelector('[name=\\'election_id\\'] option[value=\"'+formData.election_id+'\"]')?.textContent || formData.election_id) : '-'"></span></li>
                                    <li class="flex justify-between"><span class="text-gray-500">Position</span><span class="font-medium" x-text="formData.position_id === 'new' ? (formData.new_position_name || 'New position') : (String(formData.position_id).startsWith('preset:') ? formData.new_position_name : (document.querySelector('[name=\\'position_id\\'] option[value=\"'+formData.position_id+'\"]')?.textContent || formData.position_id || '-'))"></span></li>
                                    <li class="flex justify-between"><span class="text-gray-500">Partylist</span><span class="font-medium" x-text="formData.partylist_id ? (document.querySelector('[name=\\'partylist_id\\'] option[value=\"'+formData.partylist_id+'\"]')?.textContent || formData.partylist_id) : 'Independent'"></span></li>
                                    <li class="flex justify-between"><span class="text-gray-500">Status</span><span class="font-medium" x-text="formData.status"></span></li>
                                </ul>
                            </div>

                            <div class="bg-gradient-to-br from-indigo-50 to-white rounded-2xl border border-gray-100 p-6">
                                <h4 class="text-md font-semibold text-gray-900 mb-2">Pro Tips</h4>
                                <ul class="text-sm text-gray-600 space-y-3">
                                    <li class="flex items-start gap-3"><i class="ri-check-line text-green-500 mt-1"></i><span>Ensure the selected user is eligible and verified.</span></li>
                                    <li class="flex items-start gap-3"><i class="ri-shield-check-line text-indigo-500 mt-1"></i><span>Use precise position names to avoid duplicates.</span></li>
                                    <li class="flex items-start gap-3"><i class="ri-image-line text-indigo-400 mt-1"></i><span>Upload a clear headshot for candidate listings.</span></li>
                                </ul>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>

            <div x-show="showSuccess" x-transition class="fixed inset-0 bg-black/60 flex items-center justify-center p-4 z-50" @click.self="showSuccess = false">
                <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border">
                    <div class="text-center">
                        <div class="w-20 h-20 mx-auto rounded-full bg-emerald-50 flex items-center justify-center mb-4">
                            <i class="ri-checkbox-circle-line text-emerald-600 text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Candidate Created</h3>
                        <p class="text-sm text-gray-600 mb-6">The candidate has been created successfully.</p>

                        <div class="flex gap-3">
                            <button @click="showSuccess = false; window.location.href='{{ route('admin.candidates.index') }}'" class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg">View Candidates</button>
                            <button @click="showSuccess = false; $nextTick(()=>{ formData.user_search=''; formData.user_id=''; formData.election_id=''; formData.position_id=''; formData.new_position_name=''; formData.partylist_id=''; formData.platform=''; photoPreview=null; formData.photo=null; errors={}; })" class="flex-1 px-4 py-2 border border-gray-200 rounded-lg bg-white text-gray-700">Create Another</button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection

<script>
function candidateData() {
    return {
        users: @json($users->map(fn($u) => ['id' => $u->id, 'name' => $u->name ?? '', 'email' => $u->email ?? ''])->values()),
        existingPositions: @json($positions->map(fn($p) => ['id' => $p->id, 'name' => $p->name])->values()),
        commonPositions: @json($commonPositions),
        formData: {
            user_search: '',
            user_id: '',
            election_id: '',
            position_id: '',
            new_position_name: '',
            partylist_id: '',
            platform: '',
            photo: null,
            status: 'active'
        },
        errors: {},
        loading: false,
        showSuccess: false,
        photoPreview: null,

        get selectedUserDisplay() {
            const u = this.users.find(x => String(x.id) === String(this.formData.user_id));
            return u ? (u.name || u.email) + (u.email ? ' <' + u.email + '>' : '') : '';
        },

        resolveUser() {
            this.formData.user_id = '';
            const text = (this.formData.user_search || '').trim().toLowerCase();
            if (!text) return;
            const byExactDisplay = this.users.find(u => ((u.name || '') + ' <' + (u.email || '') + '>').toLowerCase() === text);
            if (byExactDisplay) { this.formData.user_id = byExactDisplay.id; return; }
            const byEmail = this.users.find(u => (u.email || '').toLowerCase() === text);
            if (byEmail) { this.formData.user_id = byEmail.id; return; }
            const byId = this.users.find(u => String(u.id) === text);
            if (byId) { this.formData.user_id = byId.id; return; }
            const byName = this.users.find(u => (u.name || '').toLowerCase() === text || (u.name || '').toLowerCase().startsWith(text));
            if (byName) { this.formData.user_id = byName.id; return; }
            // leave user_id empty if not found
        },

        handlePhotoUpload(e) {
            const file = e.target.files[0];
            if (!file) { this.photoPreview = null; this.formData.photo = null; return; }
            if (!file.type.startsWith('image/')) {
                this.errors.photo = ['File must be an image'];
                return;
            }
            this.formData.photo = file;
            const reader = new FileReader();
            reader.onload = (ev) => this.photoPreview = ev.target.result;
            reader.readAsDataURL(file);
        },

        validate() {
            this.errors = {};
            if (!this.formData.user_id) this.errors.user_id = ['Select a valid user from suggestions'];
            if (!this.formData.election_id) this.errors.election_id = ['Election is required'];
            if (!this.formData.position_id) {
                this.errors.position_id = ['Position is required'];
            } else if (this.formData.position_id === 'new' && !this.formData.new_position_name.trim()) {
                this.errors.new_position_name = ['Enter the new position name'];
            } else if (String(this.formData.position_id).startsWith('preset:') && !this.formData.new_position_name.trim()) {
                this.errors.new_position_name = ['Position name is required for preset'];
            }
            return Object.keys(this.errors).length === 0;
        },

        submitForm() {
            if (!this.validate()) return;
            this.loading = true;
            this.errors = {};

            const formData = new FormData();
            formData.append('_token', document.querySelector('input[name=_token]').value);
            formData.append('user_id', this.formData.user_id);
            formData.append('election_id', this.formData.election_id);

            // Position handling:
            // - numeric id -> existing position (append position_id)
            // - 'preset:Name' -> treat as new position (send new_position_name)
            // - 'new' -> send new_position_name
            if (String(this.formData.position_id).startsWith('preset:')) {
                formData.append('position_id', '');
                formData.append('new_position_name', this.formData.new_position_name.trim());
            } else if (this.formData.position_id === 'new') {
                formData.append('position_id', '');
                formData.append('new_position_name', this.formData.new_position_name.trim());
            } else {
                formData.append('position_id', this.formData.position_id || '');
                formData.append('new_position_name', '');
            }

            formData.append('partylist_id', this.formData.partylist_id || '');
            formData.append('platform', this.formData.platform || '');
            formData.append('status', this.formData.status);
            if (this.formData.photo) formData.append('photo', this.formData.photo);

            fetch('{{ route('admin.candidates.store') }}', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = '{{ route('admin.candidates.index') }}';
                } else {
                    this.errors = data.errors || {};
                }
            })
            .catch(error => {
                console.error('Error:', error);
                this.errors = { general: ['Server error. Try again.'] };
            })
            .finally(() => {
                this.loading = false;
            });
        }
    };
}
</script>
