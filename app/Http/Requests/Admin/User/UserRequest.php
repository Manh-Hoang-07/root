<?php

namespace App\Http\Requests\Admin\User;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->route('user');
        $userId = null;
        
        if ($user) {
            $userId = is_object($user) ? $user->id : $user;
        }

        $rules = [
            'username' => 'required|string|max:50|unique:users,username,' . $userId,
            'email' => 'required|email|max:255|unique:users,email,' . $userId,
            'phone' => 'nullable|string|max:20|unique:users,phone,' . $userId,
            'password' => $this->isMethod('post') ? 'required|string|min:8|confirmed' : 'nullable|string|min:8|confirmed',
            'status' => 'required|string|in:active,inactive,banned',
            'name' => 'nullable|string|max:255',
            'gender' => 'nullable|string|in:male,female,other',
            'birthday' => 'nullable|date',
            'address' => 'nullable|string|max:255',
            'image' => 'nullable|string|max:255',
            'about' => 'nullable|string|max:500',
        ];

        return $rules;
    }

    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);
        
        // Convert string "null" to actual null for nullable fields
        $nullableFields = ['phone', 'name', 'gender', 'birthday', 'address', 'about', 'image'];
        foreach ($nullableFields as $field) {
            if (isset($validated[$field]) && $validated[$field] === 'null') {
                $validated[$field] = null;
            }
        }
        
        return $validated;
    }

    public function messages(): array
    {
        return [
            'username.required' => 'Tên đăng nhập không được để trống.',
            'username.max' => 'Tên đăng nhập không được vượt quá :max ký tự.',
            'username.unique' => 'Tên đăng nhập đã tồn tại.',
            'email.required' => 'Email không được để trống.',
            'email.email' => 'Email không đúng định dạng.',
            'email.max' => 'Email không được vượt quá :max ký tự.',
            'email.unique' => 'Email đã tồn tại.',
            'phone.max' => 'Số điện thoại không được vượt quá :max ký tự.',
            'phone.unique' => 'Số điện thoại đã tồn tại.',
            'status.required' => 'Trạng thái không được để trống.',
            'status.in' => 'Trạng thái không hợp lệ.',
            'roles.array' => 'Danh sách vai trò phải là mảng.',
            'roles.*.exists' => 'Vai trò không hợp lệ.',
            'password.required' => 'Mật khẩu không được để trống.',
            'password.min' => 'Mật khẩu phải có ít nhất :min ký tự.',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
        ];
    }
}
