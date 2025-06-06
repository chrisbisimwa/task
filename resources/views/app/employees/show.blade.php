@extends('layouts.app')
<style>
    body {
        background-color: #f1f3f5;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .employee-header {
        background: linear-gradient(135deg, #007bff, #6610f2);
        color: white;
        padding: 2rem;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .employee-header h1 {
        margin: 0;
        font-size: 2rem;
    }

    .task-table th,
    .task-table td {
        vertical-align: middle;
    }

    .task-table .progress {
        height: 8px;
    }

    .filter-bar {
        background-color: #fff;
        border-radius: 8px;
        padding: 1rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.9rem;
    }

    .status-pending {
        background-color: #ffc107;
        color: #fff;
    }

    .status-in_progress {
        background-color: #17a2b8;
        color: #fff;
    }

    .status-done {
        background-color: #28a745;
        color: #fff;
    }

    .action-btn {
        transition: transform 0.2s;
    }

    .action-btn:hover {
        transform: scale(1.1);
    }

    @media (max-width: 768px) {
        .employee-header {
            padding: 1rem;
        }

        .employee-header h1 {
            font-size: 1.5rem;
        }
    }
</style>
@section('content')
    @livewire('employees.show', ['employeeId' => $employee->id])

    
    {{-- <div class="container">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">
                <a href="{{ route('employees.index') }}" class="mr-4"
                    ><i class="icon ion-md-arrow-back"></i
                ></a>
                @lang('crud.employees.show_title')
            </h4>

           

            <div class="mt-4">
                <div class="mb-4">
                    <h5>@lang('crud.employees.inputs.name')</h5>
                    <span>{{ $employee->name ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.employees.inputs.phone')</h5>
                    <span>{{ $employee->phone ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.employees.inputs.email')</h5>
                    <span>{{ $employee->email ?? '-' }}</span>
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('employees.index') }}" class="btn btn-light">
                    <i class="icon ion-md-return-left"></i>
                    @lang('crud.common.back')
                </a>

                @can('create', App\Models\Employee::class)
                <a href="{{ route('employees.create') }}" class="btn btn-light">
                    <i class="icon ion-md-add"></i> @lang('crud.common.create')
                </a>
                @endcan
            </div>
        </div>
    </div>
</div> --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endsection
