<?php

namespace Azuriom\Plugin\Vote\Requests;

use Azuriom\Http\Requests\Traits\ConvertCheckbox;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

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
            'rewards' => ['required', 'array'],
            'verification_key' => ['nullable', 'max:100'],
            'vote_delay' => ['required', 'integer', 'min:0'],
            'has_verification' => ['filled', 'boolean'],
            'is_enabled' => ['filled', 'boolean'],
        ];
    }

    /**
     * Get data to be validated from the request.
     *
     * @return array
     */
    public function validationData()
    {
        $url = $this->input('url');

        if ($url === null) {
            return $this->all();
        }

        return array_merge($this->all(), [
            'url' => Str::replace('{player}', '(player_name)', $url),
        ]);
    }

    /**
     * Get the validated data from the request.
     *
     * @param  mixed|null  $key
     * @param  mixed|null  $default
     * @return array
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validated($key = null, $default = null)
    {
        $validated = $this->validator->validated();
        $url = Arr::get($validated, 'url');

        return array_merge($validated, [
            'url' => Str::replace('(player_name)', '{player}', $url),
        ]);
    }
}
