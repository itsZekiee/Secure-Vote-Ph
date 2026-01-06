@extends('layouts.app-main-admin')

@section('content')
    <div x-data="{
        collapsed: window.innerWidth < 1024,
        isMobile: window.innerWidth < 1024,
        isSubmitting: false,
        activeTab: 'basic',
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
            enable_geo_location: @js($election->require_geo_verification),
            enable_geo_registration: @js($election->require_geo_registration)
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
        }
    }"
    class="flex min-h-screen bg-gray-50">

        <x-admin-sidebar />

        <main class="flex-1">
            <header class="bg-white border-b border-gray-200 px-8 py-6 sticky top-0 z-10">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                         <div class="w-12 h-12 bg-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-600/20">
                            <i class="fas fa-edit text-white"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Edit Election Details</h1>
                            <p class="text-sm text-gray-600 mt-1">Election: {{ $election->title }}</p>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ route('admin.elections.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                            Cancel
                        </a>
                        <button type="submit" form="edit-election-form" :disabled="isSubmitting"
                                class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors disabled:opacity-50">
                            <span x-show="!isSubmitting">Save Changes</span>
                            <span x-show="isSubmitting">Saving...</span>
                        </button>
                    </div>
                </div>

                <div class="mt-6 flex items-center gap-4 border-t pt-4">
                    <button @click="activeTab = 'basic'" :class="activeTab === 'basic' ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-500 hover:text-gray-700'" class="pb-2 px-1 font-bold text-sm transition-all">
                        Basic & Settings
                    </button>
                    <button @click="activeTab = 'positions'" :class="activeTab === 'positions' ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-500 hover:text-gray-700'" class="pb-2 px-1 font-bold text-sm transition-all">
                        Positions & Candidates
                    </button>
                </div>
            </header>

            <div class="p-8 max-w-5xl mx-auto">
                @if($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
                        <div class="flex items-center gap-3 text-red-700 font-bold mb-2">
                            <i class="fas fa-exclamation-circle"></i>
                            Please fix the following errors:
                        </div>
                        <ul class="list-disc list-inside text-sm text-red-600">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
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
                        <div x-show="activeTab === 'basic'" class="space-y-6">
                            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-8 space-y-6">
                                <h3 class="text-lg font-bold text-gray-900 border-b pb-4">General Settings</h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Election Title</label>
                                        <input type="text" name="title" x-model="formData.title" required
                                               class="w-full rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent px-5 py-3 transition-all"
                                               placeholder="Election Title">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Registration Deadline</label>
                                        <input type="datetime-local" name="registration_deadline" x-model="formData.registration_deadline"
                                               class="w-full rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent px-5 py-3 transition-all">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Max Number of Votes</label>
                                        <input type="number" name="max_votes" x-model="formData.max_votes" min="1"
                                               class="w-full rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent px-5 py-3 transition-all">
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-8 space-y-6">
                                <h3 class="text-lg font-bold text-gray-900 border-b pb-4">Geographic Restrictions</h3>

                                <div class="space-y-4">
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-100">
                                        <div>
                                            <p class="font-bold text-gray-900">Geographic Restriction (Voting)</p>
                                            <p class="text-xs text-gray-500">Restrict voting to the designated area</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="enable_geo_location" x-model="formData.enable_geo_location" value="1" class="sr-only peer">
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                        </label>
                                    </div>

                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-100">
                                        <div>
                                            <p class="font-bold text-gray-900">Registration & Sign-in Restriction</p>
                                            <p class="text-xs text-gray-500">Require area verification for registration/login</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="enable_geo_registration" x-model="formData.enable_geo_registration" value="1" class="sr-only peer">
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                        </label>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-400 italic font-medium"><i class="fas fa-info-circle mr-1"></i> Note: Coordinates and radius are set during election creation.</p>
                            </div>
                        </div>

                        <!-- Positions & Candidates Tab -->
                        <div x-show="activeTab === 'positions'" class="space-y-8">
                            <template x-for="(position, pIndex) in positions" :key="pIndex">
                                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden transition-all hover:shadow-md">
                                    <div class="bg-slate-50 px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                                        <div class="flex items-center gap-4 flex-1">
                                            <span class="w-8 h-8 bg-indigo-600 text-white rounded-lg flex items-center justify-center font-bold shadow-sm" x-text="pIndex + 1"></span>
                                            <div class="flex-1">
                                                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-0.5">Position Title</label>
                                                <input type="text" :name="`positions[${pIndex}][name]`" x-model="position.name" required
                                                       class="bg-transparent border-none p-0 focus:ring-0 font-bold text-xl text-gray-900 w-full placeholder-gray-300"
                                                       placeholder="e.g. President">
                                            </div>
                                            <input type="hidden" :name="`positions[${pIndex}][id]`" x-model="position.id">
                                        </div>
                                        <button type="button" @click="removePosition(pIndex)" class="w-10 h-10 flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-xl transition-all">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>

                                    <div class="p-6">
                                        <div class="flex items-center justify-between mb-4">
                                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Candidates List</label>
                                            <div class="h-px flex-1 bg-gray-100 mx-4"></div>
                                        </div>

                                        <div class="space-y-4">
                                            <template x-for="(candidate, cIndex) in position.candidates" :key="cIndex">
                                                <div class="group flex items-center gap-4 p-4 bg-gray-50 rounded-xl border border-gray-100 transition-all hover:bg-white hover:border-indigo-200 hover:shadow-sm">
                                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 flex-1">
                                                        <div>
                                                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">First Name</label>
                                                            <input type="text" :name="`positions[${pIndex}][candidates][${cIndex}][first_name]`" x-model="candidate.first_name" required
                                                                   class="w-full rounded-lg border-gray-200 text-sm focus:ring-indigo-500 focus:border-indigo-500 shadow-sm" placeholder="First Name">
                                                        </div>
                                                        <div>
                                                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Middle Name</label>
                                                            <input type="text" :name="`positions[${pIndex}][candidates][${cIndex}][middle_name]`" x-model="candidate.middle_name"
                                                                   class="w-full rounded-lg border-gray-200 text-sm focus:ring-indigo-500 focus:border-indigo-500 shadow-sm" placeholder="Optional">
                                                        </div>
                                                        <div>
                                                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Last Name</label>
                                                            <input type="text" :name="`positions[${pIndex}][candidates][${cIndex}][last_name]`" x-model="candidate.last_name" required
                                                                   class="w-full rounded-lg border-gray-200 text-sm focus:ring-indigo-500 focus:border-indigo-500 shadow-sm" placeholder="Last Name">
                                                        </div>
                                                        <input type="hidden" :name="`positions[${pIndex}][candidates][${cIndex}][id]`" x-model="candidate.id">
                                                    </div>
                                                    <button type="button" @click="removeCandidate(pIndex, cIndex)" class="w-8 h-8 flex items-center justify-center text-gray-300 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </template>

                                            <button type="button" @click="addCandidate(pIndex)"
                                                    class="w-full py-3 border-2 border-dashed border-gray-200 rounded-xl text-sm font-bold text-gray-400 hover:border-indigo-300 hover:text-indigo-600 hover:bg-indigo-50/30 transition-all flex items-center justify-center gap-2">
                                                <i class="fas fa-plus-circle text-xs"></i>
                                                Add Candidate
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <button type="button" @click="addPosition()"
                                    class="w-full py-6 border-2 border-dashed border-indigo-200 rounded-2xl text-indigo-400 font-bold hover:border-indigo-500 hover:text-indigo-600 hover:bg-indigo-50/50 transition-all flex flex-col items-center justify-center gap-3 group">
                                <div class="w-12 h-12 bg-indigo-50 text-indigo-400 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <i class="fas fa-plus text-xl"></i>
                                </div>
                                Create New Election Position
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </main>
    </div>
@endsection
