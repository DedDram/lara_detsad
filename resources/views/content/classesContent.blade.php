@extends('layouts')
@section('styles')
    @parent
    <link href="{{ asset('/css/cattable.css') }}" rel="stylesheet">
@endsection
@section('content')
    @if(!empty($article))
        {!! $article->introtext !!}
    @endif
@endsection

