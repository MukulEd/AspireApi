<?php

namespace App\Http\Requests;
use App\Models\User;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required|email',
            'password' => 'required|string',
        ];
    }

    // public function errorResponse(array $errors)
    // {
    //     return response(['errors'=>$errors,'message'=>reset($errors)[0]], 422);
    // }
    
}
