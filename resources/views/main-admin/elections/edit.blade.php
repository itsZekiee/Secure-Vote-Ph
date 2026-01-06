@extends('layouts.app-main-admin')

@section('content')
    <div x-data="{
        isSubmitting: false,
        activeTab: 'basic',
        showSuccessToast: false,
        positions: @js($election->positions->map(fn($p) => [
            'id' => $p->id,
            'name' => $p->name,
            'candidates' => $p->candidates->map(fn($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'first_name' => $c->first_name,
                'middle_name' => $c->middle_name,
                'last_name' => $c->last_name,
                'partylist_id' => $c->partylist_id
            ])
        ])),
        formData: {
            title: @js($election->title),
            registration_deadline: @js($election->registration_deadline ? $election->registration_deadline->format('Y-m-d\TH:i') : ''),
            max_votes: @js($election->max_votes),
            enable_geo_location: @js((bool)$election->require_geo_verification),
            enable_geo_registration: @js((bool)$election->require_geo_registration)
        },
        addPosition() {
            this.positions.push({ name: '', candidates: [{ name: '', first_name: '', middle_name: '', last_name: '', partylist_id: '' }] });
        },
        removePosition(index) {
            if (this.positions.length > 1) {
                this.positions.splice(index, 1);
            }
        },
        addCandidate(positionIndex) {
            this.positions[positionIndex].candidates.push({ name: '', first_name: '', middle_name: '', last_name: '', partylist_id: '' });
        },
        removeCandidate(positionIndex, candidateIndex) {
            if (this.positions[positionIndex].candidates.length > 1) {
                this.positions[positionIndex].candidates.splice(candidateIndex, 1);
            }
        },
        submitForm() {
            this.isSubmitting = true;
            this.showSuccessToast = true;
            setTimeout(() => {
                document.getElementById('edit-election-form').submit();
            }, 1000);
        }
    }"
    class="flex min-h-screen bg-slate-50">

        <!-- Sidebar is already handled by layout in some cases but here it is included -->
        {{-- <x-admin-sidebar /> --}}

        <main class="flex-1 min-w-0 overflow-hidden">
            <!-- Success Toast -->
            <div x-show="showSuccessToast"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-y-4"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 class="fixed bottom-10 right-10 z-[100] bg-slate-900 text-white px-8 py-4 rounded-3xl shadow-2xl flex items-center gap-4 border border-slate-800"
                 style="display: none;">
                <div class="w-10 h-10 bg-emerald-500 rounded-2xl flex items-center justify-center">
                    <i class="ri-check-line text-xl"></i>
                </div>
                <div>
                    <p class="font-black text-sm uppercase tracking-tight">Success!</p>
                    <p class="text-xs text-slate-400 font-bold">Election data has been saved successfully.</p>
                </div>
            </div>

            <!-- Header -->
            <header class="bg-white/80 backdrop-blur-md border-b border-slate-200 sticky top-0 z-30">
                <div class="max-w-7xl mx-auto px-8 py-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-6">
                            <div class="w-14 h-14 bg-gradient-to-br from-indigo-600 to-blue-600 rounded-2xl flex items-center justify-center shadow-xl shadow-indigo-200">
                                <i class="ri-edit-2-fill text-white text-2xl"></i>
                            </div>
                            <div>
                                <h1 class="text-3xl font-black text-slate-900 uppercase tracking-tight">Edit Election</h1>
                                <p class="text-sm text-slate-500 font-bold tracking-wide">Configuring: {{ $election->title }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <a href="{{ route('admin.elections.index') }}"
                               class="px-8 py-4 bg-slate-100 text-slate-600 rounded-2xl font-black text-sm hover:bg-slate-200 transition-all">
                                CANCEL
                            </a>
                            <button @click="submitForm()" :disabled="isSubmitting"
                                    class="px-10 py-4 bg-slate-900 text-white rounded-2xl font-black text-sm hover:bg-indigo-600 hover:shadow-2xl hover:shadow-indigo-200 transition-all disabled:opacity-50">
                                <span x-show="!isSubmitting">SAVE CHANGES</span>
                                <span x-show="isSubmitting" class="flex items-center gap-2">
                                    <i class="ri-loader-4-line animate-spin"></i> SAVING...
                                </span>
                            </button>
                        </div>
                    </div>

                    <div class="mt-8 flex items-center gap-8 border-t border-slate-100 pt-6">
                        <button @click="activeTab = 'basic'"
                                :class="activeTab === 'basic' ? 'text-indigo-600' : 'text-slate-400 hover:text-slate-600'"
                                class="flex items-center gap-3 pb-4 relative transition-all group">
                            <i class="ri-settings-4-line text-lg"></i>
                            <span class="font-black text-sm uppercase tracking-widest">Basic & Settings</span>
                            <div x-show="activeTab === 'basic'"
                                 x-transition:enter="transition scale-x-100 duration-300"
                                 class="absolute bottom-0 left-0 w-full h-1 bg-indigo-600 rounded-full"></div>
                        </button>
                        <button @click="activeTab = 'positions'"
                                :class="activeTab === 'positions' ? 'text-indigo-600' : 'text-slate-400 hover:text-slate-600'"
                                class="flex items-center gap-3 pb-4 relative transition-all group">
                            <i class="ri-group-line text-lg"></i>
                            <span class="font-black text-sm uppercase tracking-widest">Positions & Candidates</span>
                            <div x-show="activeTab === 'positions'"
                                 x-transition:enter="transition scale-x-100 duration-300"
                                 class="absolute bottom-0 left-0 w-full h-1 bg-indigo-600 rounded-full"></div>
                        </button>
                    </div>
                </div>
            </header>

            <div class="max-w-7xl mx-auto p-8">
                @if($errors->any())
                    <div class="mb-8 p-6 bg-red-50 border border-red-100 rounded-2xl flex items-start gap-4 animate-in fade-in slide-in-from-top-4 duration-500">
                        <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-red-500 shadow-sm border border-red-50 flex-shrink-0">
                            <i class="ri-error-warning-fill text-xl"></i>
                        </div>
                        <div>
                            <p class="font-bold text-red-800 uppercase tracking-tight text-base">Review Errors</p>
                            <ul class="mt-2 space-y-1">
                                @foreach($errors->all() as $error)
                                    <li class="text-red-600 font-bold text-sm">• {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <form id="edit-election-form" action="{{ route('admin.elections.update', $election->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="organization_id" value="{{ $election->organization_id }}">
                    <input type="hidden" name="start_date" value="{{ $election->start_date->format('Y-m-d\TH:i') }}">
                    <input type="hidden" name="end_date" value="{{ $election->end_date->format('Y-m-d\TH:i') }}">
                    <input type="hidden" name="status" value="{{ $election->status }}">
                    <input type="hidden" name="geo_latitude" value="{{ $election->geo_latitude }}">
                    <input type="hidden" name="geo_longitude" value="{{ $election->geo_longitude }}">
                    <input type="hidden" name="geo_radius" value="{{ $election->geo_radius_meters }}">

                    <div class="space-y-8">
                        <!-- Basic Info & Settings Tab -->
                        <div x-show="activeTab === 'basic'"
                             x-transition:enter="transition ease-out duration-500"
                             x-transition:enter-start="opacity-0 translate-y-10"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="space-y-8">

                            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-8">
                                <div class="flex items-center gap-4 mb-8">
                                    <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center text-slate-600">
                                        <i class="ri-global-line text-lg"></i>
                                    </div>
                                    <h3 class="text-xl font-bold text-slate-900 uppercase tracking-tight">General Information</h3>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    <div class="md:col-span-2 space-y-2">
                                        <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest ml-1">Election Title</label>
                                        <div class="relative group">
                                            <i class="ri-text absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                                            <input type="text" name="title" x-model="formData.title" required
                                                   class="w-full bg-slate-50 border-none rounded-xl px-14 py-4 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all shadow-sm"
                                                   placeholder="e.g. Student Council Elections 2024">
                                        </div>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest ml-1">Registration Deadline</label>
                                        <div class="relative group">
                                            <i class="ri-calendar-todo-line absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                                            <input type="datetime-local" name="registration_deadline" x-model="formData.registration_deadline"
                                                   class="w-full bg-slate-50 border-none rounded-xl px-14 py-4 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all shadow-sm">
                                        </div>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest ml-1">Maximum Votes Per Voter</label>
                                        <div class="relative group">
                                            <i class="ri-numbers-line absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                                            <input type="number" name="max_votes" x-model="formData.max_votes" min="1"
                                                   class="w-full bg-slate-50 border-none rounded-xl px-14 py-4 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all shadow-sm">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-8">
                                <div class="flex items-center gap-4 mb-8">
                                    <div class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600">
                                        <i class="ri-map-pin-user-line text-lg"></i>
                                    </div>
                                    <h3 class="text-xl font-bold text-slate-900 uppercase tracking-tight">Access Control</h3>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <label class="flex items-center justify-between p-6 bg-slate-50 rounded-2xl cursor-pointer hover:bg-white hover:shadow-xl hover:shadow-indigo-100 transition-all border border-transparent hover:border-indigo-100 group">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-indigo-600 shadow-sm group-hover:scale-110 transition-transform">
                                                <i class="ri-map-pin-2-line text-xl"></i>
                                            </div>
                                            <div>
                                                <span class="block text-sm font-bold text-slate-800 uppercase tracking-tight">Geographic Voting</span>
                                                <span class="block text-[11px] text-slate-500 font-medium mt-0.5 leading-relaxed">Limit voting actions to the radius</span>
                                            </div>
                                        </div>
                                        <div class="relative inline-block w-12 h-7 transition duration-200 ease-in-out">
                                            <input type="checkbox" name="enable_geo_location" x-model="formData.enable_geo_location"
                                                   class="peer opacity-0 w-0 h-0" value="1">
                                            <span class="absolute cursor-pointer top-0 left-0 right-0 bottom-0 bg-slate-200 transition-all duration-300 rounded-full before:absolute before:content-[''] before:h-5 before:w-5 before:left-1 before:bottom-1 before:bg-white before:transition-all before:duration-300 before:rounded-full peer-checked:bg-emerald-500 peer-checked:before:translate-x-5"></span>
                                        </div>
                                    </label>

                                    <label class="flex items-center justify-between p-6 bg-slate-50 rounded-2xl cursor-pointer hover:bg-white hover:shadow-xl hover:shadow-indigo-100 transition-all border border-transparent hover:border-indigo-100 group">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-indigo-600 shadow-sm group-hover:scale-110 transition-transform">
                                                <i class="ri-shield-user-line text-xl"></i>
                                            </div>
                                            <div>
                                                <span class="block text-sm font-bold text-slate-800 uppercase tracking-tight">Area Verification</span>
                                                <span class="block text-[11px] text-slate-500 font-medium mt-0.5 leading-relaxed">Require location access</span>
                                            </div>
                                        </div>
                                        <div class="relative inline-block w-12 h-7 transition duration-200 ease-in-out">
                                            <input type="checkbox" name="enable_geo_registration" x-model="formData.enable_geo_registration"
                                                   class="peer opacity-0 w-0 h-0" value="1">
                                            <span class="absolute cursor-pointer top-0 left-0 right-0 bottom-0 bg-slate-200 transition-all duration-300 rounded-full before:absolute before:content-[''] before:h-5 before:w-5 before:left-1 before:bottom-1 before:bg-white before:transition-all before:duration-300 before:rounded-full peer-checked:bg-emerald-500 peer-checked:before:translate-x-5"></span>
                                        </div>
                                    </label>
                                </div>
                                <div class="mt-6 flex items-center gap-3 px-5 py-3 bg-amber-50 rounded-xl border border-amber-100">
                                    <i class="ri-information-line text-amber-600 text-lg"></i>
                                    <p class="text-[10px] text-amber-700 font-bold uppercase tracking-wider">Note: Geographic coordinates and radius must be configured during initial creation.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Positions & Candidates Tab -->
                        <div x-show="activeTab === 'positions'"
                             x-transition:enter="transition ease-out duration-500"
                             x-transition:enter-start="opacity-0 translate-y-10"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="space-y-8">

                            <template x-for="(position, pIndex) in positions" :key="pIndex">
                                <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden group hover:border-indigo-200 transition-all">
                                    <div class="bg-slate-50 px-8 py-6 border-b border-slate-200/60 flex items-center justify-between group-hover:bg-indigo-50/30 transition-all">
                                        <div class="flex items-center gap-6 flex-1">
                                            <div class="w-12 h-12 bg-slate-900 text-white rounded-xl flex items-center justify-center font-bold text-lg shadow-lg" x-text="pIndex + 1"></div>
                                            <div class="flex-1 space-y-1">
                                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] block">Position Category</label>
                                                <input type="text" :name="`positions[${pIndex}][name]`" x-model="position.name" required
                                                       class="bg-transparent border-none p-0 focus:ring-0 font-black text-2xl text-slate-900 w-full placeholder-slate-200"
                                                       placeholder="ENTER POSITION TITLE">
                                            </div>
                                            <input type="hidden" :name="`positions[${pIndex}][id]`" x-model="position.id">
                                        </div>
                                        <button type="button" @click="removePosition(pIndex)"
                                                class="w-14 h-14 flex items-center justify-center text-slate-400 hover:text-red-500 hover:bg-white rounded-2xl transition-all hover:shadow-xl hover:shadow-red-100">
                                            <i class="ri-delete-bin-line text-2xl"></i>
                                        </button>
                                    </div>

                                    <div class="p-10">
                                        <div class="flex items-center gap-4 mb-8">
                                            <span class="text-[11px] font-black text-slate-400 uppercase tracking-widest">Nominated Candidates</span>
                                            <div class="h-px flex-1 bg-slate-100"></div>
                                        </div>

                                        <div class="grid gap-6">
                                            <template x-for="(candidate, cIndex) in position.candidates" :key="cIndex">
                                                <div class="flex items-center gap-6 p-8 bg-slate-50 rounded-[2.5rem] border border-slate-100 transition-all hover:bg-white hover:border-indigo-200 hover:shadow-2xl hover:shadow-indigo-100 group/cand">
                                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 flex-1">
                                                        <div class="space-y-2">
                                                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">First Name</label>
                                                            <input type="text" :name="`positions[${pIndex}][candidates][${cIndex}][first_name]`" x-model="candidate.first_name" required
                                                                   class="w-full bg-white border-none rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-indigo-500/10 shadow-sm" placeholder="First Name">
                                                        </div>
                                                        <div class="space-y-2">
                                                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Middle Name</label>
                                                            <input type="text" :name="`positions[${pIndex}][candidates][${cIndex}][middle_name]`" x-model="candidate.middle_name"
                                                                   class="w-full bg-white border-none rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-indigo-500/10 shadow-sm" placeholder="Optional">
                                                        </div>
                                                        <div class="space-y-2">
                                                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Last Name</label>
                                                            <input type="text" :name="`positions[${pIndex}][candidates][${cIndex}][last_name]`" x-model="candidate.last_name" required
                                                                   class="w-full bg-white border-none rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-indigo-500/10 shadow-sm" placeholder="Last Name">
                                                        </div>
                                                        <input type="hidden" :name="`positions[${pIndex}][candidates][${cIndex}][id]`" x-model="candidate.id">
                                                    </div>
                                                    <button type="button" @click="removeCandidate(pIndex, cIndex)"
                                                            class="w-12 h-12 flex items-center justify-center text-slate-300 hover:text-red-500 hover:bg-white rounded-xl transition-all shadow-none hover:shadow-lg">
                                                        <i class="ri-close-line text-2xl"></i>
                                                    </button>
                                                </div>
                                            </template>

                                            <button type="button" @click="addCandidate(pIndex)"
                                                    class="w-full py-6 border-2 border-dashed border-slate-200 rounded-[2.5rem] text-sm font-black text-slate-400 hover:border-indigo-400 hover:text-indigo-600 hover:bg-indigo-50/50 transition-all flex items-center justify-center gap-3">
                                                <i class="ri-add-circle-line text-xl"></i>
                                                ADD NEW CANDIDATE
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <button type="button" @click="addPosition()"
                                    class="w-full py-10 border-4 border-dashed border-slate-200 rounded-[4rem] text-slate-400 font-black hover:border-indigo-400 hover:text-indigo-600 hover:bg-white hover:shadow-2xl hover:shadow-indigo-100 transition-all flex flex-col items-center justify-center gap-4 group">
                                <div class="w-16 h-16 bg-slate-50 text-slate-400 rounded-3xl flex items-center justify-center group-hover:bg-indigo-600 group-hover:text-white transition-all transform group-hover:rotate-90">
                                    <i class="ri-add-line text-3xl"></i>
                                </div>
                                <span class="uppercase tracking-[0.3em] text-xs">Create New Election Category</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </main>
    </div>
@endsection
