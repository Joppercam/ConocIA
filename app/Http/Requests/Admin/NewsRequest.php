<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NewsRequest extends FormRequest
{
    /**
     * Determinar si el usuario está autorizado a hacer esta solicitud
     */
    public function authorize(): bool
    {
        // Aquí puedes añadir lógica de autorización más compleja si es necesario
        return auth()->check() && auth()->user()->isAdmin();
    }

    /**
     * Reglas de validación
     */
    public function rules(): array
    {
        $newsId = $this->route('news') ? $this->route('news')->id : null;

        return [
            'title' => [
                'required', 
                'string', 
                'max:255',
                Rule::unique('news', 'title')->ignore($newsId)
            ],
            'slug' => [
                'nullable', 
                'string', 
                'max:255',
                Rule::unique('news', 'slug')->ignore($newsId)
            ],
            'excerpt' => 'required|string|max:500',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'image' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif,webp',
                'max:5120' // 5MB máximo
            ],
            'remove_image' => 'nullable|boolean',
            'meta_description' => 'nullable|string|max:255',
            'meta_keywords' => 'nullable|string|max:255',
            'is_featured' => 'nullable|boolean',
            'is_published' => 'nullable|boolean',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id'
        ];
    }

    /**
     * Mensajes de error personalizados
     */
    public function messages(): array
    {
        return [
            'title.required' => 'El título es obligatorio',
            'title.unique' => 'Ya existe una noticia con este título',
            'excerpt.required' => 'El extracto es obligatorio',
            'content.required' => 'El contenido es obligatorio',
            'category_id.required' => 'Debe seleccionar una categoría',
            'category_id.exists' => 'La categoría seleccionada no es válida',
            'image.image' => 'El archivo debe ser una imagen',
            'image.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg, gif o webp',
            'image.max' => 'La imagen no puede ser mayor de 5MB'
        ];
    }

    /**
     * Preparar los datos para validación
     */
    protected function prepareForValidation(): void
    {
        // Convertir checkboxes a valores booleanos
        $this->merge([
            'is_featured' => $this->boolean('is_featured'),
            'is_published' => $this->boolean('is_published'),
            'remove_image' => $this->boolean('remove_image')
        ]);
    }
}