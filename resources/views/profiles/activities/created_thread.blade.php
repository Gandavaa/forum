@component('profiles.activities.activity')
    @slot('heading')
    {{ $profileUser->name }} published &nbsp;<a href="{{ $activity->subject->path()}}">
        {{ $activity->subject->title }}
    @endslot

    @slot('body')
        {{ $activity->subject->body }}
    @endslot
@endcomponent

