<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Suivi des Tâches - {{ $employee->name }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/favicon/apple-touch-icon.png') }}">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Sortable.js -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    <style>
        body {
            background-color: #f1f3f5;
        }

        .board {
            display: flex;
            gap: 1rem;
            overflow-x: auto;
            padding: 1rem 0;
        }

        .column {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            padding: 1rem;
            min-width: 280px;
            flex: 1;
        }

        .column h4 {
            text-align: center;
            margin-bottom: 1rem;
        }

        .task-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 0.75rem;
            margin-bottom: 0.5rem;
            background-color: #f8f9fa;
            cursor: grab;
        }

        .task-card:hover {
            background-color: #e9ecef;
        }

        .progress-wrapper {
            margin-bottom: 1rem;
        }
    </style>
</head>

<body>
    <div class="container py-4">
        <div class="text-center mb-4">
            <h2>Bonjour {{ $employee->name }}</h2>
            <p class="text-muted">Tâches de la semaine {{ $currentWeek }} ({{$weekStart}} - {{$weekEnd}})</p>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form id="task-form" method="POST" action="{{ route('suivi.submit', $access->token) }}">
            @csrf
            <div class="board">
                <div class="column">
                    <h4>🔄 En attente</h4>
                    <div id="pending" class="task-list">
                        @foreach ($tasks->where('status', 'pending') as $task)
                            <div class="task-card" data-id="{{ $task->id }}">
                                <strong>{{ $task->name }}</strong>
                                <p class="mb-0 small text-muted">{{ $task->description }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="column">
                    <h4>⏳ En cours</h4>
                    <div id="in_progress" class="task-list">
                        @foreach ($tasks->where('status', 'in_progress') as $task)
                            <div class="task-card" data-id="{{ $task->id }}">
                                <strong>{{ $task->name }}</strong>
                                <p class="mb-0 small text-muted">{{ $task->description }}</p>

                                <div class="progress-wrapper" data-task-id="{{ $task->id }}">
                                    <label class="form-label small">Progression :
                                        <input type="number" min="0" max="100" step="5"
                                            value="{{ $task->progress }}"
                                            class="form-control form-control-sm d-inline-block w-auto"
                                            onchange="submitProgress({{ $task->id }}, this.value)"
                                            oninput="updateProgressValue({{ $task->id }}, this.value)">
                                        %
                                    </label>
                                    <div class="progress mt-1">
                                        <div id="progress-bar-{{ $task->id }}" class="progress-bar"
                                            role="progressbar" style="width: {{ $task->progress }}%;"
                                            aria-valuenow="{{ $task->progress }}" aria-valuemin="0"
                                            aria-valuemax="100">{{ $task->progress }}%</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="column">
                    <h4>✅ Fait</h4>
                    <div id="done" class="task-list">
                        @foreach ($tasks->where('status', 'done') as $task)
                            <div class="task-card" data-id="{{ $task->id }}">
                                <strong>{{ $task->name }}</strong>
                                <p class="mb-0 small text-muted">{{ $task->description }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <input type="hidden" name="statuses" id="statuses">

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary">✅ Mettre à jour mes tâches</button>
            </div>
        </form>
    </div>

    <script>
        const lists = ['pending', 'in_progress', 'done'];

        lists.forEach(id => {
            new Sortable(document.getElementById(id), {
                group: 'shared',
                animation: 150
            });
        });

        document.getElementById('task-form').addEventListener('submit', function(e) {
            const statusMap = {};
            lists.forEach(status => {
                const tasks = document.getElementById(status).querySelectorAll('.task-card');
                tasks.forEach(task => {
                    statusMap[task.dataset.id] = status;
                });
            });
            document.getElementById('statuses').value = JSON.stringify(statusMap);
        });

        function updateProgressValue(taskId, value) {
            const progress = Math.min(Math.max(parseInt(value), 0), 100);
            const bar = document.getElementById('progress-bar-' + taskId);
            bar.style.width = progress + '%';
            bar.innerText = progress + '%';
            bar.setAttribute('aria-valuenow', progress);
        }

        function submitProgress(taskId, value) {
            const progress = Math.min(Math.max(parseInt(value), 0), 100);

            fetch(`/tasks/${taskId}/update-progress`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        progress: progress
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (progress >= 100) {
                        // Déplacer automatiquement la tâche dans la colonne "Fait"
                        const card = document.querySelector(`.task-card[data-id='${taskId}']`);
                        const wrapper = document.querySelector(`.progress-wrapper[data-task-id='${taskId}']`);
                        const doneList = document.getElementById('done');
                        if (card) {
                            doneList.appendChild(card);
                        }
                        if (wrapper) {
                            wrapper.remove();
                        }
                    }
                })
                .catch(error => {
                    console.error('Erreur lors de la mise à jour du progrès :', error);
                });
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
