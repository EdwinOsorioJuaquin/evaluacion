<?php

namespace App\Observers;

use App\Models\Graduate;
use App\Models\EmpGraduateSurvey;
use App\Models\EmpSurvey;
use Illuminate\Support\Facades\Log;

class GraduateObserver
{
    /**
     * Handle the Graduate "created" event.
     */
    public function created(Graduate $graduate): void
    {
        // Si se crea directamente como graduado, asignar encuestas
        if ($graduate->state === 'graduated') {
            $this->assignSurveysToGraduate($graduate);
        }
    }

    /**
     * Handle the Graduate "updated" event.
     */
    public function updated(Graduate $graduate): void
    {
        // Verificar si el estado cambió a 'graduated'
        if ($graduate->isDirty('state') && $graduate->state === 'graduated') {
            $this->assignSurveysToGraduate($graduate);
        }
    }

    /**
     * Asignar encuestas automáticamente a un graduado
     */
    private function assignSurveysToGraduate(Graduate $graduate): void
    {
        try {
            // Obtener todas las encuestas activas
            $activeSurveys = EmpSurvey::where('is_active', true)->get();

            foreach ($activeSurveys as $survey) {
                // Verificar si ya existe la asignación para evitar duplicados
                $exists = EmpGraduateSurvey::where('graduate_id', $graduate->id)
                    ->where('survey_id', $survey->id)
                    ->exists();

                if (!$exists) {
                    // Crear asignación automática
                    EmpGraduateSurvey::create([
                        'graduate_id' => $graduate->id,
                        'survey_id' => $survey->id,
                        'status' => 'pending',
                        'assigned_date' => now(),
                    ]);

                    // Log para debugging
                    Log::info("Encuesta asignada automáticamente", [
                        'graduate_id' => $graduate->id,
                        'user_name' => $graduate->user->full_name,
                        'survey_id' => $survey->id,
                        'survey_name' => $survey->name
                    ]);
                }
            }

        } catch (\Exception $e) {
            Log::error("Error al asignar encuestas automáticamente", [
                'graduate_id' => $graduate->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}