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
                          isSubmitting: false,
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
                                                :disabled="isSubmitting"
                                                :class="isSubmitting ? 'opacity-50 cursor-not-allowed' : ''"
                                                @click.prevent="
                                                    if (isSubmitting) return;
                                                    isSubmitting = true;

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
                                                        if (!res.ok) {
                                                            return res.json().then(errorData => {
                                                                throw { status: res.status, data: errorData };
                                                            }).catch(() => {
                                                                return res.text().then(text => {
                                                                    throw { status: res.status, message: text || 'Server error occurred' };
                                                                });
                                                            });
                                                        }
                                                        return res.json();
                                                    })
                                                    .then(data => {
                                                        if (data.success) {
                                                            electionCreated = true;
                                                            electionId = data.election.id;
                                                            electionCode = data.election.code;
                                                            registrationUrl = data.registration_url;
                                                            activeTab = 'share';
                                                            setTimeout(() => generateQRCode(registrationUrl), 100);
                                                        } else {
                                                            let errorDetails = [];
                                                            if (data.errors) {
                                                                for (let field in data.errors) {
                                                                    if (Array.isArray(data.errors[field])) {
                                                                        errorDetails = errorDetails.concat(data.errors[field]);
                                                                    } else {
                                                                        errorDetails.push(data.errors[field]);
                                                                    }
                                                                }
                                                            }
                                                            showError(data.message || 'Failed to create election', errorDetails);
                                                        }
                                                    })
                                                    .catch(err => {
                                                        console.error('Fetch error:', err);
                                                        let errorMessage = 'An unexpected error occurred';
                                                        let errorDetails = [];

                                                        if (err.data) {
                                                            errorMessage = err.data.message || 'Failed to create election';
                                                            if (err.data.errors) {
                                                                for (let field in err.data.errors) {
                                                                    if (Array.isArray(err.data.errors[field])) {
                                                                        errorDetails = errorDetails.concat(err.data.errors[field]);
                                                                    } else {
                                                                        errorDetails.push(err.data.errors[field]);
                                                                    }
                                                                }
                                                            }
                                                        } else if (err.message) {
                                                            errorMessage = err.message;
                                                        }

                                                        if (err.status === 422) {
                                                            errorMessage = 'Validation failed. Please check your input.';
                                                        } else if (err.status === 500) {
                                                            errorMessage = 'Server error. Please try again later.';
                                                        } else if (err.status === 403) {
                                                            errorMessage = 'You do not have permission to create an election.';
                                                        }

                                                        showError(errorMessage, errorDetails);
                                                    })
                                                    .finally(() => {
                                                        isSubmitting = false;
                                                    });
                                                "
                                                class="px-10 py-4 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:shadow-xl hover:shadow-green-500/30 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 font-semibold transition-all flex items-center gap-2">
                                            <svg x-show="isSubmitting" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            <span x-text="isSubmitting ? 'Creating...' : '🎉 Create Election'"></span>
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
                                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                                <svg class="w-4 h-4 text-green-600" viewBox="0 0 24 24" fill="none">
                                                    <path d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h2M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </div>
                                            <h3 class="text-xl font-bold text-gray-900">QR Code</h3>
                                        </div>
                                        <p class="text-sm text-gray-600 mb-6">Voters can scan this QR code to access the registration page</p>

                                        <div class="flex flex-col items-center">
                                            <div id="qrCodeDisplay" class="bg-white p-6 rounded-2xl border-2 border-gray-200 shadow-inner"></div>
                                            <button type="button"
                                                    @click="
                                                        const canvas = document.querySelector('#qrCodeDisplay canvas');
                                                        if (canvas) {
                                                            const link = document.createElement('a');
                                                            link.download = 'election-qr-' + electionCode + '.png';
                                                            link.href = canvas.toDataURL('image/png');
                                                            link.click();
                                                        }
                                                    "
                                                    class="mt-6 px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:shadow-lg hover:shadow-green-500/30 font-medium transition-all flex items-center gap-2">
                                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                                                    <path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                                Download QR Code
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="flex justify-between pt-10 border-t border-gray-200">
                                        <a href="{{ route('admin.elections.index') }}"
                                           class="px-8 py-4 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 font-semibold transition-all">
                                            ← Back to Elections
                                        </a>
                                        <a :href="'/admin/elections/' + electionId"
                                           class="px-10 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:shadow-xl hover:shadow-indigo-500/30 font-semibold transition-all flex items-center gap-2">
                                            View Election Details
                                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                                                <path d="M5 12h14M12 5l7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
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

    <!-- QR Code Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <!-- Google Maps Script -->
    <script>
        let map, marker, circle;

        function initGeoMap() {
            const defaultCenter = { lat: 14.5995, lng: 120.9842 }; // Manila, Philippines

            map = new google.maps.Map(document.getElementById('geoMap'), {
                center: defaultCenter,
                zoom: 15,
                mapTypeId: 'terrain',
                styles: [
                    { featureType: 'poi', stylers: [{ visibility: 'off' }] }
                ]
            });

            marker = new google.maps.Marker({
                position: defaultCenter,
                map: map,
                draggable: true,
                title: 'Voting Location'
            });

            circle = new google.maps.Circle({
                map: map,
                radius: 50,
                fillColor: '#4F46E5',
                fillOpacity: 0.2,
                strokeColor: '#4F46E5',
                strokeOpacity: 0.8,
                strokeWeight: 2
            });

            circle.bindTo('center', marker, 'position');

            marker.addListener('dragend', function() {
                updateCoordinates(marker.getPosition());
            });

            // Map type selector
            document.getElementById('mapType').addEventListener('change', function() {
                map.setMapTypeId(this.value);
            });

            // Use my location button
            document.getElementById('useMyLocation').addEventListener('click', function() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        const pos = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude
                        };
                        map.setCenter(pos);
                        marker.setPosition(pos);
                        updateCoordinates(pos);
                    });
                }
            });

            // Radius input listener
            document.getElementById('geoRadius').addEventListener('input', function() {
                let radius = parseFloat(this.value) || 50;
                const unit = document.querySelector('[x-model="radiusUnit"]').value;
                if (unit === 'kilometers') {
                    radius = radius * 1000;
                }
                circle.setRadius(radius);
            });

            updateCoordinates(defaultCenter);
        }

        function updateCoordinates(pos) {
            const lat = typeof pos.lat === 'function' ? pos.lat() : pos.lat;
            const lng = typeof pos.lng === 'function' ? pos.lng() : pos.lng;
            document.getElementById('geoLatitude').value = lat;
            document.getElementById('geoLongitude').value = lng;
        }
    </script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&callback=initGeoMap"></script>
@endsection

