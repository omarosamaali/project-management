@extends('layouts.app')

@section('title', 'الملف الشخصي')

@section('content')

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        @if(!empty($trainerJourney))
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            @include('academy.partials.trainer-journey', [
                'journeyStep' => $trainerJourney['step'],
                'completedSteps' => $trainerJourney['completed'],
                'allDone' => $trainerJourney['all_done'],
                'journeyHint' => $trainerJourney['hint'],
            ])
            <script>
                (function () {
                    try {
                        localStorage.setItem(@json(\App\Support\TrainerJourney::STORAGE_KEY), JSON.stringify({
                            step: {{ (int) $trainerJourney['step'] }},
                            completed: {{ (int) $trainerJourney['completed'] }},
                            allDone: {{ $trainerJourney['all_done'] ? 'true' : 'false' }},
                            source: 'profile',
                            at: Date.now()
                        }));
                    } catch (e) {}
                })();
            </script>
        </div>
        @endif

        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
                @include('profile.partials.employee-work-profile', ['user' => $user, 'employeeStats' => $employeeStats ?? null])
            </div>
        </div>

        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</div>

@endsection