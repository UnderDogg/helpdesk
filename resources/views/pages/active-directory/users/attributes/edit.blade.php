@extends('layouts.master')

@section('title')

    Modifying {{ ucfirst($attribute) }} on {{ $user->getName() }}

@endsection

@section('content')

    {!! $form !!}

@endsection
