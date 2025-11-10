<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->keyBy('key');

        return view('main-admin.settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'site_name' => 'required|string|max:255',
            'site_description' => 'nullable|string|max:500',
            'site_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'maintenance_mode' => 'boolean',
            'voting_enabled' => 'boolean',
            'registration_enabled' => 'boolean',
            'max_candidates_per_position' => 'required|integer|min:1|max:50',
            'voting_start_date' => 'nullable|date',
            'voting_end_date' => 'nullable|date|after:voting_start_date',
            'results_public' => 'boolean',
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'backup_frequency' => 'required|in:daily,weekly,monthly',
            'session_timeout' => 'required|integer|min:5|max:1440',
        ]);

        try {
            DB::beginTransaction();

            $settingsData = $request->except(['_token', 'site_logo']);

            // Handle logo upload
            if ($request->hasFile('site_logo')) {
                $logoPath = $request->file('site_logo')->store('settings', 'public');
                $settingsData['site_logo'] = $logoPath;
            }

            foreach ($settingsData as $key => $value) {
                Setting::updateOrCreate(
                    ['key' => $key],
                    [
                        'value' => is_bool($value) ? ($value ? '1' : '0') : $value,
                        'type' => $this->getSettingType($key, $value)
                    ]
                );
            }

            // Clear settings cache
            Cache::forget('system_settings');

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Settings updated successfully!'
                ]);
            }

            return redirect()->route('admin.settings')
                ->with('success', 'Settings updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating settings: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while updating settings.'
                ], 500);
            }

            return back()->withErrors(['general' => 'An error occurred while updating settings.'])
                ->withInput();
        }
    }

    public function reset()
    {
        try {
            DB::beginTransaction();

            // Reset to default values
            $defaultSettings = [
                'site_name' => 'SecureVote System',
                'site_description' => 'Secure Electronic Voting System',
                'contact_email' => 'admin@securevote.com',
                'maintenance_mode' => '0',
                'voting_enabled' => '1',
                'registration_enabled' => '1',
                'max_candidates_per_position' => '10',
                'results_public' => '0',
                'email_notifications' => '1',
                'sms_notifications' => '0',
                'backup_frequency' => 'daily',
                'session_timeout' => '60',
            ];

            foreach ($defaultSettings as $key => $value) {
                Setting::updateOrCreate(
                    ['key' => $key],
                    [
                        'value' => $value,
                        'type' => $this->getSettingType($key, $value)
                    ]
                );
            }

            Cache::forget('system_settings');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Settings reset to default values!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error resetting settings: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while resetting settings.'
            ], 500);
        }
    }

    public function backup()
    {
        try {
            $settings = Setting::all();
            $backup = [
                'timestamp' => now()->toISOString(),
                'settings' => $settings->toArray()
            ];

            $filename = 'settings_backup_' . now()->format('Y_m_d_H_i_s') . '.json';
            $content = json_encode($backup, JSON_PRETTY_PRINT);

            return response($content)
                ->header('Content-Type', 'application/json')
                ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");

        } catch (\Exception $e) {
            Log::error('Error creating settings backup: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating backup.'
            ], 500);
        }
    }

    public function restore(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|mimes:json'
        ]);

        try {
            DB::beginTransaction();

            $file = $request->file('backup_file');
            $content = file_get_contents($file->getPathname());
            $backup = json_decode($content, true);

            if (!isset($backup['settings'])) {
                throw new \Exception('Invalid backup file format');
            }

            foreach ($backup['settings'] as $setting) {
                Setting::updateOrCreate(
                    ['key' => $setting['key']],
                    [
                        'value' => $setting['value'],
                        'type' => $setting['type'] ?? 'string'
                    ]
                );
            }

            Cache::forget('system_settings');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Settings restored successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error restoring settings: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error restoring settings: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getSettingType($key, $value)
    {
        if (is_bool($value) || in_array($key, ['maintenance_mode', 'voting_enabled', 'registration_enabled', 'results_public', 'email_notifications', 'sms_notifications'])) {
            return 'boolean';
        }

        if (is_numeric($value) || in_array($key, ['max_candidates_per_position', 'session_timeout'])) {
            return 'integer';
        }

        if (in_array($key, ['voting_start_date', 'voting_end_date'])) {
            return 'date';
        }

        if (strpos($key, 'email') !== false) {
            return 'email';
        }

        return 'string';
    }
}
