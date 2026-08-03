<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Contracts\SettingRepositoryInterface;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class SettingController extends Controller
{
    protected $settingRepo;

    public function __construct(SettingRepositoryInterface $settingRepo)
    {
        $this->settingRepo = $settingRepo;
    }

    public function index()
    {
        $settings = $this->settingRepo->all();
        $user = Auth::user();
        return view('admin.settings.index', compact('settings', 'user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update($request->only(['name', 'username', 'email']));

        ActivityLog::log("Memperbarui profil admin: {$user->username}");

        return redirect()->route('admin.settings.index')->with('success', 'Profil berhasil diperbarui!');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ], [
            'new_password.min' => 'Password baru minimal 6 karakter.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->route('admin.settings.index')->withErrors(['current_password' => 'Password saat ini salah.']);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        ActivityLog::log("Admin mengubah password");

        return redirect()->route('admin.settings.index')->with('success', 'Password berhasil diubah!');
    }

    public function updateWebsite(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'footer_text' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'favicon' => 'nullable|file|mimes:ico,png|max:512',
        ]);

        $this->settingRepo->set('app_name', $request->app_name);
        $this->settingRepo->set('footer_text', $request->footer_text);

        if ($request->hasFile('logo')) {
            $logoName = 'logo_' . time() . '.' . $request->logo->extension();
            $request->logo->move(public_path('uploads'), $logoName);
            $this->settingRepo->set('logo_path', 'uploads/' . $logoName);
        }

        if ($request->hasFile('favicon')) {
            $faviconName = 'favicon_' . time() . '.' . $request->favicon->extension();
            $request->favicon->move(public_path('uploads'), $faviconName);
            $this->settingRepo->set('favicon_path', 'uploads/' . $faviconName);
        }

        ActivityLog::log("Memperbarui pengaturan website");

        return redirect()->route('admin.settings.index')->with('success', 'Pengaturan website berhasil diperbarui!');
    }
}
