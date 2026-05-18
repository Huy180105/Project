<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\RespondsWithApi;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use RespondsWithApi;

    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:30',
            'dob' => 'nullable|date',
            'gender' => 'nullable|string|in:male,female,other',
            'insurance_number' => 'nullable|string|max:100',
            'citizen_id' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:255',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:30',
            'medical_history' => 'nullable|string',
            'allergies' => 'nullable|string',
        ]);

        $user = DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
                'role' => User::ROLE_PATIENT,
            ]);

            $user->patientProfile()->create($this->profilePayload($validated));

            return $user->load('patientProfile');
        });

        return $this->success([
            'token' => $user->createToken('android')->plainTextToken,
            'user' => new UserResource($user),
        ], 'Đăng ký thành công.', 201);
    }

    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Thông tin đăng nhập không chính xác.'],
            ]);
        }

        if (! $user->isPatient()) {
            return $this->failure('Tài khoản này không thuộc nhóm bệnh nhân.', 403);
        }

        return $this->success([
            'token' => $user->createToken('android')->plainTextToken,
            'user' => new UserResource($user->load('patientProfile')),
        ], 'Đăng nhập thành công.');
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return $this->success(null, 'Đăng xuất thành công.');
    }

    public function profile(Request $request): JsonResponse
    {
        return $this->success(new UserResource($request->user()->load('patientProfile')), 'Tải hồ sơ thành công.');
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'phone' => 'required|string|max:30',
            'dob' => 'nullable|date',
            'gender' => 'nullable|string|in:male,female,other',
            'insurance_number' => 'nullable|string|max:100',
            'citizen_id' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:255',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:30',
            'medical_history' => 'nullable|string',
            'allergies' => 'nullable|string',
        ]);

        $request->user()->patientProfile()->updateOrCreate([], $this->profilePayload($validated));

        return $this->success(new UserResource($request->user()->load('patientProfile')), 'Cập nhật hồ sơ thành công.');
    }

    private function profilePayload(array $validated): array
    {
        return collect([
            'phone',
            'dob',
            'gender',
            'insurance_number',
            'citizen_id',
            'address',
            'emergency_contact_name',
            'emergency_contact_phone',
            'medical_history',
            'allergies',
        ])->mapWithKeys(fn (string $field) => [$field => $validated[$field] ?? null])->all();
    }
}
