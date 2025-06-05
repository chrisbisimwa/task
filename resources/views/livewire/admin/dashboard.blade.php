<div>
    <div class="row">
    <!-- Employés ayant accédé -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $countWithAccess }}</h3>
                <p>Employés ont accédé</p>
            </div>
            <div class="icon">
                <i class="fas fa-user-check"></i>
            </div>
        </div>
    </div>
    <!-- Employés n'ayant pas accédé -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $countWithoutAccess }}</h3>
                <p>Employés sans accès</p>
            </div>
            <div class="icon">
                <i class="fas fa-user-times"></i>
            </div>
        </div>
    </div>
    <!-- Notifications réussies -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $notifSuccess }}</h3>
                <p>Notifications (succès)</p>
            </div>
            <div class="icon">
                <i class="fas fa-paper-plane"></i>
            </div>
        </div>
    </div>
    <!-- Notifications échouées -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $notifFailed }}</h3>
                <p>Notifications (échecs)</p>
            </div>
            <div class="icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Feedbacks récents -->
    <div class="col-md-6">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">Feedbacks récents</h3>
            </div>
            <div class="card-body">
                {{-- @forelse($feedbacks as $feedback)
                    <div class="callout callout-info py-2">
                        <strong>{{ $feedback->employee->name ?? 'Employé inconnu' }}</strong>
                        @if($feedback->rating)
                            <span class="badge badge-warning ml-2">{{ $feedback->rating }}/5</span>
                        @endif
                        <br>
                        <span>{{ $feedback->comment }}</span>
                        <div class="text-muted text-xs">{{ $feedback->created_at->diffForHumans() }}</div>
                    </div>
                @empty
                    <p>Aucun feedback récent.</p>
                @endforelse --}}
            </div>
        </div>
    </div>
    <!-- Tâches urgentes -->
    <div class="col-md-6">
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h3 class="card-title">Tâches urgentes en cours</h3>
            </div>
            <div class="card-body">
                @forelse($urgentTasks as $task)
                    <div class="callout callout-danger py-2">
                        <strong>{{ $task->name }}</strong>
                        <span class="text-muted text-xs ml-2">({{ $task->employee->name ?? 'N/A' }})</span>
                        <div class="text-muted text-xs">{{ $task->due_week }}</div>
                    </div>
                @empty
                    <p>Aucune tâche urgente en cours.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

</div>
