<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\InspirationalQuoteResource;
use App\Models\InspirationalQuote;
use App\Support\AuditLogger;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class InspirationalQuoteController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorizePermission($request, 'inspirational_quotes.view_any');

        $quotes = InspirationalQuote::query()
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'تم جلب العبارات الملهمة',
            'data' => InspirationalQuoteResource::collection($quotes),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorizePermission($request, 'inspirational_quotes.create');

        $validated = $request->validate($this->rules());
        $quote = InspirationalQuote::query()->create([
            ...$validated,
            'created_by' => $request->user()?->id,
            'updated_by' => $request->user()?->id,
        ]);

        app(AuditLogger::class)->log(
            $request->user(),
            'create',
            $quote,
            [],
            $quote->only(['title', 'body', 'is_active', 'sort_order']),
        );

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء العبارة الملهمة',
            'data' => new InspirationalQuoteResource($quote),
        ], 201);
    }

    public function update(Request $request, InspirationalQuote $inspirationalQuote): JsonResponse
    {
        $this->authorizePermission($request, 'inspirational_quotes.update');

        $before = $inspirationalQuote->only(['title', 'body', 'is_active', 'sort_order']);
        $validated = $request->validate($this->rules(isUpdate: true));

        $inspirationalQuote->fill($validated);
        $inspirationalQuote->updated_by = $request->user()?->id;
        $inspirationalQuote->save();

        app(AuditLogger::class)->log(
            $request->user(),
            'update',
            $inspirationalQuote,
            $before,
            $inspirationalQuote->only(['title', 'body', 'is_active', 'sort_order']),
        );

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث العبارة الملهمة',
            'data' => new InspirationalQuoteResource($inspirationalQuote->refresh()),
        ]);
    }

    public function destroy(Request $request, InspirationalQuote $inspirationalQuote): JsonResponse
    {
        $this->authorizePermission($request, 'inspirational_quotes.delete');

        $before = $inspirationalQuote->only(['is_active']);
        $inspirationalQuote->update([
            'is_active' => false,
            'updated_by' => $request->user()?->id,
        ]);

        app(AuditLogger::class)->log(
            $request->user(),
            'delete',
            $inspirationalQuote,
            $before,
            $inspirationalQuote->only(['is_active']),
        );

        return response()->json([
            'success' => true,
            'message' => 'تم تعطيل العبارة الملهمة',
            'data' => new InspirationalQuoteResource($inspirationalQuote->refresh()),
        ]);
    }

    private function rules(bool $isUpdate = false): array
    {
        $required = $isUpdate ? 'sometimes' : 'required';

        return [
            'title' => [$required, 'string', 'max:255'],
            'body' => [$required, 'string'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ];
    }

    private function authorizePermission(Request $request, string $permission): void
    {
        $user = $request->user();

        if ($user?->role?->name === 'super_admin' || $user?->hasPermission($permission)) {
            return;
        }

        throw new AuthorizationException('لا تملك صلاحية إدارة العبارات الملهمة.');
    }
}
