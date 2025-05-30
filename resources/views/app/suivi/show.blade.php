<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Suivi des Tâches - {{ $employee->name }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
        }
        .task-card {
            border-radius: 12px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        .btn-status {
            min-width: 100px;
        }
        @media (max-width: 576px) {
            h2 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
<div class="container py-4">
    <div class="text-center mb-4">
        <h2>Bonjour {{ $employee->name }}</h2>
        <p class="text-muted">Voici vos tâches à suivre pour la semaine {{ $currentWeek }}</p>
    </div>

    @if($tasks->isEmpty())
        <div class="alert alert-success text-center">
            🎉 Vous avez terminé toutes vos tâches pour cette semaine !
        </div>
    @else
        <form method="POST" action="{{ route('suivi.submit', $access->token) }}">
            @csrf
            @foreach($tasks as $task)
                <div class="card mb-3 task-card">
                    <div class="card-body">
                        <h5 class="card-title">{{ $task->name }}</h5>
                        <p class="card-text text-muted">{{ $task->description }}</p>
                        <div class="mb-2">Statut actuel : <strong>{{ ucfirst($task->status) }}</strong></div>

                        <div class="d-flex flex-wrap gap-2">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="statuses[{{ $task->id }}]" value="in_progress" {{ $task->status == 'in_progress' ? 'checked' : '' }}>
                                <label class="form-check-label">En cours</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="statuses[{{ $task->id }}]" value="done" {{ $task->status == 'done' ? 'checked' : '' }}>
                                <label class="form-check-label">Fait</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="statuses[{{ $task->id }}]" value="pending" {{ $task->status == 'pending' ? 'checked' : '' }}>
                                <label class="form-check-label">En attente</label>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="text-center">
                <button type="submit" class="btn btn-primary px-4 py-2">✅ Mettre à jour mes tâches</button>
            </div>
        </form>
    @endif
</div>

<!-- Bootstrap JS (optionnel) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
