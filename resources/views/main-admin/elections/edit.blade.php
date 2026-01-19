@extends('layouts.app-main-admin')

@section('content')
    <div x-data="{
        isSubmitting: false,
        activeTab: 'basic',
        showSuccessToast: false,
        positions: @js($election->positions->map(fn($p) => [
            'id' => $p->id,
            'name' => $p->title,
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
            require_id_verification: @js((bool)$election->require_id_verification),
            enable_geo_location: @js((bool)$election->require_geo_verification),
            enable_geo_registration: @js((bool)$election->require_geo_registration),
            geo_latitude: @js($election->geo_latitude),
            geo_longitude: @js($election->geo_longitude),
            geo_radius: @js($election->geo_radius_meters)
        },
        radiusValue: @js($election->geo_radius_meters ?? 50),
        radiusUnit: 'meters',
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
    class="flex min-h-screen bg-slate-50/50">

        <main class="flex-1 min-w-0 overflow-hidden">
            <!-- Success Toast -->
            <div x-show="showSuccessToast"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-y-4"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 class="fixed bottom-10 right-10 z-[100] bg-slate-900 text-white px-8 py-5 rounded-[2rem] shadow-2xl flex items-center gap-4 border border-slate-800"
                 style="display: none;">
                <div class="w-12 h-12 bg-emerald-500 rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-500/20">
                    <i class="ri-check-line text-2xl"></i>
                </div>
                <div>
                    <p class="font-black text-sm uppercase tracking-tight">Changes Saved</p>
                    <p class="text-[11px] text-slate-400 font-bold uppercase tracking-wider">Election data updated successfully</p>
                </div>
            </div>

            <!-- Header -->
            <header class="bg-white/80 backdrop-blur-md border-b border-slate-200 sticky top-0 z-30">
                <div class="max-w-7xl mx-auto px-8 py-8">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-6">
                            <div class="w-16 h-16 bg-slate-900 rounded-2xl flex items-center justify-center shadow-2xl shadow-slate-200">
                                <i class="ri-settings-3-fill text-white text-3xl"></i>
                            </div>
                            <div>
                                <h1 class="text-3xl font-black text-slate-900 uppercase tracking-tighter">Election Console</h1>
                                <p class="text-xs text-blue-600 font-black uppercase tracking-[0.2em] mt-1">Configuring: {{ $election->title }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <a href="{{ route('admin.elections.index') }}"
                               class="px-8 py-4 bg-slate-100 text-slate-600 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-slate-200 transition-all">
                                Cancel
                            </a>
                            <button @click="submitForm()" :disabled="isSubmitting"
                                    class="px-10 py-4 bg-slate-900 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-blue-600 hover:shadow-2xl hover:shadow-blue-200 transition-all disabled:opacity-50">
                                <span x-show="!isSubmitting">Update Configuration</span>
                                <span x-show="isSubmitting" class="flex items-center gap-2">
                                    <i class="ri-loader-4-line animate-spin"></i> Processing...
                                </span>
                            </button>
                        </div>
                    </div>

                    <div class="mt-10 flex items-center gap-10">
                        <button @click="activeTab = 'basic'"
                                :class="activeTab === 'basic' ? 'text-blue-600' : 'text-slate-400 hover:text-slate-600'"
                                class="flex items-center gap-3 pb-6 relative transition-all group">
                            <i class="ri-equalizer-line text-xl"></i>
                            <span class="font-black text-xs uppercase tracking-[0.2em]">Core Settings</span>
                            <div x-show="activeTab === 'basic'"
                                 x-transition:enter="transition scale-x-100 duration-300"
                                 class="absolute bottom-0 left-0 w-full h-1 bg-blue-600 rounded-full"></div>
                        </button>
                        <button @click="activeTab = 'positions'"
                                :class="activeTab === 'positions' ? 'text-blue-600' : 'text-slate-400 hover:text-slate-600'"
                                class="flex items-center gap-3 pb-6 relative transition-all group">
                            <i class="ri-user-star-line text-xl"></i>
                            <span class="font-black text-xs uppercase tracking-[0.2em]">Ballot Structure</span>
                            <div x-show="activeTab === 'positions'"
                                 x-transition:enter="transition scale-x-100 duration-300"
                                 class="absolute bottom-0 left-0 w-full h-1 bg-blue-600 rounded-full"></div>
                        </button>
                    </div>
                </div>
            </header>

            <div class="max-w-7xl mx-auto p-10">
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

                    <div class="space-y-8">
                        <!-- Basic Info & Settings Tab -->
                        <div x-show="activeTab === 'basic'"
                             x-transition:enter="transition ease-out duration-500"
                             x-transition:enter-start="opacity-0 translate-y-10"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="space-y-8">

                            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-8">
                                <div class="flex items-center gap-4 mb-10">
                                    <div class="w-12 h-12 bg-slate-100 rounded-2xl flex items-center justify-center text-slate-900 shadow-inner">
                                        <i class="ri-global-line text-2xl"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-black text-slate-900 uppercase tracking-tighter">General Information</h3>
                                        <p class="text-[11px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">Define the base parameters of this election</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                                    <div class="md:col-span-2 space-y-3">
                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Election Title</label>
                                        <div class="relative group">
                                            <i class="ri-text absolute left-6 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-blue-600 transition-colors text-xl"></i>
                                            <input type="text" name="title" x-model="formData.title" required
                                                   class="w-full bg-slate-50 border-2 border-slate-100 rounded-[1.25rem] px-16 py-5 text-sm font-bold text-slate-700 focus:ring-0 focus:border-blue-600/20 focus:bg-white transition-all shadow-sm"
                                                   placeholder="e.g. Student Council Elections 2024">
                                        </div>
                                    </div>

                                    <div class="space-y-3">
                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Registration Deadline</label>
                                        <div class="relative group">
                                            <i class="ri-calendar-event-line absolute left-6 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-blue-600 transition-colors text-xl"></i>
                                            <input type="datetime-local" name="registration_deadline" x-model="formData.registration_deadline"
                                                   class="w-full bg-slate-50 border-2 border-slate-100 rounded-[1.25rem] px-16 py-5 text-sm font-bold text-slate-700 focus:ring-0 focus:border-blue-600/20 focus:bg-white transition-all shadow-sm">
                                        </div>
                                    </div>

                                    <div class="space-y-3">
                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Max Votes Per Voter</label>
                                        <div class="relative group">
                                            <i class="ri-list-ordered absolute left-6 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-blue-600 transition-colors text-xl"></i>
                                            <input type="number" name="max_votes" x-model="formData.max_votes" min="1"
                                                   class="w-full bg-slate-50 border-2 border-slate-100 rounded-[1.25rem] px-16 py-5 text-sm font-bold text-slate-700 focus:ring-0 focus:border-blue-600/20 focus:bg-white transition-all shadow-sm">
                                        </div>
                                    </div>

                                    <!-- Enhanced Geo Configuration -->
                                    <div x-show="formData.enable_geo_location || formData.enable_geo_registration"
                                         x-transition:enter="transition ease-out duration-300"
                                         x-transition:enter-start="opacity-0 transform translate-y-4"
                                         x-transition:enter-end="opacity-100 transform translate-y-0"
                                         class="bg-white border-2 border-slate-100 rounded-3xl p-8 space-y-8 shadow-xl shadow-slate-200/40">
                                        <div class="flex items-center gap-4 mb-6">
                                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-200/50">
                                                <i class="ri-map-pin-2-line text-white text-2xl"></i>
                                            </div>
                                            <div>
                                                <h3 class="text-xl font-black text-slate-900 uppercase tracking-tighter">Location Configuration</h3>
                                                <p class="text-[11px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">Set the voting location and radius</p>
                                            </div>
                                        </div>

                                        <!-- Location Search -->
                                        <div class="space-y-4">
                                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Search Location</label>
                                            <div class="flex flex-col sm:flex-row gap-4">
                                                <div class="flex-1 relative group">
                                                    <i class="ri-search-line absolute left-6 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-blue-600 transition-colors text-xl"></i>
                                                    <input type="text" id="locationSearch" placeholder="Search for a location..."
                                                           class="w-full bg-slate-50 border-2 border-slate-100 rounded-[1.25rem] px-16 py-5 text-sm font-bold text-slate-700 focus:ring-0 focus:border-blue-600/20 focus:bg-white transition-all shadow-sm">
                                                </div>
                                                <button type="button" id="useMyLocation"
                                                        class="px-8 py-5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-[1.25rem] font-black text-xs uppercase tracking-widest hover:shadow-xl hover:shadow-blue-200 transition-all flex items-center justify-center gap-3">
                                                    <i class="ri-focus-3-line text-xl"></i>
                                                    Use My Location
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Radius Control -->
                                        <div class="space-y-4">
                                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Voting Radius</label>
                                            <div class="flex flex-col sm:flex-row gap-4">
                                                <div class="flex-1 flex gap-4">
                                                    <div class="flex-1 relative group">
                                                        <i class="ri-compass-3-line absolute left-6 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-blue-600 transition-colors text-xl"></i>
                                                        <input type="number" id="geoRadius" x-model="radiusValue" min="10" max="10000"
                                                               class="w-full bg-slate-50 border-2 border-slate-100 rounded-[1.25rem] px-16 py-5 text-sm font-bold text-slate-700 focus:ring-0 focus:border-blue-600/20 focus:bg-white transition-all shadow-sm"
                                                               placeholder="Radius">
                                                    </div>
                                                    <div class="w-40">
                                                        <select x-model="radiusUnit"
                                                                class="w-full bg-slate-50 border-2 border-slate-100 rounded-[1.25rem] px-6 py-5 text-sm font-bold text-slate-700 focus:ring-0 focus:border-blue-600/20 focus:bg-white transition-all shadow-sm appearance-none cursor-pointer">
                                                            <option value="meters">Meters</option>
                                                            <option value="kilometers">Km</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <button type="button" onclick="updateRadius()"
                                                        class="px-8 py-5 bg-slate-900 text-white rounded-[1.25rem] font-black text-xs uppercase tracking-widest hover:bg-blue-600 transition-all">
                                                    Apply Radius
                                                </button>
                                                <input type="hidden" id="computedRadius" name="geo_radius" :value="radiusUnit === 'kilometers' ? radiusValue * 1000 : radiusValue">
                                            </div>
                                            <div class="flex items-center gap-2 text-blue-600 font-bold text-[10px] uppercase tracking-widest ml-1">
                                                <i class="ri-information-line"></i>
                                                <span x-text="radiusUnit === 'kilometers' ? (radiusValue * 1000) + ' meters' : radiusValue + ' meters'"></span> from the center point
                                            </div>
                                        </div>

                                        <!-- Interactive Map -->
                                        <div class="space-y-4">
                                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Set Voting Zone</label>
                                            <div id="geoMap" class="w-full h-[400px] rounded-[2.5rem] border-4 border-slate-50 overflow-hidden shadow-2xl relative z-10"></div>
                                            <input type="hidden" id="geoLatitude" name="geo_latitude" x-model="formData.geo_latitude">
                                            <input type="hidden" id="geoLongitude" name="geo_longitude" x-model="formData.geo_longitude">
                                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest ml-1 mt-2">Click on the map to set the center of your voting zone</p>
                                        </div>

                                        <!-- Coordinate Display -->
                                        <div class="grid grid-cols-2 gap-4 p-6 bg-slate-50 rounded-[1.25rem] border-2 border-slate-100">
                                            <div class="flex flex-col gap-1">
                                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Latitude</span>
                                                <span id="latDisplay" class="text-sm font-bold text-slate-900 font-mono" x-text="formData.geo_latitude || 'Not set'"></span>
                                            </div>
                                            <div class="flex flex-col gap-1">
                                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Longitude</span>
                                                <span id="lngDisplay" class="text-sm font-bold text-slate-900 font-mono" x-text="formData.geo_longitude || 'Not set'"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-200/60 p-6 sm:p-10">
                                <div class="flex items-center gap-4 mb-10">
                                    <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 shadow-inner flex-shrink-0">
                                        <i class="ri-shield-keyhole-line text-2xl"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-black text-slate-900 uppercase tracking-tighter">Security & Compliance</h3>
                                        <p class="text-[11px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">Configure access control and verification</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-8">
                                    <label class="flex items-center justify-between p-5 sm:p-8 bg-slate-50 rounded-[2rem] cursor-pointer hover:bg-white hover:shadow-2xl hover:shadow-blue-100 transition-all border-2 border-transparent hover:border-blue-100 group">
                                        <div class="flex items-center gap-3 sm:gap-5">
                                            <div class="w-12 h-12 sm:w-14 sm:h-14 bg-white rounded-2xl flex items-center justify-center text-blue-600 shadow-sm group-hover:scale-110 transition-transform flex-shrink-0">
                                                <i class="ri-map-pin-line text-xl sm:text-2xl"></i>
                                            </div>
                                            <div>
                                                <span class="block text-xs sm:text-sm font-black text-slate-900 uppercase tracking-tight">Geofencing</span>
                                                <span class="block text-[9px] sm:text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-1">Restrict Location</span>
                                            </div>
                                        </div>
                                        <div class="relative inline-block w-12 sm:w-14 h-7 sm:h-8 transition duration-200 ease-in-out flex-shrink-0">
                                            <input type="checkbox" name="enable_geo_location" x-model="formData.enable_geo_location"
                                                   class="peer opacity-0 w-0 h-0" value="1">
                                            <span class="absolute cursor-pointer top-0 left-0 right-0 bottom-0 bg-slate-200 transition-all duration-300 rounded-full before:absolute before:content-[''] before:h-5 sm:before:h-6 before:w-5 sm:before:w-6 before:left-1 before:bottom-1 before:bg-white before:transition-all before:duration-300 before:rounded-full peer-checked:bg-emerald-500 peer-checked:before:translate-x-5 sm:peer-checked:before:translate-x-6"></span>
                                        </div>
                                    </label>

                                    <label class="flex items-center justify-between p-5 sm:p-8 bg-slate-50 rounded-[2rem] cursor-pointer hover:bg-white hover:shadow-2xl hover:shadow-blue-100 transition-all border-2 border-transparent hover:border-blue-100 group">
                                        <div class="flex items-center gap-3 sm:gap-5">
                                            <div class="w-12 h-12 sm:w-14 sm:h-14 bg-white rounded-2xl flex items-center justify-center text-blue-600 shadow-sm group-hover:scale-110 transition-transform flex-shrink-0">
                                                <i class="ri-shield-user-line text-xl sm:text-2xl"></i>
                                            </div>
                                            <div>
                                                <span class="block text-xs sm:text-sm font-black text-slate-900 uppercase tracking-tight">Area Verification</span>
                                                <span class="block text-[9px] sm:text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-1">Biometric or GPS</span>
                                            </div>
                                        </div>
                                        <div class="relative inline-block w-12 sm:w-14 h-7 sm:h-8 transition duration-200 ease-in-out flex-shrink-0">
                                            <input type="checkbox" name="enable_geo_registration" x-model="formData.enable_geo_registration"
                                                   class="peer opacity-0 w-0 h-0" value="1">
                                            <span class="absolute cursor-pointer top-0 left-0 right-0 bottom-0 bg-slate-200 transition-all duration-300 rounded-full before:absolute before:content-[''] before:h-5 sm:before:h-6 before:w-5 sm:before:w-6 before:left-1 before:bottom-1 before:bg-white before:transition-all before:duration-300 before:rounded-full peer-checked:bg-emerald-500 peer-checked:before:translate-x-5 sm:peer-checked:before:translate-x-6"></span>
                                        </div>
                                    </label>


                                </div>
                                <div class="mt-8 flex items-center gap-4 px-6 py-4 bg-blue-50/50 rounded-2xl border border-blue-100">
                                    <i class="ri-information-fill text-blue-600 text-xl"></i>
                                    <p class="text-[10px] text-blue-700 font-black uppercase tracking-[0.1em] leading-relaxed">Geographic constraints are locked after initial creation to preserve audit integrity. Contact system administrator for radical changes.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Positions & Candidates Tab -->
                        <div x-show="activeTab === 'positions'"
                             x-transition:enter="transition ease-out duration-500"
                             x-transition:enter-start="opacity-0 translate-y-10"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="space-y-10">

                            <template x-for="(position, pIndex) in positions" :key="pIndex">
                                <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-200/60 overflow-hidden group hover:border-blue-200 transition-all">
                                    <div class="bg-slate-50 px-10 py-8 border-b border-slate-200/60 flex items-center justify-between group-hover:bg-blue-50/30 transition-all">
                                        <div class="flex items-center gap-8 flex-1">
                                            <div class="w-14 h-14 bg-slate-900 text-white rounded-[1.25rem] flex items-center justify-center font-black text-xl shadow-xl shadow-slate-200" x-text="pIndex + 1"></div>
                                            <div class="flex-1 space-y-1">
                                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-[0.3em] block">Position Category</label>
                                                <input type="text" :name="`positions[${pIndex}][name]`" x-model="position.name" required
                                                       class="bg-transparent border-none p-0 focus:ring-0 font-black text-2xl text-slate-900 w-full placeholder-slate-200 uppercase tracking-tighter"
                                                       placeholder="ENTER POSITION TITLE">
                                            </div>
                                            <input type="hidden" :name="`positions[${pIndex}][id]`" x-model="position.id">
                                        </div>
                                        <button type="button" @click="removePosition(pIndex)"
                                                class="w-14 h-14 flex items-center justify-center text-slate-300 hover:text-red-500 hover:bg-white rounded-2xl transition-all hover:shadow-2xl hover:shadow-red-100">
                                            <i class="ri-delete-bin-6-line text-2xl"></i>
                                        </button>
                                    </div>

                                    <div class="p-10">
                                        <div class="flex items-center gap-6 mb-10">
                                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Candidates Nominated</span>
                                            <div class="h-px flex-1 bg-slate-100"></div>
                                        </div>

                                        <div class="grid gap-6">
                                            <template x-for="(candidate, cIndex) in position.candidates" :key="cIndex">
                                                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 sm:gap-8 p-6 sm:p-8 bg-slate-50/50 rounded-[2.5rem] border-2 border-slate-50 transition-all hover:bg-white hover:border-blue-200 hover:shadow-2xl hover:shadow-blue-100 group/cand">
                                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8 flex-1 w-full">
                                                        <div class="space-y-2">
                                                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">First Name</label>
                                                            <input type="text" :name="`positions[${pIndex}][candidates][${cIndex}][first_name]`" x-model="candidate.first_name" required
                                                                   class="w-full bg-white border-none rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-blue-500/10 shadow-sm" placeholder="First Name">
                                                        </div>
                                                        <div class="space-y-2">
                                                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Middle Name</label>
                                                            <input type="text" :name="`positions[${pIndex}][candidates][${cIndex}][middle_name]`" x-model="candidate.middle_name"
                                                                   class="w-full bg-white border-none rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-blue-500/10 shadow-sm" placeholder="Optional">
                                                        </div>
                                                        <div class="space-y-2">
                                                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Last Name</label>
                                                            <input type="text" :name="`positions[${pIndex}][candidates][${cIndex}][last_name]`" x-model="candidate.last_name" required
                                                                   class="w-full bg-white border-none rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-blue-500/10 shadow-sm" placeholder="Last Name">
                                                        </div>
                                                        <input type="hidden" :name="`positions[${pIndex}][candidates][${cIndex}][id]`" x-model="candidate.id">
                                                    </div>
                                                    <button type="button" @click="removeCandidate(pIndex, cIndex)"
                                                            class="w-12 h-12 flex items-center justify-center text-slate-300 hover:text-red-500 hover:bg-white rounded-xl transition-all self-end sm:self-center">
                                                        <i class="ri-close-circle-line text-2xl"></i>
                                                    </button>
                                                </div>
                                            </template>

                                            <button type="button" @click="addCandidate(pIndex)"
                                                    class="w-full py-8 border-2 border-dashed border-slate-200 rounded-[2.5rem] text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] hover:border-blue-400 hover:text-blue-600 hover:bg-blue-50/50 transition-all flex items-center justify-center gap-3">
                                                <i class="ri-add-circle-fill text-xl"></i>
                                                Nominate Candidate
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <button type="button" @click="addPosition()"
                                    class="w-full py-12 border-4 border-dashed border-slate-200 rounded-[4rem] text-slate-400 font-black hover:border-blue-400 hover:text-blue-600 hover:bg-white hover:shadow-2xl hover:shadow-blue-100 transition-all flex flex-col items-center justify-center gap-6 group">
                                <div class="w-20 h-20 bg-slate-50 text-slate-400 rounded-3xl flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-all transform group-hover:rotate-90 shadow-inner">
                                    <i class="ri-add-line text-4xl"></i>
                                </div>
                                <span class="uppercase tracking-[0.4em] text-[10px]">Create New Ballot Category</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </main>
    </div>
    <!-- Google Maps API -->
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places"></script>

    <script>
        let map, marker, circle;

        function initGeoMap() {
            const lat = parseFloat(@js($election->geo_latitude)) || 14.5995;
            const lng = parseFloat(@js($election->geo_longitude)) || 120.9842;
            const defaultPos = { lat, lng };

            map = new google.maps.Map(document.getElementById('geoMap'), {
                zoom: @js($election->geo_latitude ? 17 : 13),
                center: defaultPos,
                styles: [
                    { "featureType": "poi", "stylers": [{ "visibility": "off" }] },
                    { "featureType": "transit", "stylers": [{ "visibility": "off" }] }
                ]
            });

            if (@js((bool)$election->geo_latitude)) {
                setLocation(lat, lng);
            }

            map.addListener('click', function(e) {
                setLocation(e.latLng.lat(), e.latLng.lng());
            });

            const input = document.getElementById('locationSearch');
            const autocomplete = new google.maps.places.Autocomplete(input);
            autocomplete.bindTo('bounds', map);
            autocomplete.addListener('place_changed', function() {
                const place = autocomplete.getPlace();
                if (!place.geometry || !place.geometry.location) return;
                if (place.geometry.viewport) map.fitBounds(place.geometry.viewport);
                else {
                    map.setCenter(place.geometry.location);
                    map.setZoom(17);
                }
                setLocation(place.geometry.location.lat(), place.geometry.location.lng());
            });

            document.getElementById('useMyLocation').addEventListener('click', function() {
                const btn = this;
                const originalContent = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<i class="ri-loader-4-line animate-spin mr-2"></i>Locating...';

            // Ensure we use high accuracy and watch for changes if needed
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const pos = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };

                    // Use a higher zoom for better precision confirmation
                    map.setCenter(pos);
                    map.setZoom(19); // Increased zoom for better accuracy display
                    setLocation(pos.lat, pos.lng);

                    btn.disabled = false;
                    btn.innerHTML = originalContent;
                }, (error) => {
                        btn.disabled = false;
                        btn.innerHTML = originalContent;
                        let msg = 'Error: The Geolocation service failed.';
                        if (error.code === 1) msg = 'Error: Permission denied.';
                        else if (error.code === 2) msg = 'Error: Position unavailable.';
                        else if (error.code === 3) msg = 'Error: Timeout.';
                        alert(msg);
                    }, {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0
                    });
                } else {
                    btn.disabled = false;
                    btn.innerHTML = originalContent;
                    alert("Error: Your browser doesn't support geolocation.");
                }
            });
        }

        function setLocation(lat, lng) {
            const pos = { lat, lng };
            if (marker) marker.setMap(null);
            if (circle) circle.setMap(null);

            marker = new google.maps.Marker({ position: pos, map: map, animation: google.maps.Animation.DROP });

            const radiusInput = document.getElementById('geoRadius');
            const radiusValue = parseFloat(radiusInput.value) || 50;
            const radiusUnit = document.querySelector('[x-model="radiusUnit"]').value;
            let radius = radiusUnit === 'kilometers' ? radiusValue * 1000 : radiusValue;

            circle = new google.maps.Circle({
                strokeColor: '#4F46E5', strokeOpacity: 0.8, strokeWeight: 2,
                fillColor: '#818CF8', fillOpacity: 0.35, map: map, center: pos, radius: radius
            });

            document.getElementById('geoLatitude').value = lat;
            document.getElementById('geoLongitude').value = lng;
            document.getElementById('latDisplay').textContent = lat.toFixed(6);
            document.getElementById('lngDisplay').textContent = lng.toFixed(6);
            document.getElementById('computedRadius').value = radius;

            // Update Alpine data if available
            const form = document.getElementById('edit-election-form');
            if (form && Alpine.$data(form)) {
                Alpine.$data(form).formData.geo_latitude = lat;
                Alpine.$data(form).formData.geo_longitude = lng;
                Alpine.$data(form).formData.geo_radius = radius;
            }
        }

        function updateRadius() {
            const lat = parseFloat(document.getElementById('geoLatitude').value);
            const lng = parseFloat(document.getElementById('geoLongitude').value);
            if (!isNaN(lat) && !isNaN(lng)) setLocation(lat, lng);
        }

        document.addEventListener('DOMContentLoaded', () => {
            initGeoMap();
        });
    </script>
@endsection
