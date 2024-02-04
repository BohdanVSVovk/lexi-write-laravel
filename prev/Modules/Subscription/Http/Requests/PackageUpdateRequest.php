<?php

namespace Modules\Subscription\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Subscription\Rules\DecimalValidator;

class PackageUpdateRequest extends FormRequest
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
            'user_id' => 'required|exists:users,id',
            'name' => 'required|max:100',
            'code' => 'nullable|min:3|max:45|unique:packages,code,' . $this->id,
            'short_description' => 'nullable|max:191',
            'sale_price' => ['nullable', new DecimalValidator],
            'discount_price' => ['nullable', 'lte:sale_price', new DecimalValidator],
            'billing_cycle' => 'required|in:days,weekly,monthly,yearly',
            'sort_order' => 'nullable|numeric',
            'trial_day' => 'nullable|numeric',
            'usage_limit' => 'nullable|numeric',
            'renewable' => 'required|boolean',
            'status' => 'required|in:Active,Pending,Inactive,Expired,Cancel',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'sale_price' => validateNumbers($this->sale_price),
            'discount_price' => validateNumbers($this->discount_price),
        ]);
    }
}
