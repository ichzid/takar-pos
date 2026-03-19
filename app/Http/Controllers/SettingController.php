<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingController extends Controller
{
    public function index()
    {
        return Inertia::render('Settings/Index', [
            'settings' => Setting::getAllAsArray(),
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'store_name'      => ['nullable', 'string', 'max:100'],
            'store_address'   => ['nullable', 'string', 'max:255'],
            'store_phone'     => ['nullable', 'string', 'max:30'],
            'receipt_message' => ['nullable', 'string', 'max:255'],
            'tax_enabled'     => ['required', 'boolean'],
            'tax_rate'        => ['required', 'numeric', 'min:0', 'max:100'],
            'theme'           => ['required', 'in:dark,light'],
        ]);

        foreach ($validated as $key => $value) {
            Setting::set($key, (string) ($value ?? ''));
        }

        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }
}
