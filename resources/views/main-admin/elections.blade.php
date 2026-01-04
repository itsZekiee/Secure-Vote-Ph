@extends('layouts.app-main-admin')

@section('content')
    <div x-data="{
        collapsed: false,
        isMobile: window.innerWidth < 1024,
        showErrorModal: false,
        errorMessage: '',
        errorDetails: []
    }"
         x-init="window.addEventListener('resize', () => { isMobile = window.innerWidth < 1024 })"
         class="flex min-h-screen bg-gradient-to-br from-gray-50 via-blue-50/30 to-gray-50">

        <x-admin-sidebar />

        <!-- Error Modal -->
        <div x-show="showErrorModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 overflow-y-auto"
             style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <!-- Backdrop -->
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="showErrorModal = false"></div>

                <!-- Modal Panel -->
                <div x-show="showErrorModal"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative inline-block w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl">

                    <!-- Error Icon -->
                    <div class="flex items-center justify-center w-16 h-16 mx-auto bg-red-100 rounded-full">
                        <svg class="w-10 h-10 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>

                    <!-- Title -->
                    <div class="mt-4 text-center">
                        <h3 class="text-xl font-bold text-gray-900">Election Creation Failed</h3>
                        <p class="mt-2 text-sm text-gray-600">The election could not be created. Please review the errors below and try again.</p>
                    </div>

                    <!-- Error Message -->
                    <div class="mt-6 bg-red-50 border border-red-200 rounded-xl p-4">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-red-800" x-text="errorMessage"></p>
                                <!-- Error Details List -->
                                <ul x-show="errorDetails.length > 0" class="mt-3 space-y-1">
                                    <template x-for="(detail, index) in errorDetails" :key="index">
                                        <li class="text-sm text-red-700 flex items-start gap-2">
                                            <span class="text-red-400">•</span>
                                            <span x-text="detail"></span>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-6 flex gap-3">
                        <button type="button"
                                @click="showErrorModal = false"
                                class="flex-1 px-4 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 font-semibold transition-all">
                            Close
                        </button>
                        <button type="button"
                                @click="showErrorModal = false"
                                class="flex-1 px-4 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:shadow-lg hover:shadow-indigo-500/30 font-semibold transition-all">
                            Try Again
                        </button>
                    </div>
                </div>
            </div>
        </div>

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
                                        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
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
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none">
                                            <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v14a2 2 0 01-2 2z" stroke="currentColor" stroke-width="2"/>
                                        </svg>
                                        Elections
                                    </a>
                                </li>
                                <li>
                                    <svg class="w-4 h-4 text-gray-300" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M9 5l7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </li>
                                <li class="flex items-center">
                                    <div class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl text-sm font-semibold shadow-lg shadow-indigo-500/30">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none">
                                            <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        Create New
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
                          isSubmitting: false,
                          positions: [{ name: '', candidates: [''] }],
                          selectedOrganization: '',
                          formData: {
                              title: '',
                              voting_start: '',
                              voting_end: '',
                              accepted_domains: '',
                              registration_deadline: '',
                              max_votes: 1
                          },
                          validateBasicInfo() {
                              if (!this.formData.title.trim()) {
                                  alert('Election Title is required');
                                  return false;
                              }
                              if (!this.selectedOrganization) {
                                  alert('Please select an organization');
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
                          },
                          showError(message, details = []) {
                              $root.errorMessage = message;
                              $root.errorDetails = details;
                              $root.showErrorModal = true;
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
                                        <div :class="activeTab === 'basic' ? 'bg-gradient-to-br from-indigo-600 to-purple-600 text-white shadow-lg shadow-indigo-500/30' : (activeTab === 'candidates' || activeTab === 'settings' || activeTab === 'share' ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-600')"
                                             class="w-12 h-12 rounded-xl flex items-center justify-center font-bold text-lg transition-all duration-300">
                                            <span x-show="!(activeTab === 'candidates' || activeTab === 'settings' || activeTab === 'share')">1</span>
                                            <svg x-show="activeTab === 'candidates' || activeTab === 'settings' || activeTab === 'share'" class="w-6 h-6" viewBox="0 0 24 24" fill="none">
                                                <path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p :class="activeTab === 'basic' ? 'text-indigo-600 font-bold' : 'text-gray-900 font-semibold'" class="text-sm transition-colors">Basic Info</p>
                                            <p class="text-xs text-gray-500">Election details</p>
                                        </div>
                                    </div>

                                    <div class="flex-1 h-1 bg-gray-200 rounded-full mx-4 relative overflow-hidden">
                                        <div :class="activeTab === 'candidates' || activeTab === 'settings' || activeTab === 'share' ? 'w-full' : 'w-0'"
                                             class="absolute inset-0 bg-gradient-to-r from-indigo-600 to-purple-600 transition-all duration-500"></div>
                                    </div>

                                    <!-- Step 2 -->
                                    <div class="flex items-center gap-4 flex-1">
                                        <div :class="activeTab === 'candidates' ? 'bg-gradient-to-br from-indigo-600 to-purple-600 text-white shadow-lg shadow-indigo-500/30' : (activeTab === 'settings' || activeTab === 'share' ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-600')"
                                             class="w-12 h-12 rounded-xl flex items-center justify-center font-bold text-lg transition-all duration-300">
                                            <span x-show="!(activeTab === 'settings' || activeTab === 'share')">2</span>
                                            <svg x-show="activeTab === 'settings' || activeTab === 'share'" class="w-6 h-6" viewBox="0 0 24 24" fill="none">
                                                <path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p :class="activeTab === 'candidates' ? 'text-indigo-600 font-bold' : 'text-gray-900 font-semibold'" class="text-sm transition-colors">Positions</p>
                                            <p class="text-xs text-gray-500">Add candidates</p>
                                        </div>
                                    </div>

                                    <div class="flex-1 h-1 bg-gray-200 rounded-full mx-4 relative overflow-hidden">
                                        <div :class="activeTab === 'settings' || activeTab === 'share' ? 'w-full' : 'w-0'"
                                             class="absolute inset-0 bg-gradient-to-r from-indigo-600 to-purple-600 transition-all duration-500"></div>
                                    </div>

                                    <!-- Step 3 -->
                                    <div class="flex items-center gap-4 flex-1">
                                        <div :class="activeTab === 'settings' ? 'bg-gradient-to-br from-indigo-600 to-purple-600 text-white shadow-lg shadow-indigo-500/30' : (activeTab === 'share' ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-600')"
                                             class="w-12 h-12 rounded-xl flex items-center justify-center font-bold text-lg transition-all duration-300">
                                            <span x-show="activeTab !== 'share'">3</span>
                                            <svg x-show="activeTab === 'share'" class="w-6 h-6" viewBox="0 0 24 24" fill="none">
                                                <path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p :class="activeTab === 'settings' ? 'text-indigo-600 font-bold' : 'text-gray-900 font-semibold'" class="text-sm transition-colors">Settings</p>
                                            <p class="text-xs text-gray-500">Configure options</p>
                                        </div>
                                    </div>

                                    <div class="flex-1 h-1 bg-gray-200 rounded-full mx-4 relative overflow-hidden">
                                        <div :class="activeTab === 'share' ? 'w-full' : 'w-0'"
                                             class="absolute inset-0 bg-gradient-to-r from-indigo-600 to-purple-600 transition-all duration-500"></div>
                                    </div>

                                    <!-- Step 4 -->
                                    <div class="flex items-center gap-4 flex-1">
                                        <div :class="activeTab === 'share' ? 'bg-gradient-to-br from-green-600 to-emerald-600 text-white shadow-lg shadow-green-500/30' : 'bg-gray-200 text-gray-600'"
                                             class="w-12 h-12 rounded-xl flex items-center justify-center font-bold text-lg transition-all duration-300">
                                            4
                                        </div>
                                        <div>
                                            <p :class="activeTab === 'share' ? 'text-green-600 font-bold' : 'text-gray-900 font-semibold'" class="text-sm transition-colors">Share</p>
                                            <p class="text-xs text-gray-500">Distribute link</p>
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
                                                <path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </div>
                                        <h2 id="basic-heading" class="text-3xl font-bold text-gray-900">Basic Information</h2>
                                    </div>
                                    <p class="text-gray-600 text-base">Provide fundamental details about your election</p>
                                </div>

                                <div class="space-y-8">
                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                        <div class="lg:col-span-2">
                                            <label for="title" class="block text-sm font-semibold text-gray-900 mb-3">Election Title <span class="text-red-500">*</span></label>
                                            <input type="text" id="title" name="title" x-model="formData.title" required
                                                   class="block w-full rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent px-5 py-4 text-base transition-all"
                                                   placeholder="e.g., Student Council Election 2025">
                                        </div>

                                        <div class="lg:col-span-2">
                                            <label for="description" class="block text-sm font-semibold text-gray-900 mb-3">Description</label>
                                            <textarea id="description" name="description" rows="4"
                                                      class="block w-full rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent px-5 py-4 text-base transition-all resize-none"
                                                      placeholder="Provide a brief description of this election..."></textarea>
                                        </div>

                                        <div class="lg:col-span-2">
                                            <label for="organization" class="block text-sm font-semibold text-gray-900 mb-3">Organization <span class="text-red-500">*</span></label>
                                            <select id="organization" name="organization_id" x-model="selectedOrganization" required
                                                    class="block w-full rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent px-5 py-4 text-base transition-all">
                                                <option value="">Select an organization</option>
                                                @foreach($organizations as $organization)
                                                    <option value="{{ $organization->id }}">{{ $organization->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div>
                                            <label for="voting_start" class="block text-sm font-semibold text-gray-900 mb-3">Voting Start <span class="text-red-500">*</span></label>
                                            <input type="datetime-local" id="voting_start" name="voting_start" x-model="formData.voting_start" required
                                                   class="block w-full rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent px-5 py-4 text-base transition-all">
                                        </div>

                                        <div>
                                            <label for="voting_end" class="block text-sm font-semibold text-gray-900 mb-3">Voting End <span class="text-red-500">*</span></label>
                                            <input type="datetime-local" id="voting_end" name="voting_end" x-model="formData.voting_end" required
                                                   class="block w-full rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent px-5 py-4 text-base transition-all">
                                        </div>
                                    </div>

                                    <div class="flex justify-end pt-10 border-t border-gray-200">
                                        <button type="button"
                                                @click="if(validateBasicInfo()) activeTab = 'candidates'"
                                                class="px-8 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:shadow-xl hover:shadow-indigo-500/30 font-semibold transition-all">
                                            Continue →
                                        </button>
                                    </div>
                                </div>
                            </section>

                            <!-- Panel 2: Positions & Candidates -->
                            <section x-show="activeTab === 'candidates'"
                                     x-transition:enter="transition ease-out duration-300"
                                     x-transition:enter-start="opacity-0 transform translate-x-4"
                                     x-transition:enter-end="opacity-100 transform translate-x-0"
                                     x-data="{
                                     automationMode: false,
                                     selectedPartylist: '',
                                     partylists: [],
                                     isFetching: false,
                                     isLoadingPartylists: false,
                                     isImported: false,
                                     importedFrom: '',
                                     get parentData() {
                                         return Alpine.$data(this.$el.closest('form'));
                                     },
                                     get selectedOrg() {
                                         return this.parentData.selectedOrganization;
                                     },
                                     async fetchPartylists() {
                                         const orgId = this.selectedOrg;
                                         if (!orgId) {
                                             this.partylists = [];
                                             return;
                                         }
                                         this.isLoadingPartylists = true;
                                         try {
                                             const response = await fetch(`/admin/organizations/${orgId}/partylists`);
                                             const data = await response.json();
                                             if (data.success) {
                                                 this.partylists = data.partylists;
                                             }
                                         } catch (error) {
                                             console.error('Failed to fetch partylists:', error);
                                         } finally {
                                             this.isLoadingPartylists = false;
                                         }
                                     },
                                     async fetchCandidates() {
                                         if (!this.selectedPartylist) {
                                             alert('Please select a partylist first');
                                             return;
                                         }
                                         this.isFetching = true;
                                         try {
                                             const response = await fetch(`/admin/partylists/${this.selectedPartylist}/candidates`);
                                             const data = await response.json();
                                             if (data.success && data.positions.length > 0) {
                                                 this.parentData.positions = data.positions;
                                                 this.isImported = true;
                                                 this.importedFrom = data.partylist_name;
                                             } else {
                                                 alert('No candidates found for this partylist');
                                             }
                                         } catch (error) {
                                             console.error('Failed to fetch candidates:', error);
                                             alert('Failed to fetch candidates');
                                         } finally {
                                             this.isFetching = false;
                                         }
                                     },
                                     clearImport() {
                                         this.parentData.positions = [{ name: '', candidates: [''] }];
                                         this.isImported = false;
                                         this.importedFrom = '';
                                         this.selectedPartylist = '';
                                     }
                                 }"
                                     x-init="$watch('automationMode', value => { if(value) fetchPartylists(); })"
                                     aria-labelledby="positions-heading">

                                <div class="mb-10">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3 mb-3">
                                            <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-amber-500 rounded-xl flex items-center justify-center shadow-lg shadow-orange-500/30">
                                                <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="none">
                                                    <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
                                                    <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <h2 id="positions-heading" class="text-2xl font-bold text-gray-900">Positions & Candidates</h2>
                                                <p class="text-gray-600">Define the positions available for voting and their candidates</p>
                                            </div>
                                        </div>

                                        <!-- Automation Mode Toggle -->
                                        <div class="flex items-center gap-4 bg-gradient-to-r from-amber-50 to-orange-50 border-2 border-amber-200 rounded-2xl px-6 py-4">
                                            <div class="flex items-center gap-2">
                                                <svg class="w-5 h-5 text-amber-600" viewBox="0 0 24 24" fill="none">
                                                    <path d="M13 10V3L4 14h7v7l9-11h-7z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                                <span class="font-semibold text-gray-700">Automation Mode</span>
                                            </div>
                                            <button type="button"
                                                    @click="automationMode = !automationMode"
                                                    :class="automationMode ? 'bg-amber-500' : 'bg-gray-300'"
                                                    class="relative inline-flex h-7 w-14 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2">
                                                    <span :class="automationMode ? 'translate-x-8' : 'translate-x-1'"
                                                          class="inline-block h-5 w-5 transform rounded-full bg-white shadow-lg transition-transform"></span>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-6">
                                    <!-- Automation Panel -->
                                    <div x-show="automationMode"
                                         x-transition:enter="transition ease-out duration-300"
                                         x-transition:enter-start="opacity-0 transform -translate-y-4"
                                         x-transition:enter-end="opacity-100 transform translate-y-0"
                                         class="bg-gradient-to-br from-amber-50 via-orange-50 to-yellow-50 border-2 border-amber-200 rounded-2xl p-8 shadow-lg">

                                        <div class="flex items-center gap-3 mb-6">
                                            <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-orange-500 rounded-xl flex items-center justify-center">
                                                <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none">
                                                    <path d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M10.172 13.828a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-bold text-gray-900">Import from Partylist</h3>
                                                <p class="text-sm text-gray-600">Automatically populate positions and candidates from an existing partylist</p>
                                            </div>
                                        </div>

                                        <!-- No Organization Selected Warning -->
                                        <template x-if="!selectedOrg">
                                            <div class="bg-yellow-100 border-2 border-yellow-300 rounded-xl p-4 mb-6">
                                                <div class="flex items-center gap-3">
                                                    <svg class="w-6 h-6 text-yellow-600" viewBox="0 0 24 24" fill="none">
                                                        <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                    <div>
                                                        <p class="font-semibold text-yellow-800">No Organization Selected</p>
                                                        <p class="text-sm text-yellow-700">Please go back to Step 1 and select an organization first.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>

                                        <!-- Partylist Selection -->
                                        <template x-if="selectedOrg">
                                            <div class="space-y-4">
                                                <label class="block text-sm font-semibold text-gray-900">Select Partylist</label>
                                                <div class="flex items-center gap-4">
                                                    <div class="flex-1">
                                                        <select x-model="selectedPartylist"
                                                                class="block w-full rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-amber-500 focus:border-transparent px-5 py-4 text-base transition-all">
                                                            <option value="">-- Select a partylist --</option>
                                                            <template x-for="partylist in partylists" :key="partylist.id">
                                                                <option :value="partylist.id" x-text="partylist.name"></option>
                                                            </template>
                                                        </select>
                                                        <p x-show="isLoadingPartylists" class="text-sm text-amber-600 mt-2">
                                                            <svg class="animate-spin inline w-4 h-4 mr-1" viewBox="0 0 24 24" fill="none">
                                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                            </svg>
                                                            Loading partylists...
                                                        </p>
                                                        <p x-show="!isLoadingPartylists && partylists.length === 0 && selectedOrg" class="text-sm text-gray-500 mt-2">
                                                            No partylists found for this organization.
                                                        </p>
                                                    </div>
                                                    <button type="button"
                                                            @click="fetchCandidates()"
                                                            :disabled="!selectedPartylist || isFetching"
                                                            class="px-8 py-4 bg-gradient-to-r from-amber-500 to-orange-500 text-white rounded-xl hover:shadow-lg hover:shadow-amber-500/30 font-semibold transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2 whitespace-nowrap">
                                                        <svg x-show="isFetching" class="animate-spin w-5 h-5" viewBox="0 0 24 24" fill="none">
                                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                        </svg>
                                                        <svg x-show="!isFetching" class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                                                            <path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                        </svg>
                                                        <span x-text="isFetching ? 'Fetching...' : 'Fetch Candidates'"></span>
                                                    </button>
                                                </div>
                                            </div>
                                        </template>

                                        <!-- Import Success Banner -->
                                        <div x-show="isImported"
                                             x-transition
                                             class="mt-6 bg-green-100 border-2 border-green-300 rounded-xl p-4">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center gap-3">
                                                    <svg class="w-6 h-6 text-green-600" viewBox="0 0 24 24" fill="none">
                                                        <path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                    <div>
                                                        <p class="font-semibold text-green-800">Successfully Imported</p>
                                                        <p class="text-sm text-green-700">Candidates imported from <span class="font-bold" x-text="importedFrom"></span></p>
                                                    </div>
                                                </div>
                                                <button type="button" @click="clearImport()" class="px-4 py-2 bg-white text-green-700 rounded-lg border border-green-300 hover:bg-green-50 text-sm font-medium transition-all">
                                                    Clear & Reset
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Imported Data Display (Read-Only Cards) -->
                                    <template x-if="isImported && automationMode">
                                        <div class="space-y-6">
                                            <template x-for="(position, pIndex) in parentData.positions" :key="pIndex">
                                                <div class="bg-white border-2 border-indigo-200 rounded-2xl p-6 shadow-lg relative">
                                                    <div class="absolute -top-3 -right-3 bg-gradient-to-r from-indigo-500 to-purple-500 text-white px-3 py-1 rounded-full text-xs font-bold flex items-center gap-1">
                                                        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none">
                                                            <path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                        </svg>
                                                        Imported
                                                    </div>
                                                    <div class="flex items-center gap-3 mb-4">
                                                        <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-xl flex items-center justify-center text-white font-bold" x-text="pIndex + 1"></div>
                                                        <h4 class="text-lg font-bold text-gray-900" x-text="position.name"></h4>
                                                    </div>
                                                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                                        <template x-for="(candidate, cIndex) in position.candidates" :key="cIndex">
                                                            <div class="bg-gray-50 border border-gray-200 rounded-lg px-4 py-2 text-sm text-gray-700">
                                                                <span x-text="candidate"></span>
                                                            </div>
                                                        </template>
                                                    </div>
                                                    <input type="hidden" :name="'positions[' + pIndex + '][name]'" :value="position.name">
                                                    <template x-for="(candidate, cIndex) in position.candidates" :key="'input-' + cIndex">
                                                        <input type="hidden" :name="'positions[' + pIndex + '][candidates][]'" :value="candidate">
                                                    </template>
                                                </div>
                                            </template>
                                        </div>
                                    </template>

                                    <!-- Manual Entry Form -->
                                    <div x-show="!automationMode" class="space-y-6">
                                        <template x-for="(position, pIndex) in parentData.positions" :key="pIndex">
                                            <div class="bg-white border-2 border-gray-200 rounded-2xl p-8 shadow-lg hover:shadow-xl transition-shadow">
                                                <div class="flex items-center gap-4 mb-6">
                                                    <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-xl flex items-center justify-center text-white font-bold text-lg shadow-lg" x-text="pIndex + 1"></div>
                                                    <div class="flex-1">
                                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Position name</label>
                                                        <input type="text" x-model="position.name" :name="'positions[' + pIndex + '][name]'"
                                                               class="block w-full rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent px-5 py-4 text-base transition-all"
                                                               placeholder="e.g., President, Vice President, Secretary">
                                                    </div>
                                                    <button type="button" @click="parentData.positions.splice(pIndex, 1)" x-show="parentData.positions.length > 1"
                                                            class="p-3 bg-red-100 text-red-600 rounded-xl hover:bg-red-200 transition-all self-end">
                                                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                                                            <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                        </svg>
                                                    </button>
                                                </div>

                                                <div class="space-y-4 pl-16">
                                                    <label class="block text-sm font-semibold text-gray-700">Candidates</label>
                                                    <template x-for="(candidate, cIndex) in position.candidates" :key="cIndex">
                                                        <div class="flex items-center gap-3">
                                                            <input type="text" x-model="position.candidates[cIndex]" :name="'positions[' + pIndex + '][candidates][]'"
                                                                   class="flex-1 rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent px-5 py-3 text-base transition-all"
                                                                   placeholder="Candidate name">
                                                            <button type="button" @click="position.candidates.splice(cIndex, 1)" x-show="position.candidates.length > 1"
                                                                    class="p-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition-all">
                                                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none">
                                                                    <path d="M6 18L18 6M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </template>
                                                    <button type="button" @click="position.candidates.push('')"
                                                            class="flex items-center gap-2 text-indigo-600 hover:text-indigo-700 font-semibold text-sm transition-all">
                                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none">
                                                            <path d="M12 5v14m-7-7h14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                        </svg>
                                                        Add Candidate
                                                    </button>
                                                </div>
                                            </div>
                                        </template>

                                        <button type="button" @click="parentData.positions.push({ name: '', candidates: [''] })"
                                                class="mt-8 w-full py-4 border-2 border-dashed border-indigo-300 rounded-2xl text-indigo-600 hover:bg-indigo-50 hover:border-indigo-400 font-semibold transition-all flex items-center justify-center gap-2">
                                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                                                <path d="M12 5v14m-7-7h14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
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
                                                class="px-10 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:shadow-xl hover:shadow-indigo-500/30 font-bold transition-all">
                                            Continue to Settings →
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
                                                <path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
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
                                            <div class="flex items-start gap-4">
                                                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-500 rounded-xl flex items-center justify-center flex-shrink-0">
                                                    <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="none">
                                                        <path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                        <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <h3 class="text-lg font-bold text-gray-900">Geographic Restriction</h3>
                                                    <p class="text-sm text-gray-600 mt-1">Restrict voting to a specific geographic area. Voters must be within the designated radius to cast their vote.</p>
                                                </div>
                                            </div>
                                            <div class="relative flex-shrink-0">
                                                <input type="checkbox" x-model="enableGeo" name="enable_geo_location" class="sr-only peer" id="geoToggle">
                                                <div :class="enableGeo ? 'bg-gradient-to-r from-blue-500 to-indigo-500' : 'bg-gray-300'"
                                                     class="block w-14 h-8 rounded-full transition-all duration-300 cursor-pointer relative shadow-inner">
                                                    <div :class="enableGeo ? 'translate-x-7' : 'translate-x-1'"
                                                         class="absolute top-1 left-0 w-6 h-6 bg-white rounded-full shadow-lg transition-transform duration-300 flex items-center justify-center">
                                                        <svg x-show="enableGeo" class="w-3 h-3 text-blue-600" viewBox="0 0 24 24" fill="none">
                                                            <path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                                                        </svg>
                                                    </div>
                                                </div>
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
                                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-500 rounded-xl flex items-center justify-center">
                                                <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none">
                                                    <path d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l5.447 2.724A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-bold text-gray-900">Location Configuration</h3>
                                                <p class="text-sm text-gray-600">Set the voting location and radius</p>
                                            </div>
                                        </div>

                                        <!-- Location Search -->
                                        <div class="space-y-4">
                                            <label class="block text-sm font-semibold text-gray-900">Search Location</label>
                                            <div class="flex gap-3">
                                                <input type="text" id="locationSearch" placeholder="Search for a location..."
                                                       class="flex-1 rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent px-5 py-4 text-base transition-all">
                                                <button type="button" id="useMyLocation"
                                                        class="px-6 py-4 bg-gradient-to-r from-blue-500 to-indigo-500 text-white rounded-xl hover:shadow-lg font-semibold transition-all flex items-center gap-2">
                                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                                                        <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                    Use My Location
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Radius Control -->
                                        <div class="space-y-4">
                                            <label class="block text-sm font-semibold text-gray-900">Voting Radius</label>
                                            <div class="flex gap-4">
                                                <div class="flex-1">
                                                    <input type="number" id="geoRadius" name="geo_radius" x-model="radiusValue" min="10" max="10000"
                                                           class="block w-full rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent px-5 py-4 text-base transition-all"
                                                           placeholder="Enter radius">
                                                </div>
                                                <div class="w-40">
                                                    <select x-model="radiusUnit"
                                                            class="block w-full rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent px-5 py-4 text-base transition-all">
                                                        <option value="meters">Meters</option>
                                                        <option value="kilometers">Kilometers</option>
                                                    </select>
                                                </div>
                                                <button type="button" @click="updateRadius()"
                                                        class="px-6 py-4 bg-gradient-to-r from-blue-500 to-indigo-500 text-white rounded-xl hover:shadow-lg font-semibold transition-all">
                                                    Apply
                                                </button>
                                                <input type="hidden" id="computedRadius" name="geo_radius_meters">
                                            </div>
                                            <p class="text-sm text-gray-500 mt-2">
                                                <span x-text="radiusUnit === 'kilometers' ? (radiusValue * 1000) + ' meters' : radiusValue + ' meters'"></span> from the center point
                                            </p>
                                        </div>

                                        <!-- Interactive Map -->
                                        <div class="space-y-4">
                                            <label class="block text-sm font-semibold text-gray-900">Set Voting Zone</label>
                                            <div id="geoMap" class="w-full h-80 rounded-2xl border-2 border-gray-200 overflow-hidden shadow-inner"></div>
                                            <input type="hidden" id="geoLatitude" name="geo_latitude">
                                            <input type="hidden" id="geoLongitude" name="geo_longitude">
                                            <p class="text-sm text-gray-500">Click on the map to set the center of your voting zone</p>
                                        </div>

                                        <!-- Coordinate Display -->
                                        <div class="grid grid-cols-2 gap-4 p-4 bg-gray-50 rounded-xl">
                                            <div>
                                                <span class="text-sm font-medium text-gray-600">Latitude:</span>
                                                <span id="latDisplay" class="ml-2 text-sm text-gray-900 font-mono">Not set</span>
                                            </div>
                                            <div>
                                                <span class="text-sm font-medium text-gray-600">Longitude:</span>
                                                <span id="lngDisplay" class="ml-2 text-sm text-gray-900 font-mono">Not set</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Additional Configuration Fields -->
                                    <div class="bg-white border-2 border-gray-200 rounded-2xl p-8 space-y-8 shadow-sm">
                                        <div class="flex items-center gap-3 mb-2">
                                            <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-xl flex items-center justify-center">
                                                <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none">
                                                    <path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-bold text-gray-900">Access & Voting Control</h3>
                                                <p class="text-sm text-gray-600">Fine-tune who can vote and how</p>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                            <div class="lg:col-span-2">
                                                <label for="accepted_domains" class="block text-sm font-semibold text-gray-900 mb-3">
                                                    Accepted Domains (e.g., @iskolarngbayan.edu.ph, @zdeveloper.org)
                                                </label>
                                                <input type="text" id="accepted_domains" name="accepted_domains" x-model="formData.accepted_domains"
                                                       class="block w-full rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent px-5 py-4 text-base transition-all"
                                                       placeholder="Comma separated domains (including @), leave empty for all">
                                                <p class="mt-2 text-xs text-gray-500">Only users with these email domains will be allowed to register.</p>
                                            </div>

                                            <div>
                                                <label for="registration_deadline" class="block text-sm font-semibold text-gray-900 mb-3">Registration Deadline</label>
                                                <input type="datetime-local" id="registration_deadline" name="registration_deadline" x-model="formData.registration_deadline"
                                                       class="block w-full rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent px-5 py-4 text-base transition-all">
                                                <p class="mt-2 text-xs text-gray-500">Voters cannot register after this time.</p>
                                            </div>

                                            <div>
                                                <label for="max_votes" class="block text-sm font-semibold text-gray-900 mb-3">Max Number of Votes</label>
                                                <input type="number" id="max_votes" name="max_votes" x-model="formData.max_votes" min="1"
                                                       class="block w-full rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent px-5 py-4 text-base transition-all">
                                                <p class="mt-2 text-xs text-gray-500">Number of times a user can submit a vote.</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex justify-between pt-10 border-t border-gray-200">
                                        <button type="button" @click="activeTab = 'candidates'"
                                                class="px-8 py-4 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 font-semibold transition-all">
                                            ← Previous
                                        </button>
                                        <button type="submit"
                                                :disabled="isSubmitting"
                                                class="px-10 py-4 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:shadow-xl hover:shadow-green-500/30 font-bold transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-3">
                                            <svg x-show="isSubmitting" class="animate-spin w-5 h-5" viewBox="0 0 24 24" fill="none">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            <span x-text="isSubmitting ? 'Creating Election...' : 'Create Election'"></span>
                                        </button>
                                    </div>
                                </div>
                            </section>

                            <!-- Panel 4: Share & QR Code (shown after successful creation) -->
                            <section x-show="activeTab === 'share'"
                                     x-transition:enter="transition ease-out duration-300"
                                     x-transition:enter-start="opacity-0 transform translate-x-4"
                                     x-transition:enter-end="opacity-100 transform translate-x-0"
                                     aria-labelledby="share-heading">
                                <div class="text-center mb-10">
                                    <div class="w-20 h-20 bg-gradient-to-br from-green-500 to-emerald-500 rounded-full flex items-center justify-center mx-auto mb-6 shadow-2xl shadow-green-500/30">
                                        <svg class="w-10 h-10 text-white" viewBox="0 0 24 24" fill="none">
                                            <path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                    <h2 id="share-heading" class="text-3xl font-bold text-gray-900 mb-3">Election Created Successfully!</h2>
                                    <p class="text-gray-600 text-lg">Share the registration link with your voters</p>
                                </div>

                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                    <!-- QR Code Section -->
                                    <div class="bg-gradient-to-br from-indigo-50 to-purple-50 border-2 border-indigo-200 rounded-2xl p-8 text-center">
                                        <h3 class="text-xl font-bold text-gray-900 mb-6">Scan QR Code</h3>
                                        <div id="qrCodeDisplay" class="flex justify-center mb-6"></div>
                                        <p class="text-sm text-gray-600">Voters can scan this code to access the registration page</p>
                                    </div>

                                    <!-- Link Section -->
                                    <div class="space-y-6">
                                        <!-- Election Access Code -->
                                        <div class="bg-white border-2 border-gray-200 rounded-2xl p-6">
                                            <h4 class="text-lg font-bold text-gray-900 mb-4">Election Access Code</h4>
                                            <div class="flex items-center gap-3">
                                                <input type="text"
                                                       :value="electionCode"
                                                       readonly
                                                       class="flex-1 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-lg font-mono text-gray-900">
                                                <button type="button"
                                                        @click="copyToClipboard(electionCode)"
                                                        class="p-3 bg-indigo-100 text-indigo-600 rounded-xl hover:bg-indigo-200 transition-all">
                                                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none">
                                                        <path d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="bg-white border-2 border-gray-200 rounded-2xl p-6">
                                            <h3 class="text-lg font-bold text-gray-900 mb-4">Registration Link</h3>
                                            <div class="flex items-center gap-3">
                                                <input type="text" readonly :value="registrationUrl"
                                                       class="flex-1 px-5 py-4 bg-gray-100 rounded-xl text-sm text-gray-700 font-mono">
                                                <button type="button" @click="copyToClipboard(registrationUrl)"
                                                        class="px-4 py-4 bg-indigo-100 text-indigo-700 rounded-xl hover:bg-indigo-200 transition-all">
                                                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none">
                                                        <path d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>

                                        <a :href="'/admin/elections/' + electionId"
                                           class="block w-full px-8 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:shadow-xl hover:shadow-indigo-500/30 font-semibold transition-all text-center">
                                            View Election Dashboard →
                                        </a>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <!-- Google Maps API -->
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places"></script>
    <!-- QRCode.js -->
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>

    <script>
        let map, marker, circle;

        function initGeoMap() {
            const defaultPos = { lat: 14.5995, lng: 120.9842 };
            map = new google.maps.Map(document.getElementById('geoMap'), {
                zoom: 13,
                center: defaultPos,
                styles: [
                    {
                        "featureType": "administrative",
                        "elementType": "geometry",
                        "stylers": [{ "visibility": "off" }]
                    },
                    {
                        "featureType": "poi",
                        "stylers": [{ "visibility": "off" }]
                    },
                    {
                        "featureType": "road",
                        "elementType": "labels.icon",
                        "stylers": [{ "visibility": "off" }]
                    },
                    {
                        "featureType": "transit",
                        "stylers": [{ "visibility": "off" }]
                    }
                ]
            });

            map.addListener('click', function(e) {
                setLocation(e.latLng.lat(), e.latLng.lng());
            });

            // Add Autocomplete
            const input = document.getElementById('locationSearch');
            const autocomplete = new google.maps.places.Autocomplete(input);
            autocomplete.bindTo('bounds', map);
            autocomplete.addListener('place_changed', function() {
                const place = autocomplete.getPlace();
                if (!place.geometry || !place.geometry.location) return;

                if (place.geometry.viewport) {
                    map.fitBounds(place.geometry.viewport);
                } else {
                    map.setCenter(place.geometry.location);
                    map.setZoom(17);
                }
                setLocation(place.geometry.location.lat(), place.geometry.location.lng());
            });

            // Use My Location
            document.getElementById('useMyLocation').addEventListener('click', function() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        const pos = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude
                        };
                        map.setCenter(pos);
                        map.setZoom(17);
                        setLocation(pos.lat, pos.lng);
                    }, function() {
                        alert('Error: The Geolocation service failed.');
                    });
                } else {
                    alert("Error: Your browser doesn't support geolocation.");
                }
            });
        }

        function setLocation(lat, lng) {
            const pos = { lat: lat, lng: lng };

            if (marker) marker.setMap(null);
            if (circle) circle.setMap(null);

            marker = new google.maps.Marker({
                position: pos,
                map: map,
                animation: google.maps.Animation.DROP
            });

            const radiusInput = document.getElementById('geoRadius');
            const radiusUnitElement = document.querySelector('[x-model="radiusUnit"]');
            const radiusUnit = radiusUnitElement ? radiusUnitElement.value : 'meters';

            let radius = parseFloat(radiusInput.value) || 50;
            if (radiusUnit === 'kilometers') {
                radius = radius * 1000;
            }

            circle = new google.maps.Circle({
                strokeColor: '#4F46E5',
                strokeOpacity: 0.8,
                strokeWeight: 2,
                fillColor: '#818CF8',
                fillOpacity: 0.35,
                map: map,
                center: pos,
                radius: radius
            });

            document.getElementById('geoLatitude').value = lat;
            document.getElementById('geoLongitude').value = lng;
            document.getElementById('latDisplay').textContent = lat.toFixed(6);
            document.getElementById('lngDisplay').textContent = lng.toFixed(6);
            document.getElementById('computedRadius').value = radius;
        }

        function updateRadius() {
            const lat = parseFloat(document.getElementById('geoLatitude').value);
            const lng = parseFloat(document.getElementById('geoLongitude').value);
            if (!isNaN(lat) && !isNaN(lng)) {
                setLocation(lat, lng);
            }
        }

        function searchLocation() {
            // This is now handled by Google Places Autocomplete place_changed event
            // but we keep the function name if it's referenced elsewhere
        }

        // Form submission handler
        // JavaScript (place in `resources/views/main-admin/elections.blade.php` script section)
        document.getElementById('electionForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formEl = this;
            const alpineData = Alpine.$data(formEl);
            alpineData.isSubmitting = true;

            try {
                const formData = new FormData(formEl);

                const tokenMeta = document.querySelector('meta[name="csrf-token"]');
                const csrfToken = tokenMeta ? tokenMeta.getAttribute('content') : null;
                if (!csrfToken) console.warn('CSRF meta tag not found. Add <meta name="csrf-token" content=\"{{ csrf_token() }}\"> to your layout.');

                const response = await fetch(formEl.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {})
                    },
                    credentials: 'same-origin' // <--- ensures session cookie is sent to the server
                });

                if (!response.ok) {
                    // log full response for debugging (server may return HTML on error)
                    const text = await response.text().catch(() => '');
                    console.error('Election create failed', response.status, text);
                    // try to parse JSON error body
                    try {
                        const err = text ? JSON.parse(text) : null;
                        alpineData.showError(err?.message || 'Server error', err?.errors || []);
                    } catch (parseErr) {
                        alpineData.showError('Server error: ' + response.status);
                    }
                    return;
                }

                const data = await response.json();

                if (data.success) {
                    alpineData.electionCreated = true;
                    alpineData.electionId = data.election.id;
                    alpineData.electionCode = data.election.code;
                    alpineData.registrationUrl = data.registration_url;
                    alpineData.activeTab = 'share';
                    setTimeout(() => alpineData.generateQRCode(alpineData.registrationUrl), 100);
                } else {
                    alpineData.showError(data.message || 'Failed to create election', data.errors || []);
                }
            } catch (error) {
                console.error('Unexpected error:', error);
                alpineData.showError('An unexpected error occurred');
            } finally {
                alpineData.isSubmitting = false;
            }
        });
    </script>
@endsection
