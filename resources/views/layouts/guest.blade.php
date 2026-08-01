@extends('layouts.user')

@section('title', __('messages.academy'))

@section('content')
@if(\App\Support\AuthUi::isAcademy())
    <x-auth.academy-shell :narrow="true">
        {{ $slot }}
    </x-auth.academy-shell>
@else
    <div class="my-10 mx-auto max-w-lg w-[96%] bg-white rounded-xl shadow-2xl overflow-hidden p-8 md:p-10">
        {{ $slot }}
    </div>
@endif
@endsection
