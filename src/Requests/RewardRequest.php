<?php

namespace Azuriom\Plugin\Vote\Requests;

use Azuriom\Http\Requests\Traits\ConvertCheckbox;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class RewardRequest extends FormRequest
{
    use ConvertCheckbox;

    /**
     * The checkboxes attributes.
     *
     * @var array
     */
    protected $checkboxes = [
        'need_online', 'is_enabled',
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
            'image' => ['nullable', 'image'],
            'servers.*' => ['required', 'exists:servers,id'],
            'chances' => ['required', 'numeric', 'between:0,100'],
            'money' => ['nullable', 'numeric', 'min:0'],
            'need_online' => ['filled', 'boolean'],
            'commands' => ['sometimes', 'nullable', 'array'],
            'is_enabled' => ['filled', 'boolean'],
        ];
    }

    /**
     * Get the validated data from the request.
     *
     * @param  mixed|null  $key
     * @param  mixed|null  $default
     * @return array
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated();

        $validated['commands'] = array_filter(Arr::get($validated, 'commands', []));

        if (Arr::get($validated, 'money') === null) {
            $validated['money'] = 0;
        }

        return $validated;
    }
}
