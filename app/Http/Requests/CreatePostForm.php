<?php

namespace App\Http\Requests;

use App\Exceptions\ThrottleException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;

class CreatePostForm extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows('create', New \App\Reply);
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
    public function persist($thread) {
        return $thread->addReply([
            'body' => request('body'),
            'user_id' => auth()->id()
        ])->load('owner');
    }
}
