<?php

namespace App\Http\Controllers\AdminApi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SettingsUpdateRequest;
use App\Services\SiteSettingsService;
use Illuminate\Http\JsonResponse;
use RuntimeException;

class SettingsController extends Controller
{
    public function __construct(
        private readonly SiteSettingsService $siteSettings,
    ) {
    }

    public function show(): JsonResponse
    {
        return response()->json([
            'item' => $this->siteSettings->payload(),
        ]);
    }

    public function update(SettingsUpdateRequest $request): JsonResponse
    {
        try {
            $settings = $this->siteSettings->update($request->validated());
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'message' => 'Pengaturan situs berhasil diperbarui.',
            'item' => $settings,
        ]);
    }
}
