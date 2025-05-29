<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AccessToken;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class SuiviController extends Controller
{
    public function show($token)
    {
        $access = AccessToken::where('token', $token)->firstOrFail();

        if (!$access->isValid()) {
            abort(403, 'Lien expiré');
        }

        $startOfWeek = Carbon::now()->startOfWeek(); // lundi
        $endOfWeek = Carbon::now()->endOfWeek();     // dimanche

        $yearWeek = now()->format('o-\WW');

        $currentWeek = now()->weekOfYear;
        //dd($yearWeek);

        $employee = $access->employee;
        if (!$employee) {
            abort(404, 'Employé non trouvé');
        }

        $tasks = Task::where('employee_id', $access->employee_id)
            ->whereNotIn('status', ['done'])
            ->where('due_week', $yearWeek)
            ->get();

        return view('app.suivi.show', compact('access', 'tasks', 'employee', 'startOfWeek', 'endOfWeek', 'currentWeek'));
    }

    public function update(Request $request, $token)
    {
        $access = AccessToken::where('token', $token)->firstOrFail();

        if (!$access->isValid()) {
            abort(403, 'Lien expiré');
        }

        foreach ($request->tasks as $taskId => $status) {
            Task::where('id', $taskId)
                ->where('employee_id', $access->employee_id)
                ->update(['status' => $status]);
        }

        return redirect()->back()->with('success', '✅ Tâches mises à jour avec succès');
    }

    public function submit(Request $request, $token)
    {
        // Récupération du token
        $accessToken = AccessToken::where('token', $token)
            ->where('expires_at', '>', Carbon::now())
            ->firstOrFail();

        $employee = $accessToken->employee;

        // Validation des données entrantes
        $validated = $request->validate([
            'statuses' => 'required|array',
            'statuses.*' => 'in:done,pending,in_progress',
        ]);

        // Mise à jour des statuts
        DB::transaction(function () use ($validated, $employee) {
            foreach ($validated['statuses'] as $taskId => $status) {
                $task = Task::where('id', $taskId)
                    ->where('employee_id', $employee->id)
                    ->first();

                if ($task) {
                    $task->status = $status;
                    $task->save();
                }
            }
        });

        return redirect()
            ->route('suivi.show', $token)
            ->with('success', '✅ Les statuts de vos tâches ont bien été mis à jour.');
    }
}
