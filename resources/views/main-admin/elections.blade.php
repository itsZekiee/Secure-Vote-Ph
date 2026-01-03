@extends('layouts.app-main-admin')

@section('content')
    <div x-data="{
        collapsed: false,
        isMobile: window.innerWidth < 1024,
         }"
         x-init="window.addEventListener('resize', () => { isMobile = window.innerWidth < 1024 })"
         class="flex min-h-screen bg-gradient-to-br from-gray-50 via-blue-50/30 to-gray-50">

        <x-admin-sidebar />

        <!-- Main Content -->
        <main class="flex-1">
            <!-- Enhanced Page Header -->
            <header class="bg-white/80 backdrop-blur-sm border-b border-gray-200/50 shadow-sm sticky top-0 z-10">
                <div class="px-8 py-6">
                    <div class="flex items-center justify-between gap-6">
                        <div class="flex items-center gap-5">
                            <div class="flex-shrink-0">
                                <div class="w-14 h-14 bg-gradient-to-br from-indigo-600 via-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-500/30 ring-4 ring-indigo-50">
                                    <svg class="w-7 h-7 text-white" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                              stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M15 2v5a1 1 0 001 1h5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                            </div>

                            <div>
                                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Create New Election</h1>
                                <p class="text-sm text-gray-600 leading-relaxed mt-0.5">Set up a comprehensive election with positions, candidates, and voting configurations</p>
                            </div>
                        </div>

                        <nav class="flex items-center" aria-label="Breadcrumb">
                            <ol class="flex items-center gap-3">
                                <li>
                                    <a href="{{ route('admin.elections.index') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-all">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                                  stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        Elections
                                    </a>
                                </li>
                                <li>
                                    <svg class="w-4 h-4 text-gray-300" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M9 6l6 6-6 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </li>
                                <li class="flex items-center">
                                    <div class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl text-sm font-semibold shadow-lg shadow-indigo-500/30">
                                        <div class="w-2 h-2 bg-white rounded-full animate-pulse"></div>
                                        Create
                                    </div>
                                </li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </header>

            <!-- Content Container -->
            <div class="p-8">
                <form id="electionForm" action="{{ route('admin.elections.store') }}" method="POST"
                      x-data="{
                          activeTab: 'basic',
                          electionCreated: false,
                          electionId: null,
                          electionCode: null,
                          registrationUrl: null,
                          positions: [{ name: '', candidates: [''] }],
                          formData: {
                              title: '',
                              voting_start: '',
                              voting_end: ''
                          },
                          validateBasicInfo() {
                              if (!this.formData.title.trim()) {
                                  alert('Election Title is required');
                                  return false;
                              }
                              if (!this.formData.voting_start) {
                                  alert('Voting Start date is required');
                                  return false;
                              }
                              if (!this.formData.voting_end) {
                                  alert('Voting End date is required');
                                  return false;
                              }
                              if (new Date(this.formData.voting_start) >= new Date(this.formData.voting_end)) {
                                  alert('Voting End must be after Voting Start');
                                  return false;
                              }
                              return true;
                          },
                          generateQRCode(text) {
                              const qrContainer = document.getElementById('qrCodeDisplay');
                              qrContainer.innerHTML = '';
                              new QRCode(qrContainer, {
                                  text: text,
                                  width: 256,
                                  height: 256,
                                  colorDark: '#4F46E5',
                                  colorLight: '#ffffff',
                                  correctLevel: QRCode.CorrectLevel.H
                              });
                          },
                          copyToClipboard(text) {
                              navigator.clipboard.writeText(text).then(() => {
                                  alert('Copied to clipboard!');
                              });
                          }
                      }"
                      class="max-w-7xl mx-auto">
                    @csrf

                    <!-- Enhanced Progress Stepper -->
                    <div class="mb-10">
                        <div class="bg-white/80 backdrop-blur-sm p-8 border border-gray-200/50 rounded-2xl shadow-xl shadow-gray-200/50">
                            <div class="flex items-center justify-between">
                                <div class="flex-1 flex items-center gap-8">
                                    <!-- Step 1 -->
                                    <div class="flex items-center gap-4 flex-1">
                                        <div :class="activeTab === 'basic' ? 'bg-gradient-to-br from-indigo-600 to-purple-600 text-white shadow-lg shadow-indigo-500/30' : 'bg-white text-gray-400 border-2 border-gray-200'"
                                             class="w-12 h-12 rounded-xl flex items-center justify-center font-bold transition-all duration-300 flex-shrink-0">
                                            <span class="text-base">1</span>
                                        </div>
                                        <div class="min-w-0">
                                            <div :class="activeTab === 'basic' ? 'text-indigo-700 font-bold' : 'text-gray-600 font-medium'" class="text-sm">Basic Information</div>
                                            <div class="text-xs text-gray-500 mt-0.5">Election details</div>
                                        </div>
                                    </div>

                                    <div class="flex-1 h-1 bg-gray-200 rounded-full mx-4 relative overflow-hidden">
                                        <div :class="['candidates', 'settings', 'share'].includes(activeTab) ? 'w-full' : 'w-0'"
                                             class="absolute inset-0 bg-gradient-to-r from-indigo-600 to-purple-600 transition-all duration-500 ease-out rounded-full"></div>
                                    </div>

                                    <!-- Step 2 -->
                                    <div class="flex items-center gap-4 flex-1">
                                        <div :class="activeTab === 'candidates' ? 'bg-gradient-to-br from-indigo-600 to-purple-600 text-white shadow-lg shadow-indigo-500/30' : 'bg-white text-gray-400 border-2 border-gray-200'"
                                             class="w-12 h-12 rounded-xl flex items-center justify-center font-bold transition-all duration-300 flex-shrink-0">
                                            <span class="text-base">2</span>
                                        </div>
                                        <div class="min-w-0">
                                            <div :class="activeTab === 'candidates' ? 'text-indigo-700 font-bold' : 'text-gray-600 font-medium'" class="text-sm">Positions & Candidates</div>
                                            <div class="text-xs text-gray-500 mt-0.5">Setup voting options</div>
                                        </div>
                                    </div>

                                    <div class="flex-1 h-1 bg-gray-200 rounded-full mx-4 relative overflow-hidden">
                                        <div :class="['settings', 'share'].includes(activeTab) ? 'w-full' : 'w-0'"
                                             class="absolute inset-0 bg-gradient-to-r from-indigo-600 to-purple-600 transition-all duration-500 ease-out rounded-full"></div>
                                    </div>

                                    <!-- Step 3 -->
                                    <div class="flex items-center gap-4 flex-1">
                                        <div :class="activeTab === 'settings' ? 'bg-gradient-to-br from-indigo-600 to-purple-600 text-white shadow-lg shadow-indigo-500/30' : 'bg-white text-gray-400 border-2 border-gray-200'"
                                             class="w-12 h-12 rounded-xl flex items-center justify-center font-bold transition-all duration-300 flex-shrink-0">
                                            <span class="text-base">3</span>
                                        </div>
                                        <div class="min-w-0">
                                            <div :class="activeTab === 'settings' ? 'text-indigo-700 font-bold' : 'text-gray-600 font-medium'" class="text-sm">Voting Settings</div>
                                            <div class="text-xs text-gray-500 mt-0.5">Configure restrictions</div>
                                        </div>
                                    </div>

                                    <div class="flex-1 h-1 bg-gray-200 rounded-full mx-4 relative overflow-hidden">
                                        <div :class="activeTab === 'share' ? 'w-full' : 'w-0'"
                                             class="absolute inset-0 bg-gradient-to-r from-indigo-600 to-purple-600 transition-all duration-500 ease-out rounded-full"></div>
                                    </div>

                                    <!-- Step 4 -->
                                    <div class="flex items-center gap-4 flex-1">
                                        <div :class="activeTab === 'share' ? 'bg-gradient-to-br from-indigo-600 to-purple-600 text-white shadow-lg shadow-indigo-500/30' : 'bg-white text-gray-400 border-2 border-gray-200'"
                                             class="w-12 h-12 rounded-xl flex items-center justify-center font-bold transition-all duration-300 flex-shrink-0">
                                            <span class="text-base">4</span>
                                        </div>
                                        <div class="min-w-0">
                                            <div :class="activeTab === 'share' ? 'text-indigo-700 font-bold' : 'text-gray-600 font-medium'" class="text-sm">Share Election</div>
                                            <div class="text-xs text-gray-500 mt-0.5">QR & Access Codes</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Main Content Card -->
                    <div class="bg-white/80 backdrop-blur-sm rounded-3xl shadow-2xl shadow-gray-200/50 border border-gray-200/50 overflow-hidden">
                        <div class="p-12">
                            <!-- Panel 1: Basic Information -->
                            <section x-show="activeTab === 'basic'"
                                     x-transition:enter="transition ease-out duration-300"
                                     x-transition:enter-start="opacity-0 transform translate-x-4"
                                     x-transition:enter-end="opacity-100 transform translate-x-0"
                                     aria-labelledby="basic-heading">
                                <div class="mb-10">
                                    <div class="flex items-center gap-3 mb-3">
                                        <div class="w-10 h-10 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-xl flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none">
                                                <path d="M13 2L3 14h8l-1 8 10-12h-8l1-8z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </div>
                                        <h2 id="basic-heading" class="text-3xl font-bold text-gray-900">Basic Information</h2>
                                    </div>
                                    <p class="text-gray-600 text-base">Provide fundamental details about your election</p>
                                </div>

                                <div class="space-y-8">
                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                        <div class="lg:col-span-2">
                                            <label for="formTitle" class="block text-sm font-semibold text-gray-900 mb-3">
                                                Election Title <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" id="formTitle" name="title" required
                                                   x-model="formData.title"
                                                   class="block w-full rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent px-5 py-4 text-base transition-all"
                                                   placeholder="e.g., 2025 General Election">
                                        </div>

                                        <div class="lg:col-span-2">
                                            <label for="organization" class="block text-sm font-semibold text-gray-900 mb-3">Organization</label>
                                            <select id="organization" name="organization_id" required
                                                    class="block w-full rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent px-5 py-4 text-base transition-all">
                                                <option value="">Select organization</option>
                                                @foreach($organizations ?? [] as $org)
                                                    <option value="{{ $org->id }}">{{ $org->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="lg:col-span-2">
                                            <label for="description" class="block text-sm font-semibold text-gray-900 mb-3">Description</label>
                                            <textarea id="description" name="description" rows="5"
                                                      class="block w-full rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent px-5 py-4 text-base transition-all"
                                                      placeholder="Describe the scope and purpose of this election"></textarea>
                                        </div>

                                        <div>
                                            <label for="votingStart" class="block text-sm font-semibold text-gray-900 mb-3">
                                                Voting Start <span class="text-red-500">*</span>
                                            </label>
                                            <input type="datetime-local" id="votingStart" name="voting_start" required
                                                   x-model="formData.voting_start"
                                                   class="block w-full rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent px-5 py-4 text-base transition-all">
                                        </div>

                                        <div>
                                            <label for="votingEnd" class="block text-sm font-semibold text-gray-900 mb-3">
                                                Voting End <span class="text-red-500">*</span>
                                            </label>
                                            <input type="datetime-local" id="votingEnd" name="voting_end" required
                                                   x-model="formData.voting_end"
                                                   class="block w-full rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent px-5 py-4 text-base transition-all">
                                        </div>
                                    </div>

                                    <div class="flex justify-end pt-10 border-t border-gray-200">
                                        <button type="button"
                                                @click="if (validateBasicInfo()) { activeTab = 'candidates' }"
                                                class="group px-10 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:shadow-xl hover:shadow-indigo-500/30 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all font-semibold text-base">
                                            Continue
                                            <svg class="w-5 h-5 ml-2 inline-block group-hover:translate-x-1 transition-transform" viewBox="0 0 24 24" fill="none" aria-hidden>
                                                <path d="M5 12h14M12 5l7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </section>

                            <!-- Panel 2: Positions & Candidates -->
                            <section x-show="activeTab === 'candidates'"
                                     x-transition:enter="transition ease-out duration-300"
                                     x-transition:enter-start="opacity-0 transform translate-x-4"
                                     x-transition:enter-end="opacity-100 transform translate-x-0"
                                     aria-labelledby="positions-heading">
                                <div class="mb-10">
                                    <div class="flex items-center gap-3 mb-3">
                                        <div class="w-10 h-10 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-xl flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none">
                                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M9 7a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </div>
                                        <h2 id="positions-heading" class="text-3xl font-bold text-gray-900">Positions & Candidates</h2>
                                    </div>
                                    <p class="text-gray-600 text-base">Define the positions available for voting and their candidates</p>
                                </div>

                                <div class="space-y-6">
                                    <template x-for="(position, index) in positions" :key="index">
                                        <div class="border-2 border-gray-200 rounded-2xl p-8 bg-gradient-to-br from-white to-gray-50 hover:border-indigo-300 transition-all duration-300 shadow-sm hover:shadow-lg">
                                            <div class="flex items-start justify-between gap-4 mb-6">
                                                <div class="flex-1">
                                                    <label class="block text-sm font-semibold text-gray-900 mb-3">Position name</label>
                                                    <input type="text" x-model="position.name" :name="`positions[${index}][name]`"
                                                           class="block w-full rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent px-5 py-4 text-base transition-all"
                                                           placeholder="e.g., Mayor">
                                                </div>
                                            </div>

                                            <div class="space-y-4">
                                                <label class="block text-sm font-semibold text-gray-900 mb-3">Candidates</label>
                                                <template x-for="(c, cidx) in position.candidates" :key="cidx">
                                                    <div class="flex items-center gap-3">
                                                        <div class="flex-1">
                                                            <input type="text" x-model="position.candidates[cidx]" :name="`positions[${index}][candidates][${cidx}]`"
                                                                   class="block w-full rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent px-5 py-3 text-base transition-all"
                                                                   placeholder="Candidate name">
                                                        </div>
                                                        <button type="button" @click="position.candidates.splice(cidx,1)"
                                                                class="p-3 rounded-xl text-red-600 hover:bg-red-50 focus:outline-none transition-all" aria-label="Remove candidate">
                                                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" aria-hidden>
                                                                <path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m3 0v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6h14zM10 11v6M14 11v6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </template>

                                                <div class="flex justify-end">
                                                    <button type="button" @click="position.candidates.push('')"
                                                            class="px-5 py-2.5 bg-indigo-50 text-indigo-700 border-2 border-indigo-100 rounded-xl hover:bg-indigo-100 font-medium transition-all">
                                                        + Add Candidate
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="mt-6 pt-6 border-t border-gray-200 flex justify-end">
                                                <button type="button" @click="positions.splice(index,1)"
                                                        class="px-5 py-2.5 bg-red-50 text-red-600 rounded-xl border-2 border-red-100 hover:bg-red-100 focus:outline-none font-medium transition-all" aria-label="Remove position">
                                                    Remove Position
                                                </button>
                                            </div>
                                        </div>
                                    </template>

                                    <div class="flex justify-center py-4">
                                        <button type="button" @click="positions.push({ name: '', candidates: [''] })"
                                                class="px-8 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white border-2 border-transparent rounded-xl hover:shadow-xl hover:shadow-indigo-500/30 font-semibold transition-all">
                                            <svg class="w-5 h-5 inline-block mr-2" viewBox="0 0 24 24" fill="none" aria-hidden>
                                                <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                            Add New Position
                                        </button>
                                    </div>

                                    <div class="flex justify-between pt-10 border-t border-gray-200">
                                        <button type="button" @click="activeTab = 'basic'"
                                                class="px-8 py-4 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 font-semibold transition-all">
                                            ← Previous
                                        </button>
                                        <button type="button" @click="activeTab = 'settings'"
                                                class="px-8 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:shadow-xl hover:shadow-indigo-500/30 font-semibold transition-all">
                                            Continue →
                                        </button>
                                    </div>
                                </div>
                            </section>

                            <!-- Panel 3: Voting Settings -->
                            <section x-show="activeTab === 'settings'"
                                     x-transition:enter="transition ease-out duration-300"
                                     x-transition:enter-start="opacity-0 transform translate-x-4"
                                     x-transition:enter-end="opacity-100 transform translate-x-0"
                                     x-data="{
                                                 enableGeo: false,
                                                 mapInitialized: false,
                                                 radiusValue: 50,
                                                 radiusUnit: 'meters'
                                             }"
                                     x-init="$watch('enableGeo', value => { if(value && !mapInitialized){ setTimeout(() => { initGeoMap(); mapInitialized = true }, 200) } })"
                                     aria-labelledby="settings-heading">
                                <div class="mb-10">
                                    <div class="flex items-center gap-3 mb-3">
                                        <div class="w-10 h-10 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-xl flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none">
                                                <path d="M12 22s-8-4.5-8-11.8A8 8 0 0 1 12 2a8 8 0 0 1 8 8.2c0 7.3-8 11.8-8 11.8z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <circle cx="12" cy="10" r="3" stroke="currentColor" stroke-width="2"/>
                                            </svg>
                                        </div>
                                        <h2 id="settings-heading" class="text-3xl font-bold text-gray-900">Voting Settings</h2>
                                    </div>
                                    <p class="text-gray-600 text-base">Configure security and access restrictions for your election</p>
                                </div>

                                <div class="space-y-8">

                                    <!-- Geographic Restriction Toggle Switch -->
                                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-2xl p-6">
                                        <label class="flex items-start justify-between cursor-pointer gap-4">
                                            <div class="flex-1">
                                                <div class="font-semibold text-gray-900 text-base mb-1">Restrict by geographic location</div>
                                                <div class="text-sm text-gray-600">Limit voting to voters within a configured geographic region</div>
                                            </div>

                                            <!-- Toggle Switch -->
                                            <div class="relative flex-shrink-0">
                                                <input type="checkbox" name="enable_geo_location" value="1" x-model="enableGeo" class="sr-only peer" id="geoToggle">
                                                <label for="geoToggle"
                                                       :class="enableGeo ? 'bg-gradient-to-r from-indigo-600 to-purple-600' : 'bg-gray-300'"
                                                       class="block w-14 h-8 rounded-full transition-all duration-300 cursor-pointer relative shadow-inner">
                                                    <div :class="enableGeo ? 'translate-x-7' : 'translate-x-1'"
                                                         class="absolute top-1 left-0 w-6 h-6 bg-white rounded-full shadow-lg transition-transform duration-300 flex items-center justify-center">
                                                        <svg x-show="enableGeo" class="w-3 h-3 text-indigo-600" viewBox="0 0 24 24" fill="none">
                                                            <path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                                                        </svg>
                                                    </div>
                                                </label>
                                            </div>
                                        </label>
                                    </div>

                                    <!-- Enhanced Geo Configuration -->
                                    <div x-show="enableGeo"
                                         x-transition:enter="transition ease-out duration-300"
                                         x-transition:enter-start="opacity-0 transform translate-y-4"
                                         x-transition:enter-end="opacity-100 transform translate-y-0"
                                         class="bg-white border-2 border-gray-200 rounded-2xl p-8 space-y-6 shadow-lg">
                                        <div class="flex items-center gap-3 mb-4">
                                            <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                                                <svg class="w-4 h-4 text-indigo-600" viewBox="0 0 24 24" fill="none">
                                                    <path d="M12 22s-8-4.5-8-11.8A8 8 0 0 1 12 2a8 8 0 0 1 8 8.2c0 7.3-8 11.8-8 11.8z" stroke="currentColor" stroke-width="2"/>
                                                    <circle cx="12" cy="10" r="3" stroke="currentColor" stroke-width="2"/>
                                                </svg>
                                            </div>
                                            <h3 class="text-xl font-bold text-gray-900">Geographic Configuration</h3>
                                        </div>

                                        <!-- Location Search -->
                                        <div class="space-y-4">
                                            <label class="block text-sm font-semibold text-gray-900">Search Location</label>
                                            <div class="flex gap-3">
                                                <input id="geoSearch"
                                                       :disabled="!enableGeo"
                                                       class="flex-1 rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent px-5 py-4 text-base disabled:bg-gray-100 disabled:cursor-not-allowed"
                                                       placeholder="Search address or place...">
                                                <button type="button" id="useMyLocation"
                                                        :disabled="!enableGeo"
                                                        class="px-5 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:shadow-lg hover:shadow-indigo-500/30 transition-all flex items-center gap-2 font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                                                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                                                        <circle cx="12" cy="12" r="3" fill="currentColor"/>
                                                    </svg>
                                                    My Location
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Radius Control -->
                                        <div class="space-y-4">
                                            <label class="block text-sm font-semibold text-gray-900">Allowed Radius</label>
                                            <div class="flex gap-3">
                                                <input type="number"
                                                       id="geoRadius"
                                                       name="geo_radius"
                                                       x-model="radiusValue"
                                                       min="1"
                                                       :disabled="!enableGeo"
                                                       class="flex-1 rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent px-5 py-4 text-base disabled:bg-gray-100 disabled:cursor-not-allowed" />
                                                <select x-model="radiusUnit"
                                                        :disabled="!enableGeo"
                                                        class="rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent px-5 py-4 text-base font-medium disabled:bg-gray-100 disabled:cursor-not-allowed">
                                                    <option value="meters">Meters</option>
                                                    <option value="kilometers">Kilometers</option>
                                                </select>
                                            </div>
                                            <p class="text-sm text-gray-500">Only voters within this radius can participate</p>
                                        </div>

                                        <!-- Map Preview -->
                                        <div class="space-y-4">
                                            <label class="block text-sm font-semibold text-gray-900">Map Preview</label>
                                            <div id="geoMap" class="w-full h-96 rounded-2xl border-2 border-gray-200 overflow-hidden shadow-lg"></div>
                                            <div class="flex justify-end">
                                                <select id="mapType"
                                                        :disabled="!enableGeo"
                                                        class="rounded-xl border-gray-300 shadow-sm px-4 py-2 text-sm disabled:bg-gray-100 disabled:cursor-not-allowed">
                                                    <option value="terrain">Terrain</option>
                                                    <option value="satellite">Satellite</option>
                                                    <option value="roadmap">Roadmap</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Hidden inputs for coordinates -->
                                        <input type="hidden" id="geoLatitude" name="geo_latitude">
                                        <input type="hidden" id="geoLongitude" name="geo_longitude">
                                    </div>

                                    <!-- Additional Settings -->
                                    <div class="bg-white border-2 border-gray-200 rounded-2xl p-8 space-y-6">
                                        <div class="flex items-center gap-3 mb-4">
                                            <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                                                <svg class="w-4 h-4 text-indigo-600" viewBox="0 0 24 24" fill="none">
                                                    <path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </div>
                                            <h3 class="text-xl font-bold text-gray-900">Access & Registration</h3>
                                        </div>

                                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-900 mb-3">Allowed email domain</label>
                                                <input type="text" name="allowed_email_domain" placeholder="example.com, org.edu"
                                                       class="block w-full rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent px-5 py-4 text-base" />
                                                <p class="text-xs text-gray-500 mt-2">Comma-separated list of allowed domains</p>
                                            </div>

                                            <div>
                                                <label class="block text-sm font-semibold text-gray-900 mb-3">Registration deadline</label>
                                                <input type="datetime-local" name="registration_deadline"
                                                       class="block w-full rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent px-5 py-4 text-base" />
                                                <p class="text-xs text-gray-500 mt-2">Last date to register for voting</p>
                                            </div>

                                            <div class="lg:col-span-2">
                                                <label class="block text-sm font-semibold text-gray-900 mb-3">Max votes per voter</label>
                                                <input type="number" name="max_votes_per_voter" min="1" value="1"
                                                       class="block w-full rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent px-5 py-4 text-base" />
                                                <p class="text-xs text-gray-500 mt-2">Maximum number of times a voter can vote in this election</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex justify-between pt-10 border-t border-gray-200">
                                        <button type="button" @click="activeTab = 'candidates'"
                                                class="px-8 py-4 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 font-semibold transition-all">
                                            ← Previous
                                        </button>
                                        <button type="submit"
                                                @click.prevent="
                                                    const form = $el.closest('form');
                                                    const formData = new FormData(form);

                                                    // Remove any existing positions fields
                                                    for (let key of Array.from(formData.keys())) {
                                                        if (key.startsWith('positions')) {
                                                            formData.delete(key);
                                                        }
                                                    }

                                                    // Add positions data properly
                                                    positions.forEach((position, idx) => {
                                                        formData.append(`positions[${idx}][name]`, position.name);
                                                        position.candidates.forEach((candidate, cidx) => {
                                                            if (candidate.trim()) {
                                                                formData.append(`positions[${idx}][candidates][${cidx}]`, candidate);
                                                            }
                                                        });
                                                    });

                                                    // Debug: Log what we're sending
                                                    console.log('Sending data:');
                                                    for (let [key, value] of formData.entries()) {
                                                        console.log(key, value);
                                                    }

                                                    fetch(form.action, {
                                                        method: 'POST',
                                                        body: formData,
                                                        headers: {
                                                            'X-Requested-With': 'XMLHttpRequest',
                                                            'Accept': 'application/json',
                                                            'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value
                                                        }
                                                    })
                                                    .then(res => {
                                                        console.log('Response status:', res.status);
                                                        console.log('Response headers:', [...res.headers.entries()]);

                                                        if (!res.ok) {
                                                            return res.text().then(text => {
                                                                console.error('Error response:', text);
                                                                throw new Error(`Server error: ${res.status} - ${text.substring(0, 200)}`);
                                                            });
                                                        }

                                                        return res.text().then(text => {
                                                            console.log('Raw response:', text);
                                                            try {
                                                                return JSON.parse(text);
                                                            } catch (e) {
                                                                console.error('Failed to parse JSON:', e);
                                                                throw new Error('Server returned invalid JSON: ' + text.substring(0, 200));
                                                            }
                                                        });
                                                    })
                                                    .then(data => {
                                                        console.log('Parsed data:', data);
                                                        if (data.success) {
                                                            electionCreated = true;
                                                            electionId = data.election.id;
                                                            electionCode = data.election.code;
                                                            registrationUrl = data.registration_url;
                                                            activeTab = 'share';
                                                            setTimeout(() => generateQRCode(registrationUrl), 100);
                                                        } else {
                                                            alert('Failed: ' + (data.message || 'Unknown error'));
                                                        }
                                                    })
                                                    .catch(err => {
                                                        console.error('Fetch error:', err);
                                                        alert('Error: ' + err.message);
                                                    })
                                                "
                                                class="px-10 py-4 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:shadow-xl hover:shadow-green-500/30 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 font-semibold transition-all">
                                            🎉 Create Election
                                        </button>


                                    </div>
                                </div>
                            </section>

                            <!-- Panel 4: Share Election -->
                            <section x-show="activeTab === 'share'"
                                     x-transition:enter="transition ease-out duration-300"
                                     x-transition:enter-start="opacity-0 transform translate-x-4"
                                     x-transition:enter-end="opacity-100 transform translate-x-0"
                                     aria-labelledby="share-heading">
                                <div class="mb-10">
                                    <div class="flex items-center gap-3 mb-3">
                                        <div class="w-10 h-10 bg-gradient-to-br from-green-600 to-emerald-600 rounded-xl flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none">
                                                <path d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </div>
                                        <h2 id="share-heading" class="text-3xl font-bold text-gray-900">Share Election</h2>
                                    </div>
                                    <p class="text-gray-600 text-base">Election created successfully! Share with voters using these methods</p>
                                </div>

                                <div class="space-y-8">
                                    <!-- Success Banner -->
                                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-2xl p-6">
                                        <div class="flex items-center gap-4">
                                            <div class="flex-shrink-0">
                                                <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center">
                                                    <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="none">
                                                        <path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="flex-1">
                                                <h3 class="text-lg font-bold text-green-900">Election Created Successfully!</h3>
                                                <p class="text-sm text-green-700 mt-1">Your election is now ready. Share it with voters using the options below.</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 6-Digit Code Card -->
                                    <div class="bg-white border-2 border-gray-200 rounded-2xl p-8 shadow-lg">
                                        <div class="flex items-center gap-3 mb-6">
                                            <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                                                <svg class="w-4 h-4 text-purple-600" viewBox="0 0 24 24" fill="none">
                                                    <path d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </div>
                                            <h3 class="text-xl font-bold text-gray-900">6-Digit Access Code</h3>
                                        </div>
                                        <p class="text-sm text-gray-600 mb-6">Voters can enter this code from the welcome page to access the registration form</p>

                                        <div class="bg-gradient-to-br from-purple-50 to-indigo-50 border-2 border-purple-200 rounded-xl p-8 text-center">
                                            <div class="text-sm font-medium text-gray-600 mb-3">Election Code</div>
                                            <div class="text-5xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-indigo-600 tracking-widest mb-4" x-text="electionCode || '------'"></div>
                                            <button type="button"
                                                    @click="copyToClipboard(electionCode)"
                                                    class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-xl hover:shadow-lg hover:shadow-purple-500/30 font-medium transition-all">
                                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                                                    <path d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                                Copy Code
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Registration Link Card -->
                                    <div class="bg-white border-2 border-gray-200 rounded-2xl p-8 shadow-lg">
                                        <div class="flex items-center gap-3 mb-6">
                                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                                <svg class="w-4 h-4 text-blue-600" viewBox="0 0 24 24" fill="none">
                                                    <path d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </div>
                                            <h3 class="text-xl font-bold text-gray-900">Registration Link</h3>
                                        </div>
                                        <p class="text-sm text-gray-600 mb-6">Share this direct link for voters to register and cast their vote</p>

                                        <div class="flex gap-3">
                                            <input type="text"
                                                   :value="registrationUrl"
                                                   readonly
                                                   class="flex-1 rounded-xl border-gray-300 bg-gray-50 px-5 py-4 text-sm font-mono text-gray-700">
                                            <button type="button"
                                                    @click="copyToClipboard(registrationUrl)"
                                                    class="px-6 py-4 bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-xl hover:shadow-lg hover:shadow-blue-500/30 font-medium transition-all flex items-center gap-2">
                                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                                                    <path d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                                Copy Link
                                            </button>
                                        </div>
                                    </div>

                                    <!-- QR Code Card -->
                                    <div class="bg-white border-2 border-gray-200 rounded-2xl p-8 shadow-lg">
                                        <div class="flex items-center gap-3 mb-6">
                                            <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                                                <svg class="w-4 h-4 text-indigo-600" viewBox="0 0 24 24" fill="none">
                                                    <path d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </div>
                                            <h3 class="text-xl font-bold text-gray-900">QR Code</h3>
                                        </div>
                                        <p class="text-sm text-gray-600 mb-6">Voters can scan this QR code to instantly access the registration form</p>

                                        <div class="bg-gradient-to-br from-gray-50 to-white border-2 border-gray-200 rounded-xl p-8">
                                            <div class="flex flex-col items-center gap-6">
                                                <div id="qrCodeDisplay" class="bg-white p-4 rounded-xl shadow-lg"></div>
                                                <button type="button"
                                                        @click="
                                                            const canvas = document.querySelector('#qrCodeDisplay canvas');
                                                            const link = document.createElement('a');
                                                            link.download = 'election-qr-code.png';
                                                            link.href = canvas.toDataURL();
                                                            link.click();
                                                        "
                                                        class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:shadow-lg hover:shadow-indigo-500/30 font-medium transition-all">
                                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                                                        <path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                    Download QR Code
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="flex justify-between pt-10 border-t border-gray-200">
                                        <a href="{{ route('admin.elections.index') }}"
                                           class="px-8 py-4 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 font-semibold transition-all">
                                            ← Back to Elections
                                        </a>
                                        <button type="button"
                                                @click="window.print()"
                                                class="px-8 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:shadow-xl hover:shadow-indigo-500/30 font-semibold transition-all flex items-center gap-2">
                                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                                                <path d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                            Print Details
                                        </button>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <!-- Google Maps & helper scripts -->
    @php
        $gmKey = config('services.google_maps.key') ?? env('GOOGLE_MAPS_API_KEY');
    @endphp

    @if(!$gmKey)
        <script>console.warn('Google Maps API key not set');</script>
    @endif

    <script src="https://maps.googleapis.com/maps/api/js?key={{ $gmKey }}&libraries=places&callback=initMap" async defer></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>

    <script>
        let map, marker, circle, autocomplete;

        function initMap() {
            const defaultCenter = { lat: 14.5995, lng: 120.9842 };
            const mapOptions = { zoom: 13, center: defaultCenter, mapTypeId: 'terrain' };
            map = new google.maps.Map(document.getElementById('geoMap'), mapOptions);

            const input = document.getElementById('geoSearch');
            autocomplete = new google.maps.places.Autocomplete(input);
            autocomplete.bindTo('bounds', map);

            autocomplete.addListener('place_changed', () => {
                const place = autocomplete.getPlace();
                if (!place.geometry?.location) {
                    alert("No details available for input: '" + place.name + "'");
                    return;
                }
                if (place.geometry.viewport) {
                    map.fitBounds(place.geometry.viewport);
                } else {
                    map.setCenter(place.geometry.location);
                    map.setZoom(15);
                }
                placeMarkerAndCircle(place.geometry.location);
            });

            document.getElementById('useMyLocation')?.addEventListener('click', () => {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            const pos = { lat: position.coords.latitude, lng: position.coords.longitude };
                            map.setCenter(pos);
                            map.setZoom(15);
                            placeMarkerAndCircle(pos);
                        },
                        () => alert('Error: The Geolocation service failed.')
                    );
                } else {
                    alert('Error: Your browser doesn\'t support geolocation.');
                }
            });

            document.getElementById('mapType')?.addEventListener('change', (e) => {
                map.setMapTypeId(e.target.value);
            });

            document.getElementById('geoRadius')?.addEventListener('input', updateCircle);
        }

        function initGeoMap() {
            if (typeof google !== 'undefined' && google.maps) {
                initMap();
            } else {
                setTimeout(initGeoMap, 500);
            }
        }

        function placeMarkerAndCircle(location) {
            if (marker) marker.setMap(null);
            if (circle) circle.setMap(null);

            marker = new google.maps.Marker({
                position: location,
                map: map,
                draggable: true,
                animation: google.maps.Animation.DROP,
                icon: {
                    path: google.maps.SymbolPath.CIRCLE,
                    scale: 10,
                    fillColor: '#4F46E5',
                    fillOpacity: 1,
                    strokeColor: '#ffffff',
                    strokeWeight: 3
                }
            });

            document.getElementById('geoLatitude').value = location.lat;
            document.getElementById('geoLongitude').value = location.lng;

            updateCircle();

            marker.addListener('dragend', () => {
                const pos = marker.getPosition();
                document.getElementById('geoLatitude').value = pos.lat();
                document.getElementById('geoLongitude').value = pos.lng();
                updateCircle();
            });
        }

        function updateCircle() {
            if (!marker) return;
            if (circle) circle.setMap(null);

            const radiusValue = parseFloat(document.getElementById('geoRadius')?.value) || 50;
            const radiusUnit = document.querySelector('[x-model="radiusUnit"]')?.value || 'meters';
            const radiusInMeters = radiusUnit === 'kilometers' ? radiusValue * 1000 : radiusValue;

            circle = new google.maps.Circle({
                map: map,
                center: marker.getPosition(),
                radius: radiusInMeters,
                fillColor: '#4F46E5',
                fillOpacity: 0.2,
                strokeColor: '#4F46E5',
                strokeOpacity: 0.8,
                strokeWeight: 2
            });

            map.fitBounds(circle.getBounds());
        }

        window.initMap = initMap;
</script>
