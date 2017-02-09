@extends('layouts.app')

@section('content')
    <h1>Eventi</h1>
    @foreach($events as $event)
        <p>Title: {{ $event->title }}</p>
        <img src="{{url('api/eventPhoto/'.$event->photo)}}"/>
    @endforeach

@endsection