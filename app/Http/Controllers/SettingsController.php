<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserSetting;

class SettingsController extends Controller
{
    /**
     * Get user settings
     */
    public function index()
    {
        $user = Auth::user();
        $settings = UserSetting::where('user_id', $user->id)->first();

        if (!$settings) {
            // Return default settings
            return response()->json($this->getDefaultSettings());
        }

        return response()->json($settings->settings_data);
    }

    /**
     * Save user settings
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'language' => 'nullable|string',
            'dateFormat' => 'nullable|string',
            'time24h' => 'nullable|boolean',
            'currency' => 'nullable|string',
            'useMiles' => 'nullable|boolean',
            'fuelUnit' => 'nullable|string',
            'useGallons' => 'nullable|boolean',
            'defaultVehicle' => 'nullable|string',
            'fontSize' => 'nullable|string',
            'viewDensity' => 'nullable|string',
            'notificationsEnabled' => 'nullable|boolean',
            'expirationNoticeDays' => 'nullable|string',
            'notificationSound' => 'nullable|boolean',
            'quietHoursStart' => 'nullable|string',
            'quietHoursEnd' => 'nullable|string',
            'autoBackupFrequency' => 'nullable|string',
            'autoLockTimeout' => 'nullable|string',
            'requirePasswordOnLaunch' => 'nullable|boolean',
            'dataSharing' => 'nullable|boolean',
        ]);

        $settings = UserSetting::updateOrCreate(
            ['user_id' => $user->id],
            ['settings_data' => $validated]
        );

        return response()->json([
            'message' => 'Paramètres enregistrés avec succès',
            'settings' => $settings->settings_data
        ]);
    }

    /**
     * Get default settings
     */
    private function getDefaultSettings()
    {
        return [
            'language' => 'fr',
            'dateFormat' => 'DD/MM/YYYY',
            'time24h' => true,
            'currency' => 'TND',
            'useMiles' => false,
            'fuelUnit' => 'L/100km',
            'useGallons' => false,
            'defaultVehicle' => '',
            'fontSize' => 'medium',
            'viewDensity' => 'comfortable',
            'notificationsEnabled' => true,
            'expirationNoticeDays' => '15',
            'notificationSound' => true,
            'quietHoursStart' => '22:00',
            'quietHoursEnd' => '08:00',
            'autoBackupFrequency' => 'weekly',
            'autoLockTimeout' => '15',
            'requirePasswordOnLaunch' => false,
            'dataSharing' => false,
        ];
    }
}
