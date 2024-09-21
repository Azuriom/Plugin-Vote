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
     * The attributes represented by checkboxes.
     *
     * @var array<int, string>
     */
    protected array $checkboxes = [
        'has_verification',
        'is_enabled',
    ];

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:50'],
            'url' => ['required', 'string', 'url', 'max:150'],
            'rewards' => ['required', 'array'],
            'verification_key' => ['nullable', 'max:100'],
            'vote_delay' => ['required_with:reset_interval', 'nullable', 'integer', 'min:0'],
            'vote_reset_at' => ['required_without:reset_interval', 'nullable', 'date_format:H\\:i'],
            'has_verification' => ['filled', 'boolean'],
            'is_enabled' => ['filled', 'boolean'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->mergeCheckboxes();

        if ($this->filled('url')) {
            $this->merge([
                'url' => Str::replace('{player}', '(player_name)', $this->input('url')),
            ]);
        }

        if ($this->filled('reset_interval')) {
            $this->merge(['vote_reset_at' => null]);
        } else {
            $this->merge(['vote_delay' => 0]);
        }
    }

    /**
     * Get the validated data from the request.
     *
     * @param  array|int|string|null  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function validated($key = null, $default = null)
    {
        $validated = $this->validator->validated();

        if (($url = Arr::get($validated, 'url')) !== null) {
            $validated['url'] = Str::replace('(player_name)', '{player}', $url);
        }

        return data_get($validated, $key, $default);
    }
}
