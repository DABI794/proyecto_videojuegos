<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        // Ignorar el producto actual en la validación unique
        $productId = $this->route('product')->id;

        return [
            'name'        => "required|string|max:200|unique:products,name,{$productId}",
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string|max:5000',
            'price'       => 'required|numeric|min:0.01|max:99999.99',
            'stock'       => 'required|integer|min:0',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_active'   => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'        => 'El nombre del producto es obligatorio.',
            'name.unique'          => 'Ya existe un producto con ese nombre.',
            'category_id.required' => 'Seleccioná una categoría.',
            'category_id.exists'   => 'La categoría seleccionada no existe.',
            'price.required'       => 'El precio es obligatorio.',
            'price.numeric'        => 'El precio debe ser un número.',
            'price.min'            => 'El precio debe ser mayor a 0.',
            'stock.required'       => 'El stock es obligatorio.',
            'stock.integer'        => 'El stock debe ser un número entero.',
            'stock.min'            => 'El stock no puede ser negativo.',
            'image.image'          => 'El archivo debe ser una imagen.',
            'image.mimes'          => 'La imagen debe ser JPG, PNG o WebP.',
            'image.max'            => 'La imagen no puede superar los 2MB.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active'   => $this->boolean('is_active'),
            'is_featured' => $this->boolean('is_featured'),
        ]);
    }
}
