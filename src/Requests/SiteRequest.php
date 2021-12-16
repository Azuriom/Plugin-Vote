<?php

namespace Azuriom\Plugin\Vote\Requests;

use Azuriom\Http\Requests\Traits\ConvertCheckbox;
use Illuminate\Foundation\Http\FormRequest;

class SiteRequest extends FormRequest
{
    use ConvertCheckbox;

    /**
     * The checkboxes attributes.
     *
     * @var array
     */
    protected $checkboxes = [
        'has_verification',
        'is_enabled',
    ];

    public function validationData()
    {
        $data = parent::validationData();
        $data['url'] = str_replace('{player}', 'VAR_PLAYER', $data['url']);

        return $data;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:50'],
            'url' => ['required', 'string', 'url', 'max:150'],
            'verification_key' => ['nullable', 'max:100'],
            'vote_delay' => ['required', 'integer', 'min:0'],
            'has_verification' => ['filled', 'boolean'],
            'is_enabled' => ['filled', 'boolean'],
        ];
    }
}
