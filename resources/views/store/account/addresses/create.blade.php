@extends('layouts.store')

@section('title', 'Agregar dirección — Le Cameleon')

@section('content')
<div class="container account-layout">
    @include('store.account.addresses.navigation')
    <div class="account-content">
        <h1 class="heading-2">Agregar dirección</h1>
        @include('store.account.addresses.form', ['formAction' => route('account.addresses.store'), 'formMethod' => 'POST'])
    </div>
</div>
@endsection
