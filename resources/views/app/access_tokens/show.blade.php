@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">
                <a href="{{ route('access-tokens.index') }}" class="mr-4"
                    ><i class="icon ion-md-arrow-back"></i
                ></a>
                @lang('crud.access_tokens.show_title')
            </h4>

            <div class="mt-4">
                <div class="mb-4">
                    <h5>@lang('crud.access_tokens.inputs.employee_id')</h5>
                    <span
                        >{{ optional($accessToken->employee)->name ?? '-'
                        }}</span
                    >
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.access_tokens.inputs.token')</h5>
                    <span>{{ $accessToken->token ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.access_tokens.inputs.expires_at')</h5>
                    <span>{{ $accessToken->expires_at ?? '-' }}</span>
                </div>
            </div>

            <div class="mt-4">
                <a
                    href="{{ route('access-tokens.index') }}"
                    class="btn btn-light"
                >
                    <i class="icon ion-md-return-left"></i>
                    @lang('crud.common.back')
                </a>

                @can('create', App\Models\AccessToken::class)
                <a
                    href="{{ route('access-tokens.create') }}"
                    class="btn btn-light"
                >
                    <i class="icon ion-md-add"></i> @lang('crud.common.create')
                </a>
                @endcan
            </div>
        </div>
    </div>
</div>
@endsection
