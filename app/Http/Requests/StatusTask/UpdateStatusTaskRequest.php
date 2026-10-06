<?php

namespace App\Http\Requests\StatusTask;

use App\Models\StatusTask;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStatusTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $statusTaskId = $this->route('status_task') instanceof StatusTask
            ? $this->route('status_task')->id
            : $this->route('status_task');

        return [
            'name' => ['sometimes', 'required', 'string', 'max:150'],
            'slug' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('status_tasks', 'slug')->ignore($statusTaskId),
            ],
            'active' => ['sometimes', 'boolean'],
        ];
    }
}
