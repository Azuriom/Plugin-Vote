<?php

namespace Azuriom\Plugin\Vote\Requests;

use Azuriom\Http\Requests\Traits\ConvertCheckbox;
use Illuminate\Foundation\Http\FormRequest;

class RewardRequest extends FormRequest
{
    use ConvertCheckbox;

    /**
     * The attributes represented by checkboxes.
     *
     * @var array<int, string>
     */
    protected array $checkboxes = [
        'need_online', 'single_server', 'is_enabled',
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
            'image' => ['nullable', 'image:allow_svg'],
            'servers.*' => ['required', 'exists:servers,id'],
            'chances' => ['required', 'numeric', 'between:0,100'],
            'money' => ['nullable', 'numeric', 'min:0'],
            'need_online' => ['filled', 'boolean'],
            'single_server' => ['filled', 'boolean'],
            'commands' => ['sometimes', 'nullable', 'array'],
            'monthly_rewards' => ['sometimes', 'nullable', 'array'],
            'is_enabled' => ['filled', 'boolean'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->mergeCheckboxes();

        if (! $this->filled('money')) {
            $this->merge(['money' => 0]);
        }

        $rewards = array_filter($this->input('monthly_rewards', []));

        $this->merge([
            'commands' => array_filter($this->input('commands', [])),
            'monthly_rewards' => array_map(fn ($val) => (int) $val, $rewards),
        ]);
    }
}
