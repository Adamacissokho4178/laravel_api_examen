<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;

    protected $table = 'notes';
    protected $fillable = [
        'eleve_id', 'matiere_id', 'enseignant_id', 'note', 'periode', 
        'appreciation', 'date_evaluation'
    ];

    protected $casts = [
        'note' => 'decimal:2',
        'date_evaluation' => 'date',
    ];

    public function eleve()
    {
        return $this->belongsTo(Eleve::class, 'eleve_id');
    }

    public function matiere()
    {
        return $this->belongsTo(Matiere::class, 'matiere_id');
    }

    public function enseignant()
    {
        return $this->belongsTo(Enseignant::class, 'enseignant_id');
    }

    /**
     * Calculer la moyenne d'un élève pour une période donnée
     */
    public static function calculerMoyenneEleve(int $eleveId, string $periode = null): array
    {
        $query = self::where('eleve_id', $eleveId)
                    ->with(['matiere', 'enseignant']);

        if ($periode) {
            $query->where('periode', $periode);
        }

        $notes = $query->get();

        if ($notes->isEmpty()) {
            return [
                'moyenne' => 0,
                'mention' => 'Non évalué',
                'notes' => [],
                'total_coefficient' => 0
            ];
        }

        $totalPoints = 0;
        $totalCoefficient = 0;

        foreach ($notes as $note) {
            $coefficient = $note->matiere->coefficient ?? 1;
            $totalPoints += $note->note * $coefficient;
            $totalCoefficient += $coefficient;
        }

        $moyenne = $totalCoefficient > 0 ? $totalPoints / $totalCoefficient : 0;

        return [
            'moyenne' => round($moyenne, 2),
            'mention' => self::determinerMention($moyenne),
            'notes' => $notes,
            'total_coefficient' => $totalCoefficient
        ];
    }

    /**
     * Calculer le rang d'un élève dans sa classe
     */
    public static function calculerRangEleve(int $eleveId, string $periode = null): int
    {
        $query = self::select('eleve_id')
                    ->selectRaw('AVG(notes.note) as moyenne')
                    ->groupBy('eleve_id');

        if ($periode) {
            $query->where('periode', $periode);
        }

        $moyennes = $query->orderByDesc('moyenne')->get();

        $rang = $moyennes->search(function ($item) use ($eleveId) {
            return $item->eleve_id == $eleveId;
        });

        return $rang !== false ? $rang + 1 : 0;
    }

    /**
     * Déterminer la mention selon la moyenne
     */
    private static function determinerMention(float $moyenne): string
    {
        if ($moyenne >= 16) {
            return 'Très Bien';
        } elseif ($moyenne >= 14) {
            return 'Bien';
        } elseif ($moyenne >= 12) {
            return 'Assez Bien';
        } elseif ($moyenne >= 10) {
            return 'Passable';
        } else {
            return 'Insuffisant';
        }
    }

    /**
     * Obtenir les statistiques des notes
     */
    public static function getStatistiques(string $periode = null): array
    {
        $query = self::query();

        if ($periode) {
            $query->where('periode', $periode);
        }

        $stats = $query->selectRaw('
            COUNT(*) as total_notes,
            AVG(note) as moyenne_generale,
            MIN(note) as note_min,
            MAX(note) as note_max,
            STDDEV(note) as ecart_type
        ')->first();

        return [
            'total_notes' => $stats->total_notes ?? 0,
            'moyenne_generale' => round($stats->moyenne_generale ?? 0, 2),
            'note_min' => $stats->note_min ?? 0,
            'note_max' => $stats->note_max ?? 0,
            'ecart_type' => round($stats->ecart_type ?? 0, 2)
        ];
    }
} 