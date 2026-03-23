@extends('layouts.app')
@section('title', 'Editar: ' . $product->name . ' — Admin')

@section('content')
@include('admin.products.create')
@endsection
