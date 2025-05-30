<div class="p-6 max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('employees.index') }}" class="text-blue-600 hover:underline">&larr; Retour</a>
        <h1 class="text-2xl font-bold mt-2">Détails de l'employé</h1>
        <div class="mt-2 text-gray-700">
            <p><strong>Nom:</strong> {{ $employee->name }}</p>
            <p><strong>Email:</strong> {{ $employee->email }}</p>
            <p><strong>Rôle:</strong> {{ $employee->role }}</p>
        </div>
    </div>

    <!-- Résumé -->
    {{-- <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white shadow rounded-xl p-4">
            <p class="text-sm text-gray-500">Tâches totales</p>
            <p class="text-xl font-semibold">{{ $summary['total'] }}</p>
        </div>
        <div class="bg-green-100 shadow rounded-xl p-4">
            <p class="text-sm text-gray-500">Terminées</p>
            <p class="text-xl font-semibold text-green-800">{{ $summary['done'] }}</p>
        </div>
        <div class="bg-yellow-100 shadow rounded-xl p-4">
            <p class="text-sm text-gray-500">En cours</p>
            <p class="text-xl font-semibold text-yellow-800">{{ $summary['in_progress'] }}</p>
        </div>
        <div class="bg-red-100 shadow rounded-xl p-4">
            <p class="text-sm text-gray-500">En retard</p>
            <p class="text-xl font-semibold text-red-800">{{ $summary['late'] }}</p>
        </div>
    </div> --}}

    <!-- Progression Globale -->
    {{-- <div class="mb-8">
        <p class="mb-2 font-medium">Progression globale :</p>
        <div class="w-full bg-gray-200 rounded-full h-4">
            <div class="bg-blue-600 h-4 rounded-full" style="width: {{ $summary['progress'] }}%"></div>
        </div>
        <p class="text-sm text-right mt-1">{{ $summary['progress'] }}%</p>
    </div> --}}

    <!-- Tableau des tâches -->
    <div class="bg-white shadow rounded-xl p-6">
        <h2 class="text-lg font-bold mb-4">Tâches de la semaine</h2>
        <table class="w-full text-sm text-left">
            <thead>
                <tr class="text-gray-600 border-b">
                    <th class="py-2">Titre</th>
                    {{-- <th>Deadline</th> --}}
                    <th>Progression</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tasks as $task)
                <tr class="border-b">
                    <td class="py-2">{{ $task->name }}</td>
                    {{-- <td>{{ $task->deadline->format('d/m/Y') }}</td> --}}
                    <td>{{ $task->progress }}%</td>
                    <td>
                        <span class="px-2 py-1 rounded-full text-xs {{
                            $task->status === 'done' ? 'bg-green-200 text-green-800' :
                            ($task->status === 'in_progress' ? 'bg-yellow-200 text-yellow-800' :
                            ($task->status === 'late' ? 'bg-red-200 text-red-800' : 'bg-gray-200 text-gray-700'))
                        }}">
                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('tasks.show', $task) }}" class="text-blue-600 hover:underline">👁️</a>
                        <a href="{{ route('tasks.edit', $task) }}" class="text-yellow-600 hover:underline ml-2">✏️</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Historique -->
    <div class="mt-10">
        <h2 class="text-lg font-bold mb-4">Historique des semaines précédentes</h2>
        {{-- <div class="space-y-2">
            @foreach($pastWeeks as $week)
            <details class="bg-white shadow rounded-xl p-4">
                <summary class="cursor-pointer font-semibold">Semaine du {{ $week['start']->format('d/m') }} au {{ $week['end']->format('d/m') }}</summary>
                <ul class="mt-2 list-disc list-inside text-sm text-gray-700">
                    @foreach($week['tasks'] as $t)
                    <li>{{ $t->title }} - {{ ucfirst($t->status) }} ({{ $t->progress }}%)</li>
                    @endforeach
                </ul>
            </details>
            @endforeach
        </div> --}}
    </div>
</div>
