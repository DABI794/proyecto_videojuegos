@extends('layouts.app')
@section('title', isset($product) ? 'Editar Producto — Admin' : 'Nuevo Producto — Admin')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('admin.productos.index') }}" class="p-2 text-[#64748b] hover:text-[#f1f5f9] bg-[#1e293b] border border-[#334155] rounded-xl no-underline transition-colors">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-bold text-[#f1f5f9]">
            {{ isset($product) ? 'Editar: ' . $product->name : 'Nuevo producto' }}
        </h1>
    </div>

    <form
        action="{{ isset($product) ? route('admin.productos.update', $product) : route('admin.productos.store') }}"
        method="POST"
        enctype="multipart/form-data"
        class="space-y-6"
    >
        @csrf
        @if(isset($product)) @method('PUT') @endif

        {{-- Errores globales --}}
        @if($errors->any())
            <div class="bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-3 rounded-xl text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-[#1e293b] border border-[#334155] rounded-2xl p-6 space-y-5">

            {{-- Nombre --}}
            <div>
                <label class="block text-sm font-medium text-[#94a3b8] mb-1.5">Nombre *</label>
                <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}"
                    class="w-full bg-[#0f172a] border {{ $errors->has('name') ? 'border-red-500/50' : 'border-[#334155]' }} text-[#f1f5f9] text-sm rounded-xl px-4 py-3 focus:outline-none focus:border-[#6366f1] placeholder-[#64748b] transition-colors"
                    placeholder="Nombre del videojuego">
                @error('name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Categoría --}}
            <div>
                <label class="block text-sm font-medium text-[#94a3b8] mb-1.5">Categoría *</label>
                <select name="category_id"
                    class="w-full bg-[#0f172a] border {{ $errors->has('category_id') ? 'border-red-500/50' : 'border-[#334155]' }} text-[#94a3b8] text-sm rounded-xl px-4 py-3 focus:outline-none focus:border-[#6366f1] transition-colors">
                    <option value="">Seleccioná una categoría</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}"
                            {{ old('category_id', $product->category_id ?? '') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Descripción --}}
            <div>
                <label class="block text-sm font-medium text-[#94a3b8] mb-1.5">Descripción</label>
                <textarea name="description" rows="4"
                    class="w-full bg-[#0f172a] border border-[#334155] text-[#f1f5f9] text-sm rounded-xl px-4 py-3 focus:outline-none focus:border-[#6366f1] placeholder-[#64748b] transition-colors resize-none"
                    placeholder="Descripción del producto...">{{ old('description', $product->description ?? '') }}</textarea>
            </div>

            {{-- Precio + Stock --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-[#94a3b8] mb-1.5">Precio (Bs.) *</label>
                    <input type="number" name="price" step="0.01" min="0"
                        value="{{ old('price', $product->price ?? '') }}"
                        class="w-full bg-[#0f172a] border {{ $errors->has('price') ? 'border-red-500/50' : 'border-[#334155]' }} text-[#f1f5f9] text-sm rounded-xl px-4 py-3 focus:outline-none focus:border-[#6366f1] transition-colors"
                        placeholder="0.00">
                    @error('price') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#94a3b8] mb-1.5">Stock *</label>
                    <input type="number" name="stock" min="0"
                        value="{{ old('stock', $product->stock ?? 0) }}"
                        class="w-full bg-[#0f172a] border {{ $errors->has('stock') ? 'border-red-500/50' : 'border-[#334155]' }} text-[#f1f5f9] text-sm rounded-xl px-4 py-3 focus:outline-none focus:border-[#6366f1] transition-colors"
                        placeholder="0">
                    @error('stock') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Imagen --}}
            <div>
                <label class="block text-sm font-medium text-[#94a3b8] mb-1.5">Imagen del producto</label>
                @if(isset($product) && $product->image_path)
                    <div class="mb-3 flex items-center gap-3">
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-16 h-16 rounded-xl object-cover bg-[#0f172a]">
                        <span class="text-xs text-[#64748b]">Imagen actual. Subí una nueva para reemplazarla.</span>
                    </div>
                @endif
                <input type="file" name="image" accept="image/jpg,image/jpeg,image/png,image/webp"
                    class="w-full bg-[#0f172a] border border-[#334155] text-[#94a3b8] text-sm rounded-xl px-4 py-3 focus:outline-none focus:border-[#6366f1] transition-colors file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:bg-[#334155] file:text-[#94a3b8] file:text-xs file:cursor-pointer hover:file:bg-[#475569]">
                <p class="text-xs text-[#64748b] mt-1">JPG, PNG o WebP · Máx. 2MB</p>
                @error('image') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Switches --}}
            <div class="flex items-center gap-8 pt-2">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1"
                        {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}
                        class="w-4 h-4 accent-[#6366f1]">
                    <span class="text-sm text-[#94a3b8]">Producto activo</span>
                </label>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="hidden" name="is_featured" value="0">
                    <input type="checkbox" name="is_featured" value="1"
                        {{ old('is_featured', $product->is_featured ?? false) ? 'checked' : '' }}
                        class="w-4 h-4 accent-[#6366f1]">
                    <span class="text-sm text-[#94a3b8]">Destacado en home</span>
                </label>
            </div>
        </div>

        {{-- Botones --}}
        <div class="flex items-center gap-3">
            <button type="submit"
                class="flex items-center gap-2 bg-[#6366f1] hover:bg-[#4f46e5] text-white font-semibold px-6 py-3 rounded-xl transition-all hover:-translate-y-0.5 border-0">
                <i class="bi bi-check-lg"></i>
                {{ isset($product) ? 'Guardar cambios' : 'Crear producto' }}
            </button>
            <a href="{{ route('admin.productos.index') }}"
               class="bg-[#1e293b] border border-[#334155] text-[#94a3b8] hover:text-[#f1f5f9] font-medium px-6 py-3 rounded-xl no-underline transition-colors">
                Cancelar
            </a>
        </div>
    </form>
</div>
@endsection
