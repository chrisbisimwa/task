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


        // startOfWeek (Lundi 26 Février 2024) en français
        $startOfWeek->locale('fr'); // Assurez-vous que la locale est définie sur 'fr'
        $weekStart = $startOfWeek->format('l d/m/Y');
        // endOfWeek (Dimanche 3 Mars 2024)
        $endOfWeek->locale('fr'); // Assurez-vous que la locale est définie sur 'fr'
        $weekEnd = $endOfWeek->format('l d/m/Y');


        $yearWeek = now()->format('o-\WW');

        $currentWeek = now()->weekOfYear;
        //dd($yearWeek);

        $employee = $access->employee;
        if (!$employee) {
            abort(404, 'Employé non trouvé');
        }

        $tasks = Task::where('employee_id', $access->employee_id)
            /* ->whereNotIn('status', ['done']) */
            ->where('due_week', $yearWeek)
            ->get();

        return view('app.suivi.show', compact('access', 'tasks', 'employee', 'weekStart', 'weekEnd', 'currentWeek'));
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
        //$access = AccessToken::where('token', $token)->first();
        $access = AccessToken::where('token', $token)
            ->where('expires_at', '>', Carbon::now())
            ->firstOrFail();

        if (! $access) {
            return redirect()->back()->with('error', 'Lien expiré ou invalide.');
        }


        /*  if (! $access || $access->isValid()) {
            return redirect()->back()->with('error', 'Lien expiré ou invalide.');
        } */

        // Décoder le JSON des statuts mis à jour
        $updates = json_decode($request->input('statuses'), true);

        if (! is_array($updates)) {
            return redirect()->back()->with('error', 'Données de statut invalides.');
        }

        // Vérifier que les tâches appartiennent bien à l'employé
        $taskIds = array_keys($updates);
        $tasks = Task::whereIn('id', $taskIds)
            ->where('employee_id', $access->employee_id)
            ->get();

        // Appliquer les mises à jour
        DB::beginTransaction();
        try {
            foreach ($tasks as $task) {
                $newStatus = $updates[$task->id];
                if (in_array($newStatus, ['done', 'in_progress', 'pending'])) {
                    $task->status = $newStatus;
                    $task->save();
                }
            }
            DB::commit();

            return redirect()->back()->with('success', 'Mise à jour des statuts effectuée avec succès !');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Une erreur est survenue lors de la mise à jour.');
        }
    }

    public function updateProgress(Request $request, $id)
    {
        $request->validate([
            'progress' => 'required|integer|min:0|max:100',
        ]);

        $task = Task::findOrFail($id); 

        $task->progress = $request->progress;

        if ($request->progress >= 100) {
            $task->status = 'done';
            $task->progress = 100;
        } elseif ($task->status === 'pending') {
            $task->status = 'in_progress';
        }

        $task->save();

        return response()->json(['status' => 'success']);
    }
}
