<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateElectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && ($this->user()->id === $this->election?->created_by || $this->user()->isAdmin());
    }

    public function rules(): array
    {
        $electionId = $this->route('election')?->id;

        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'organization_id' => 'required|exists:organizations,id',
            'voting_start' => 'required|date',
            'voting_end' => 'required|date|after:voting_start',
            'enable_geo_location' => 'boolean',
            'geo_latitude' => 'nullable|numeric|between:-90,90',
            'geo_longitude' => 'nullable|numeric|between:-180,180',
            'geo_radius' => 'nullable|integer|min:1',
            'status' => 'required|in:draft,active,completed,cancelled',
            'sub_admin_ids' => 'nullable|array',
            'sub_admin_ids.*' => 'exists:users,id',
        ];
    }
}
