<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LicenceRequest extends FormRequest
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
            'monthCount' => 'required|integer|min:1|max:12',
            'player' => 'required|unique:licences,player',
        ];
    }
}
