<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

final class PasswordResetController extends Controller
{
    public function forgot(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:150'],
        ]);

        $status = Password::sendResetLink([
            'email' => $validated['email'],
        ]);

        if ($status === Password::RESET_THROTTLED) {
            return response()->json([
                'success' => false,
                'message' => 'تم إرسال رابط استعادة مؤخرًا. يرجى المحاولة بعد قليل.',
                'data' => null,
            ], 429);
        }

        return response()->json([
            'success' => true,
            'message' => 'إذا كان البريد الإلكتروني مسجلًا لدينا، فسيصلك رابط إعادة تعيين كلمة المرور.',
            'data' => null,
        ]);
    }

    public function reset(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:150'],
            'token' => ['required', 'string'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
        ]);

        $status = Password::reset(
            $validated,
            static function (User $user, string $password): void {
                $user->forceFill([
                    'password_hash' => Hash::make($password),
                    'remember_token' => Str::random(60),
                    'must_change_password' => false,
                ])->save();

                $user->tokens()->delete();

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return response()->json([
                'success' => false,
                'message' => 'رابط الاستعادة غير صالح أو منتهي الصلاحية.',
                'data' => null,
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث كلمة المرور بنجاح. يمكنك تسجيل الدخول الآن.',
            'data' => null,
        ]);
    }
}
