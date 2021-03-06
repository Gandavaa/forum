@extends('layouts.app')

@section('content')
<section class="hero">
    <div class="hero-body">
      <div class="container">
          <div class="column is-offset-2 is-8">
                <h1 class="title">
                    {{ $profileUser->name }}  
                    {{-- <small> {{ $profileUser->created_at->diffForHumans() }} </small> --}}
                </h1>
                <hr>
                @forelse ($activities as $date => $activity)
                    <h2 class="title is-5"> {{ $date }} </h2>
                    @foreach ($activity as $record)
                        @if (view()->exists("profiles.activities.{$record->type}"))
                           @include("profiles.activities.{$record->type}", ['activity' => $record ])                        
                        @endif
                    @endforeach

                @empty

                    <p>Хэрэглэгчид ямар нэг идэвхтэй үйлдэл хийгээгүй байна.</p>
                
                @endforelse

                {{-- {{ $threads->links() }} --}}
            </div>
            
        </div>
    </div>
    </div>
  </section>


@endsection