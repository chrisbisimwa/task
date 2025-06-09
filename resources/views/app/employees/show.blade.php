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
    

    

    <div class="container py-5">
        <!-- En-tête de l'employé -->
        <div class="employee-header mb-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1><i class="fas fa-user me-2"></i>{{ $employee->name }}</h1>
                    <p class="mb-0"><strong>Email :</strong> {{ $employee->email }}</p>
                    <p class="mb-0"><strong>Département :</strong> {{ $employee->department ?? 'Non spécifié' }}</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="{{ route('employees.index') }}" class="btn btn-light">
                        <i class="fas fa-arrow-left"></i> Retour à la liste
                    </a>
                </div>
            </div>
        </div>

        <!-- Barre de filtres -->
        <div class="filter-bar mb-4">
            <form method="GET" action="{{ route('employees.show', $employee->id) }}">
                <div class="row g-3 align-items-center">
                    <div class="col-md-3">
                        <label for="week" class="form-label">Semaine</label>
                        <select name="week" id="week" class="form-control">
                            <option value="all">Toutes les semaines</option>
                            @foreach ($weeks as $week)
                                <option value="{{ $week['semaine'] }}"
                                    {{ request('semaine') == $week['semaine']? 'selected' : 'all' }}>
                                    Du {{ $week['start_date' ]}} au  {{ $week['end_date' ]}} ({{ $week['tasks'] }} tâches)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label">Statut</label>
                        <select name="status" id="status" class="form-control">
                            <option value="all">Tous les statuts</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : 'all' }}>En attente</option>
                            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>En cours</option>
                            <option value="done" {{ request('status') == 'done' ? 'selected' : '' }}>Fait</option>
                        </select>
                    </div>
                    <div class="col-md-3 align-self-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter"></i> Filtrer
                        </button>
                    </div>
                    <div class="col-md-3 align-self-end">
                        <a href="{{ route('tasks.create', $employee->id) }}" class="btn btn-success w-100">
                            <i class="fas fa-plus"></i> Ajouter une tâche
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Tableau des tâches -->
        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="card-title mb-4">Tâches de {{ $employee->name }}</h4>
                @if ($tasks->isEmpty())
                    <p class="text-muted text-center">Aucune tâche trouvée pour cet employé.</p>
                @else
                    <div class="table-responsive">
                        <table class="table task-table table-hover">
                            <thead>
                                <tr>
                                    <th>Tâche</th>
                                    <th>Description</th>
                                    <th>Statut</th>
                                    <th>Progression</th>
                                    <th>Semaine</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tasks as $task)
                                    <tr>
                                        <td>{{ $task->name }}</td>
                                        <td>{{ Str::limit($task->description, 50) }}</td>
                                        <td>
                                            <span class="status-badge status-{{ $task->status }}">
                                                {{ $task->status == 'pending' ? 'En attente' : ($task->status == 'in_progress' ? 'En cours' : 'Fait') }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($task->status == 'in_progress')
                                                <div class="progress">
                                                    <div class="progress-bar" role="progressbar"
                                                        style="width: {{ $task->progress }}%;"
                                                        aria-valuenow="{{ $task->progress }}" aria-valuemin="0"
                                                        aria-valuemax="100">{{ $task->progress }}%</div>
                                                </div>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>{{ $task->due_week }}</td>
                                        <td>
                                            <a href="{{ route('tasks.edit', $task->id) }}"
                                                class="btn btn-sm btn-warning action-btn" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('tasks.destroy', $task->id) }}"
                                                method="POST" style="display:inline;"
                                                onsubmit="return confirm('Voulez-vous vraiment supprimer cette tâche ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger action-btn"
                                                    title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

      {{--   <!-- Pagination -->
        @if ($tasks->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $tasks->links() }}
            </div>
        @endif --}}
    </div>




    
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
