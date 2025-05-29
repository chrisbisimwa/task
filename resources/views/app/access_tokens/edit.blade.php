@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">
                <a href="{{ route('access-tokens.index') }}" class="mr-4"
                    ><i class="icon ion-md-arrow-back"></i
                ></a>
                @lang('crud.access_tokens.edit_title')
            </h4>

            <x-form
                method="PUT"
                action="{{ route('access-tokens.update', $accessToken) }}"
                class="mt-4"
            >
                @include('app.access_tokens.form-inputs')

                <div class="mt-4">
                    <a
                        href="{{ route('access-tokens.index') }}"
                        class="btn btn-light"
                    >
                        <i class="icon ion-md-return-left text-primary"></i>
                        @lang('crud.common.back')
                    </a>

                    <a
                        href="{{ route('access-tokens.create') }}"
                        class="btn btn-light"
                    >
                        <i class="icon ion-md-add text-primary"></i>
                        @lang('crud.common.create')
                    </a>

                    <button type="submit" class="btn btn-primary float-right">
                        <i class="icon ion-md-save"></i>
                        @lang('crud.common.update')
                    </button>
                </div>
            </x-form>
        </div>
    </div>
</div>
@endsection
