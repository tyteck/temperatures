@extends('layouts.app')

@section('pageTitle', $pageTitle)

@section('content')

    <div class="max-w-screen-xl mx-auto py-6 md:py-12 px-4">
        <h2 class="text-3xl md:text-5xl text-white font-semibold">{{$pageTitle}}</h2>

        <livewire:charts />
    </div>
@endsection
