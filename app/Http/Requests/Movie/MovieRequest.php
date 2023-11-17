<?php

namespace App\Http\Requests\Movie;

use App\Models\Movie;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class MovieRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        if(request()->method() == 'POST') {
            $rules = Movie::VALIDATION_RULES['post'];
        } elseif(request()->method() == 'PUT' || request()->method() == 'PATCH') {
            $rules = Movie::VALIDATION_RULES['put'];
        }

        return $rules;
    }
}
