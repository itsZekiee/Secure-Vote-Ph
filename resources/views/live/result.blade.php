@extends('voter.layouts.app')

@section('title', 'Live Results - ' . $election->title)

@push('styles')
    <style>
        .white-smoke-bg { background-color: #F5F5F5; }
        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .gradient-brand { background: linear-gradient(135deg, #003153 0%, #00D4AA 100%); }
        .text-brand-primary { color: #003153; }
        .text-brand-accent { color: #00D4AA; }
    </style>
@endpush

@section('content')
    <div class="min-h-screen white-smoke-bg py-12 px-4">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-16">
                <div class="inline-flex items-center px-4 py-2 bg-white rounded-full shadow-sm mb-6 border border-slate-100">
                    <span class="w-2 h-2 bg-red-500 rounded-full mr-2 animate-ping"></span>
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Live Election Results</span>
                </div>
                <h1 class="text-4xl lg:text-6xl font-black text-brand-primary mb-4 tracking-tight">{{ $election->title }}</h1>
                <p class="text-slate-500 text-lg">Real-time vote tallies and candidate comparison</p>
            </div>

            <!-- Comparison Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                @foreach($partylists as $party)
                    <div class="flex flex-col h-full">
                        <!-- Party Header -->
                        <div class="bg-white rounded-[2.5rem] shadow-xl p-8 mb-8 border border-white relative overflow-hidden transition-all hover:shadow-2xl">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-brand-accent/5 rounded-full -mr-16 -mt-16 blur-2xl"></div>

                            <div class="flex items-center gap-6 relative z-10">
                                <div class="w-24 h-24 rounded-3xl overflow-hidden shadow-lg border-4 border-slate-50 bg-slate-50">
                                    @if($party->logo)
                                        <img src="{{ asset('storage/' . $party->logo) }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full gradient-brand flex items-center justify-center">
                                            <span class="text-white text-3xl font-black">{{ substr($party->name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <h2 class="text-3xl font-black text-brand-primary tracking-tight">{{ $party->name }}</h2>
                                    @if($party->acronym)
                                        <p class="text-brand-accent font-bold uppercase tracking-widest text-sm">{{ $party->acronym }}</p>
                                    @endif
                                </div>
                            </div>

                            @if($party->description)
                                <div class="mt-6 p-4 bg-slate-50 rounded-2xl border border-slate-100">
                                    <p class="text-slate-600 text-sm italic">"{{ $party->description }}"</p>
                                </div>
                            @endif
                        </div>

                        <!-- Candidates List -->
                        <div class="space-y-6 flex-grow">
                            @foreach($party->candidates as $candidate)
                                <div class="bg-white rounded-[2rem] p-6 shadow-md border border-white transition-all hover:translate-x-2">
                                    <div class="flex items-center gap-6">
                                        <div class="relative">
                                            <div class="w-20 h-20 rounded-2xl overflow-hidden shadow-inner bg-slate-100">
                                                @if($candidate->photo)
                                                    <img src="{{ asset('storage/' . $candidate->photo) }}" class="w-full h-full object-cover">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center text-slate-300">
                                                        <i class="fas fa-user text-3xl"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="absolute -bottom-2 -right-2 w-10 h-10 bg-brand-primary text-white rounded-xl flex items-center justify-center shadow-lg border-2 border-white">
                                                <span class="text-xs font-black">{{ $candidate->votes_count }}</span>
                                            </div>
                                        </div>

                                        <div class="flex-grow">
                                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">{{ $candidate->position->name }}</p>
                                            <h4 class="text-xl font-black text-brand-primary leading-tight">{{ $candidate->name }}</h4>

                                            @if($candidate->platform)
                                                <button type="button" onclick="togglePlatform('p{{ $candidate->id }}')" class="mt-2 text-xs font-bold text-brand-accent hover:underline flex items-center gap-1">
                                                    <i class="fas fa-info-circle"></i> View Agenda
                                                </button>
                                            @endif
                                        </div>
                                    </div>

                                    @if($candidate->platform)
                                        <div id="p{{ $candidate->id }}" class="hidden mt-4 pt-4 border-t border-slate-50">
                                            <p class="text-slate-600 text-sm leading-relaxed">{{ $candidate->platform }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-20 text-center">
                <a href="{{ route('voter.elections.vote', $election->code) }}" class="inline-flex items-center gap-3 px-8 py-4 bg-white text-brand-primary font-black rounded-3xl shadow-lg hover:shadow-xl transition-all border border-slate-100">
                    <i class="fas fa-arrow-left"></i>
                    BACK TO BALLOT
                </a>
            </div>
        </div>
    </div>

    <script>
        function togglePlatform(id) {
            const el = document.getElementById(id);
            el.classList.toggle('hidden');
        }

        // Auto refresh every 30 seconds
        setTimeout(() => {
            window.location.reload();
        }, 30000);
    </script>
@endsection
