@extends('layouts.store')

@section('title', 'Editar dirección — Le Cameleon')

@section('content')
<div class="container account-layout">
    @include('store.account.addresses.navigation')
    <div class="account-content">
        <h1 class="heading-2">Editar dirección</h1>
        @include('store.account.addresses.form', ['formAction' => route('account.addresses.update', $address), 'formMethod' => 'PUT'])
    </div>
</div>
@endsection
