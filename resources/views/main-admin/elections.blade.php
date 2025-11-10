@extends('layouts.app-main-admin')

@section('content')
    <div x-data="{
        collapsed: false,
        isMobile: window.innerWidth < 1024,
     }"
         x-init="window.addEventListener('resize', () => { isMobile = window.innerWidth < 1024 })"
         class="flex min-h-screen bg-gray-50">

        <x-admin-sidebar />

        <!-- Main Content -->
        <main class="flex-1 p-8">
            <!-- Page Header -->
            <header class="mb-10 max-w-6xl mx-auto">
                <div class="flex items-start justify-between gap-6">
                    <div class="flex-1 min-w-0">
                        <nav class="text-sm text-gray-500 mb-3" aria-label="Breadcrumb">
                            <ol class="flex items-center gap-2">
                                <li><a href="{{ route('admin.dashboard') }}" class="hover:underline text-gray-500">Dashboard</a></li>
                                <li aria-hidden="true">/</li>
                                <li><a href="{{ route('admin.elections.index') }}" class="hover:underline text-gray-500">Elections</a></li>
                                <li aria-hidden="true">/</li>
                                <li class="text-gray-900 font-semibold" aria-current="page">Create Election</li>
                            </ol>
                        </nav>

                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg flex-shrink-0">
                                <svg class="w-7 h-7 text-white" viewBox="0 0 24 24" fill="none" aria-hidden>
                                    <path d="M3 7v10a2 2 0 0 0 2 2h14V7H3z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M8 7V4h8v3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>

                            <div class="min-w-0">
                                <h1 class="text-3xl font-semibold text-gray-900 leading-tight">Create Election</h1>
                                <p class="text-gray-600 mt-1">Configure your voting election with comprehensive, secure settings — follow the stepper to complete creation.</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.elections.index') }}"
                           class="inline-flex items-center gap-2 px-4 py-2 border border-gray-200 bg-white text-sm rounded-lg hover:bg-gray-50">
                            <svg class="w-4 h-4 text-gray-600" viewBox="0 0 24 24" fill="none" aria-hidden>
                                <path d="M15 19l-7-7 7-7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Back
                        </a>

                        <button type="button"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden>
                                <path d="M5 12h14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M12 5v14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Save Draft
                        </button>
                    </div>
                </div>
            </header>

            <form id="electionForm" action="{{ route('admin.elections.store') }}" method="POST"
                  x-data="{ activeTab: 'basic', electionCreated: false, positions: [{ name: '', candidates: [''] }] }"
                  class="max-w-6xl mx-auto">
                @csrf

                <!-- Progress Stepper -->
                <div class="mb-8">
                    <div class="flex items-center justify-between bg-white p-4 border border-gray-200 rounded-lg shadow-sm">
                        <div class="w-full">
                            <div class="flex items-center">
                                <div class="flex items-center gap-6 w-full">
                                    <div class="flex items-center gap-6 w-full">
                                        <div class="flex items-center gap-4">
                                            <div :class="activeTab === 'basic' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-400 border border-gray-200'"
                                                 class="w-10 h-10 rounded-full flex items-center justify-center shadow-sm">
                                                <span class="text-sm font-medium">1</span>
                                            </div>
                                            <div class="min-w-0">
                                                <div :class="activeTab === 'basic' ? 'text-indigo-700 font-semibold' : 'text-gray-600'" class="text-sm">Basic Information</div>
                                                <div class="text-xs text-gray-400">Election details</div>
                                            </div>
                                        </div>

                                        <div class="flex-1 h-px bg-gray-200 mx-4 relative">
                                            <div :class="['candidates', 'settings', 'share'].includes(activeTab) ? 'absolute inset-0 w-full bg-indigo-600' : 'absolute inset-0 w-0 bg-indigo-600'"
                                                 class="h-full transition-all duration-500 ease-out"></div>
                                        </div>

                                        <div class="flex items-center gap-4">
                                            <div :class="activeTab === 'candidates' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-400 border border-gray-200'"
                                                 class="w-10 h-10 rounded-full flex items-center justify-center shadow-sm">
                                                <span class="text-sm font-medium">2</span>
                                            </div>
                                            <div class="min-w-0">
                                                <div :class="activeTab === 'candidates' ? 'text-indigo-700 font-semibold' : 'text-gray-600'" class="text-sm">Positions & Candidates</div>
                                                <div class="text-xs text-gray-400">Setup voting options</div>
                                            </div>
                                        </div>

                                        <div class="flex-1 h-px bg-transparent mx-4 relative hidden lg:block">
                                            <div :class="['settings', 'share'].includes(activeTab) ? 'absolute inset-0 w-full bg-indigo-600' : 'absolute inset-0 w-0 bg-indigo-600'"
                                                 class="h-full transition-all duration-500 ease-out"></div>
                                        </div>

                                        <div class="hidden lg:flex items-center gap-4">
                                            <div :class="activeTab === 'settings' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-400 border border-gray-200'"
                                                 class="w-10 h-10 rounded-full flex items-center justify-center shadow-sm">
                                                <span class="text-sm font-medium">3</span>
                                            </div>
                                            <div class="min-w-0">
                                                <div :class="activeTab === 'settings' ? 'text-indigo-700 font-semibold' : 'text-gray-600'" class="text-sm">Voting Settings</div>
                                                <div class="text-xs text-gray-400">Configure restrictions</div>
                                            </div>

                                            <div class="flex-1 h-px bg-transparent mx-4 relative">
                                                <div :class="activeTab === 'share' ? 'absolute inset-0 w-full bg-indigo-600' : 'absolute inset-0 w-0 bg-indigo-600'"
                                                     class="h-full transition-all duration-500 ease-out"></div>
                                            </div>

                                            <div :class="activeTab === 'share' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-400 border border-gray-200'"
                                                 class="w-10 h-10 rounded-full flex items-center justify-center shadow-sm">
                                                <span class="text-sm font-medium">4</span>
                                            </div>
                                            <div class="min-w-0">
                                                <div :class="activeTab === 'share' ? 'text-indigo-700 font-semibold' : 'text-gray-600'" class="text-sm">Share & Deploy</div>
                                                <div class="text-xs text-gray-400">Publish election</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Main Content Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="p-10">
                        <!-- Panel 1: Basic Information -->
                        <section x-show="activeTab === 'basic'"
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 transform translate-x-4"
                                 x-transition:enter-end="opacity-100 transform translate-x-0"
                                 aria-labelledby="basic-heading">
                            <div class="mb-8">
                                <h2 id="basic-heading" class="text-2xl font-bold text-gray-900 mb-2">Basic Information</h2>
                                <p class="text-gray-600">Provide fundamental details about your election</p>
                            </div>

                            <div class="space-y-8">
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                    <div class="lg:col-span-2">
                                        <label for="formTitle" class="block text-sm font-medium text-gray-900 mb-3">Election Title</label>
                                        <input type="text" id="formTitle" name="title" required
                                               class="block w-full rounded-lg border-gray-200 shadow-sm focus:ring-2 focus:ring-indigo-500 px-4 py-3" placeholder="e.g., 2025 General Election">
                                    </div>

                                    <div class="lg:col-span-2">
                                        <label for="organization" class="block text-sm font-medium text-gray-900 mb-3">Organization</label>
                                        <select id="organization" name="organization_id" required
                                                class="block w-full rounded-lg border-gray-200 shadow-sm focus:ring-2 focus:ring-indigo-500 px-4 py-3">
                                            <option value="">Select organization</option>
                                            @foreach($organizations ?? [] as $org)
                                                <option value="{{ $org->id }}">{{ $org->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="lg:col-span-2">
                                        <label for="description" class="block text-sm font-medium text-gray-900 mb-3">Description</label>
                                        <textarea id="description" name="description" rows="4"
                                                  class="block w-full rounded-lg border-gray-200 shadow-sm focus:ring-2 focus:ring-indigo-500 px-4 py-3" placeholder="Describe the scope and purpose of this election"></textarea>
                                    </div>

                                    <div>
                                        <label for="votingStart" class="block text-sm font-medium text-gray-900 mb-3">Voting Start</label>
                                        <input type="datetime-local" id="votingStart" name="voting_start" required
                                               class="block w-full rounded-lg border-gray-200 shadow-sm focus:ring-2 focus:ring-indigo-500 px-4 py-3">
                                    </div>

                                    <div>
                                        <label for="votingEnd" class="block text-sm font-medium text-gray-900 mb-3">Voting End</label>
                                        <input type="datetime-local" id="votingEnd" name="voting_end" required
                                               class="block w-full rounded-lg border-gray-200 shadow-sm focus:ring-2 focus:ring-indigo-500 px-4 py-3">
                                    </div>
                                </div>

                                <div class="flex justify-end pt-8 border-t border-gray-200">
                                    <button type="button" @click="activeTab = 'candidates'"
                                            class="px-8 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all font-medium">
                                        Continue
                                        <svg class="w-4 h-4 ml-2 inline-block" viewBox="0 0 24 24" fill="none" aria-hidden>
                                            <path d="M5 12h14M12 5l7 7-7 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
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
                            <div class="mb-8">
                                <h2 id="positions-heading" class="text-2xl font-bold text-gray-900 mb-2">Positions & Candidates</h2>
                                <p class="text-gray-600">Define the positions available for voting and their candidates</p>
                            </div>

                            <div class="space-y-6">
                                <template x-for="(position, index) in positions" :key="index">
                                    <div class="border border-gray-200 rounded-xl p-6 bg-gray-50 hover:bg-gray-100 transition-colors">
                                        <div class="flex items-start justify-between gap-4 mb-4">
                                            <div class="flex-1">
                                                <label class="block text-sm font-medium text-gray-900 mb-2">Position name</label>
                                                <input type="text" x-model="position.name" :name="`positions[${index}][name]`"
                                                       class="block w-full rounded-lg border-gray-200 shadow-sm focus:ring-2 focus:ring-indigo-500 px-4 py-3" placeholder="e.g., Mayor">
                                            </div>
                                        </div>

                                        <div class="space-y-3">
                                            <template x-for="(c, cidx) in position.candidates" :key="cidx">
                                                <div class="flex items-center gap-3">
                                                    <input type="text" x-model="position.candidates[cidx]" :name="`positions[${index}][candidates][${cidx}]`"
                                                           class="flex-1 rounded-lg border-gray-200 shadow-sm focus:ring-2 focus:ring-indigo-500 px-4 py-2" placeholder="Candidate name">
                                                    <button type="button" @click="position.candidates.splice(cidx,1)"
                                                            class="p-2 rounded-md text-red-600 hover:bg-red-50 focus:outline-none" aria-label="Remove candidate">
                                                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" aria-hidden>
                                                            <path d="M3 6h18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                            <path d="M8 6v14a2 2 0 0 0 2 2h4a2 2 0 0 0 2-2V6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                            <path d="M10 11v6M14 11v6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </template>

                                            <div class="flex justify-end">
                                                <button type="button" @click="position.candidates.push('')"
                                                        class="px-4 py-2 bg-indigo-50 text-indigo-700 border border-indigo-100 rounded-lg hover:bg-indigo-100">
                                                    Add Candidate
                                                </button>
                                            </div>
                                        </div>

                                        <div class="mt-4 flex justify-end">
                                            <button type="button" @click="positions.splice(index,1)"
                                                    class="p-2 bg-red-50 text-red-600 rounded-lg border border-red-100 hover:bg-red-100 focus:outline-none" aria-label="Remove position">
                                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" aria-hidden>
                                                    <path d="M3 6h18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M8 6v14a2 2 0 0 0 2 2h4a2 2 0 0 0 2-2V6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M10 11v6M14 11v6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </template>

                                <div class="flex justify-center">
                                    <button type="button" @click="positions.push({ name: '', candidates: [''] })"
                                            class="px-6 py-3 bg-indigo-50 text-indigo-700 border border-indigo-100 rounded-xl hover:bg-indigo-100 font-medium">
                                        <svg class="w-4 h-4 inline-block mr-2" viewBox="0 0 24 24" fill="none" aria-hidden>
                                            <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        Add New Position
                                    </button>
                                </div>

                                <div class="flex justify-between pt-6 border-t border-gray-200">
                                    <button type="button" @click="activeTab = 'basic'"
                                            class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium">
                                        Previous
                                    </button>
                                    <button type="button" @click="activeTab = 'settings'"
                                            class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium">
                                        Continue
                                    </button>
                                </div>
                            </div>
                        </section>

                        <!-- Panel 3: Voting Settings (enhanced with Geo & extra fields) -->
                        <section x-show="activeTab === 'settings'"
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 transform translate-x-4"
                                 x-transition:enter-end="opacity-100 transform translate-x-0"
                                 x-data="{ enableGeo: false, mapInitialized: false }"
                                 x-init="$watch('enableGeo', value => { if(value && !mapInitialized){ setTimeout(() => { initGeoMap(); mapInitialized = true }, 200) } })"
                                 aria-labelledby="settings-heading">
                            <div class="mb-8">
                                <h2 id="settings-heading" class="text-2xl font-bold text-gray-900 mb-2">Voting Settings</h2>
                                <p class="text-gray-600">Configure security and access restrictions for your election</p>
                            </div>

                            <div class="space-y-6">
                                <div class="bg-blue-50 border border-blue-100 rounded-xl p-6">
                                    <label class="flex items-start cursor-pointer gap-4">
                                        <input type="checkbox" name="enable_geo_location" x-model="enableGeo" class="mt-1">
                                        <div>
                                            <div class="font-medium text-gray-900">Restrict by geographic location</div>
                                            <div class="text-sm text-gray-600">Limit voting to a configured geographic region</div>
                                        </div>
                                    </label>
                                </div>

                                <!-- Geo configuration: show when enabled -->
                                <div x-show="enableGeo"
                                     x-transition:enter="transition ease-out duration-300"
                                     x-transition:enter-start="opacity-0 transform translate-y-4"
                                     x-transition:enter-end="opacity-100 transform translate-y-0"
                                     class="bg-white border border-gray-200 rounded-xl p-6 space-y-4">
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Geographic Configuration</h3>

                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 items-end">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-900 mb-2">Search location</label>
                                            <input id="geoSearch" class="block w-full rounded-lg border-gray-200 px-3 py-2" placeholder="Search address or place (autocomplete)">
                                        </div>

                                        <div class="flex gap-2">
                                            <button type="button" id="useMyLocation" class="px-3 py-2 bg-indigo-50 text-indigo-700 border border-indigo-100 rounded-lg">Use my location</button>
                                            <select id="mapType" class="rounded-lg border-gray-200 px-3 py-2">
                                                <option value="terrain">Terrain</option>
                                                <option value="satellite">Satellite</option>
                                                <option value="roadmap">Roadmap</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-900 mb-2">Latitude</label>
                                            <input type="text" id="geoLatitude" name="geo_latitude"
                                                   class="block w-full rounded-lg border-gray-200 px-3 py-2" placeholder="e.g., 37.421998">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-900 mb-2">Longitude</label>
                                            <input type="text" id="geoLongitude" name="geo_longitude"
                                                   class="block w-full rounded-lg border-gray-200 px-3 py-2" placeholder="e.g., -122.084000">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-900 mb-2">Radius (meters)</label>
                                            <div class="flex items-center gap-2">
                                                <input type="number" id="geoRadius" name="geo_radius" min="10" value="1000"
                                                       class="block w-full rounded-lg border-gray-200 px-3 py-2" />
                                                <span class="text-sm text-gray-600">m</span>
                                            </div>
                                        </div>

                                        <div class="col-span-1 lg:col-span-2">
                                            <label class="block text-sm font-medium text-gray-900 mb-2">Map preview</label>
                                            <div id="geoMap" class="w-full h-64 rounded-lg border border-gray-200 overflow-hidden"></div>
                                        </div>
                                    </div>

                                    <p class="text-sm text-gray-500">Map shows a marker for the chosen location and a radius circle. Use the search box or "Use my location".</p>
                                </div>

                                <!-- Additional settings below geo: allowed email domain, registration deadline -->
                                <div class="bg-white border border-gray-200 rounded-xl p-6 space-y-4">
                                    <h3 class="text-lg font-medium text-gray-900">Access & Registration</h3>

                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-900 mb-2">Allowed email domain (comma separated)</label>
                                            <input type="text" name="allowed_email_domain" placeholder="example.com, org.edu"
                                                   class="block w-full rounded-lg border-gray-200 px-3 py-2" />
                                            <p class="text-xs text-gray-500 mt-1">Only emails matching these domains will be allowed (validated server-side).</p>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-900 mb-2">Registration deadline</label>
                                            <input type="datetime-local" name="registration_deadline"
                                                   class="block w-full rounded-lg border-gray-200 px-3 py-2" />
                                            <p class="text-xs text-gray-500 mt-1">After this time new voters cannot register to vote in this election.</p>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-900 mb-2">Require authentication</label>
                                            <select name="require_authentication" class="block w-full rounded-lg border-gray-200 px-3 py-2">
                                                <option value="1">Yes - require login</option>
                                                <option value="0" selected>No - open access</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-900 mb-2">Max votes per voter</label>
                                            <input type="number" name="max_votes_per_voter" min="1" value="1"
                                                   class="block w-full rounded-lg border-gray-200 px-3 py-2" />
                                        </div>
                                    </div>
                                </div>

                                <div class="flex justify-between pt-6 border-t border-gray-200">
                                    <button type="button" @click="activeTab = 'candidates'"
                                            class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium">
                                        Previous
                                    </button>
                                    <button type="submit"
                                            class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 font-medium">
                                        Create Election
                                    </button>
                                </div>
                            </div>
                        </section>

                        <!-- Panel 4: Share Form -->
                        <section x-show="activeTab === 'share'"
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 transform translate-x-4"
                                 x-transition:enter-end="opacity-100 transform translate-x-0"
                                 aria-labelledby="share-heading">
                            <div class="mb-8">
                                <h2 id="share-heading" class="text-2xl font-bold text-gray-900 mb-2">Election Deployment</h2>
                                <p class="text-gray-600">Finalize and distribute access to your election</p>
                            </div>

                            <div class="space-y-6">
                                <div class="bg-white border border-gray-200 rounded-xl p-6">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Voter Access Link</h3>
                                    <div class="flex gap-4">
                                        <input type="text" id="shareLink" readonly class="flex-1 rounded-lg border-gray-200 px-3 py-2" value="{{ url('/') }}" />
                                        <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('shareLink').value)"
                                                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Copy</button>
                                    </div>
                                    <p class="text-sm text-gray-500 mt-3">Share this link with eligible voters to access the election.</p>
                                </div>

                                <div class="bg-white border border-gray-200 rounded-xl p-6 text-center">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">QR Code Access</h3>
                                    <div class="inline-block p-6 bg-gray-50 rounded-xl border border-gray-200 mb-4">
                                        <div id="qrcode" class="inline-block"></div>
                                    </div>
                                    <div class="space-y-3">
                                        <button type="button" onclick="/* implement downloadQR */" class="px-4 py-2 bg-indigo-600 text-white rounded-lg">Download QR</button>
                                        <p class="text-sm text-gray-500">Voters can scan this QR code to quickly access the election.</p>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </form>
        </main>
    </div>

    <!-- Google Maps & helper scripts -->
    @php
        $gmKey = config('services.google_maps.key') ?? env('GOOGLE_MAPS_API_KEY');
    @endphp

    @if(!$gmKey)
        <script>console.warn('Google Maps API key not set in config/services.php or .env (GOOGLE_MAPS_API_KEY)');</script>
    @endif

    <script src="https://maps.googleapis.com/maps/api/js?key={{ $gmKey }}&libraries=places&v=weekly" defer></script>
    <script>
        // Geolocation map logic. initGeoMap is called when the geo section is first enabled.
        function initGeoMap() {
            if (!window.google || !google.maps) {
                console.error('Google Maps JS not loaded yet.');
                return;
            }

            const mapEl = document.getElementById('geoMap');
            const latInput = document.getElementById('geoLatitude');
            const lngInput = document.getElementById('geoLongitude');
            const radiusInput = document.getElementById('geoRadius');
            const searchInput = document.getElementById('geoSearch');
            const useMyLocBtn = document.getElementById('useMyLocation');
            const mapTypeSelect = document.getElementById('mapType');

            const defaultPos = { lat: 37.421998, lng: -122.084000 }; // fallback
            const map = new google.maps.Map(mapEl, {
                center: defaultPos,
                zoom: 13,
                mapTypeId: google.maps.MapTypeId.TERRAIN,
            });

            let marker = new google.maps.Marker({
                position: defaultPos,
                map,
                draggable: true,
            });

            let circle = new google.maps.Circle({
                map,
                radius: Number(radiusInput.value || 1000),
                center: defaultPos,
                fillColor: '#3b82f6',
                fillOpacity: 0.12,
                strokeColor: '#3b82f6',
                strokeOpacity: 0.6,
                strokeWeight: 1,
            });

            // keep inputs synced
            function updateInputsFromPosition(pos) {
                latInput.value = pos.lat.toFixed(6);
                lngInput.value = pos.lng.toFixed(6);
            }
            function updateCircleRadius() {
                const r = Number(radiusInput.value) || 0;
                circle.setRadius(r);
            }

            // Drag marker -> update circle and inputs
            marker.addListener('dragend', () => {
                const pos = marker.getPosition();
                const latlng = { lat: pos.lat(), lng: pos.lng() };
                circle.setCenter(latlng);
                updateInputsFromPosition(latlng);
                map.panTo(latlng);
            });

            // Changing numeric inputs should update marker & circle
            latInput.addEventListener('change', () => {
                const lat = parseFloat(latInput.value) || defaultPos.lat;
                const lng = parseFloat(lngInput.value) || defaultPos.lng;
                const pos = { lat, lng };
                marker.setPosition(pos);
                circle.setCenter(pos);
                map.panTo(pos);
            });
            lngInput.addEventListener('change', () => {
                const lat = parseFloat(latInput.value) || defaultPos.lat;
                const lng = parseFloat(lngInput.value) || defaultPos.lng;
                const pos = { lat, lng };
                marker.setPosition(pos);
                circle.setCenter(pos);
                map.panTo(pos);
            });
            radiusInput.addEventListener('input', () => {
                updateCircleRadius();
            });

            // Map type selector
            mapTypeSelect.addEventListener('change', () => {
                const val = mapTypeSelect.value;
                map.setMapTypeId(val.toUpperCase() === 'SATELLITE' ? google.maps.MapTypeId.SATELLITE : (val.toUpperCase() === 'TERRAIN' ? google.maps.MapTypeId.TERRAIN : google.maps.MapTypeId.ROADMAP));
            });

            // Places Autocomplete
            const autocomplete = new google.maps.places.Autocomplete(searchInput);
            autocomplete.addListener('place_changed', () => {
                const place = autocomplete.getPlace();
                if (!place.geometry) return;
                const loc = {
                    lat: place.geometry.location.lat(),
                    lng: place.geometry.location.lng()
                };
                marker.setPosition(loc);
                circle.setCenter(loc);
                updateInputsFromPosition(loc);
                map.panTo(loc);
                map.setZoom(14);
            });

            // Use current location
            useMyLocBtn.addEventListener('click', () => {
                if (!navigator.geolocation) {
                    alert('Geolocation is not supported by your browser.');
                    return;
                }
                useMyLocBtn.disabled = true;
                useMyLocBtn.innerText = 'Locating...';
                navigator.geolocation.getCurrentPosition((position) => {
                    const pos = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };
                    marker.setPosition(pos);
                    circle.setCenter(pos);
                    updateInputsFromPosition(pos);
                    map.panTo(pos);
                    map.setZoom(14);
                    useMyLocBtn.disabled = false;
                    useMyLocBtn.innerText = 'Use my location';
                }, (err) => {
                    console.error(err);
                    alert('Unable to retrieve your location.');
                    useMyLocBtn.disabled = false;
                    useMyLocBtn.innerText = 'Use my location';
                }, { timeout: 10000 });
            });

            // Initialize inputs from defaults
            updateInputsFromPosition(defaultPos);
            updateCircleRadius();

            // expose for debugging
            window._geoMap = { map, marker, circle, updateCircleRadius };
        }
    </script>
@endsection
