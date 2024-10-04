<?php

namespace App\Http\Requests\Auth;

use App\Models\Users\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SetUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        /** @var User */
        $user = Auth::user();
        $model_id = $this->id;
        if ($model_id) {
            $user_model = User::findOrFail($model_id);
            return $user->can('update', $user_model);
        } else {
            return $user->can('create', User::class);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "id"            =>  "nullable|exists:users,id",
            "username"      =>  "required|unique:users,username," . $this->id,
            "first_name"    =>  "required",
            "last_name"     =>  "required",
            "type"          =>  "required|in:" . implode(',', User::TYPES),
            "password"      =>  "required_without:id",
            "email"         =>  "nullable|email",
            "phone"         =>  "nullable",
        ];
    }
}
