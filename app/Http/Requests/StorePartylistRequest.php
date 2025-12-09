<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePartylistRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $partylistId = $this->route('partylist')?->id;

        return [
            'name' => 'required|string|max:255|unique:partylists,name,' . ($partylistId ?? 'NULL'),
            'acronym' => 'nullable|string|max:10|unique:partylists,acronym,' . ($partylistId ?? 'NULL'),
            'description' => 'nullable|string|max:1000',
            'platform' => 'nullable|string|max:5000',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'color' => 'nullable|string|max:7|regex:/^#[0-9A-F]{6}$/i',
            'organization_id' => 'required|exists:organizations,id',
            'election_id' => 'nullable|exists:elections,id',
            'status' => 'required|in:active,pending,inactive',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Party list name is required',
            'name.unique' => 'Party list name already exists',
            'acronym.unique' => 'Party list acronym already exists',
            'color.regex' => 'Color must be a valid hex code (e.g., #FF0000)',
            'organization_id.required' => 'Organization is required',
        ];
    }
}
