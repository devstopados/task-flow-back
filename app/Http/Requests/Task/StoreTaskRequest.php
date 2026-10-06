<?php

namespace App\Http\Requests\Task;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $merge = [];

        if ($this->has('completion_date') && ! $this->has('end_date')) {
            $merge['end_date'] = $this->input('completion_date');
        }

        if ($this->has('worked_hours') && ! $this->has('hours')) {
            $merge['hours'] = $this->input('worked_hours');
        }

        if (! empty($merge)) {
            $this->merge($merge);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'code' => ['nullable', 'string', 'max:50'],
            'name' => ['required', 'string', 'max:255'],
            'start_date' => ['sometimes', 'nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'hours' => ['nullable', 'numeric', 'min:0', 'max:99999.99'],
            'branch' => ['nullable', 'string', 'max:255'],
            'link' => ['nullable', 'string', 'max:500'],
            'status_id' => ['required', 'integer', 'exists:status_tasks,id'],
            'subproject_id' => ['nullable', 'integer', 'exists:subprojects,id'],
        ];
    }
}
