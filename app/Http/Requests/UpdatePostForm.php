<?php

namespace App\Http\Requests;

use App\Exceptions\ThrottleException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePostForm extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // return Gate::allows('update',auth()->user());
        return true;
    }

    // Overrides default failedAuthorization Exception
    protected function failedAuthorization() {
        throw new ThrottleException('You are replying too frequently. Take a break.', 429);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'body' => 'required|min:5|spamfree',
        ];
    }
}
