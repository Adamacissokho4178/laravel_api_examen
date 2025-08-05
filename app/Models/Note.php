<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Note extends Model
{
    use HasFactory;

    protected $table = 'notes';
    protected $fillable = [
        'eleve_id', 'matiere_id', 'enseignant_id', 'periode', 'note', 'appreciation'
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
    public static function calculerMoyenneEleve($eleveId, $periode = null)
    {
        $query = self::join('matieres', 'notes.matiere_id', '=', 'matieres.id')
            ->where('notes.eleve_id', $eleveId);

        if ($periode) {
            $query->where('notes.periode', $periode);
        }

        $resultats = $query->select(
            'notes.matiere_id',
            'matieres.nom as matiere_nom',
            'matieres.coefficient',
            'notes.note',
            'notes.appreciation'
        )->get();

        if ($resultats->isEmpty()) {
            return [
                'moyenne' => 0,
                'mention' => 'Non évalué',
                'total_coefficient' => 0,
                'notes' => []
            ];
        }

        $totalPoints = 0;
        $totalCoefficient = 0;
        $notes = [];

        foreach ($resultats as $resultat) {
            $points = $resultat->note * $resultat->coefficient;
            $totalPoints += $points;
            $totalCoefficient += $resultat->coefficient;

            $notes[] = [
                'matiere_id' => $resultat->matiere_id,
                'matiere_nom' => $resultat->matiere_nom,
                'coefficient' => $resultat->coefficient,
                'note' => $resultat->note,
                'appreciation' => $resultat->appreciation,
                'points' => $points
            ];
        }

        $moyenne = $totalCoefficient > 0 ? round($totalPoints / $totalCoefficient, 2) : 0;
        $mention = self::getMention($moyenne);

        return [
            'moyenne' => $moyenne,
            'mention' => $mention,
            'total_coefficient' => $totalCoefficient,
            'notes' => $notes
        ];
    }

    /**
     * Calculer le rang d'un élève pour une période donnée
     */
    public static function calculerRangEleve($eleveId, $periode = null)
    {
        $query = self::join('matieres', 'notes.matiere_id', '=', 'matieres.id')
            ->join('eleves', 'notes.eleve_id', '=', 'eleves.id')
            ->join('classes', 'eleves.classe_id', '=', 'classes.id');

        if ($periode) {
            $query->where('notes.periode', $periode);
        }

        // Récupérer la classe de l'élève
        $eleve = Eleve::find($eleveId);
        if (!$eleve) {
            return null;
        }

        $query->where('eleves.classe_id', $eleve->classe_id);

        $moyennes = $query->select(
            'eleves.id as eleve_id',
            'eleves.nom',
            'eleves.prenom',
            DB::raw('SUM(notes.note * matieres.coefficient) / SUM(matieres.coefficient) as moyenne')
        )
        ->groupBy('eleves.id', 'eleves.nom', 'eleves.prenom')
        ->orderBy('moyenne', 'desc')
        ->get();

        $rang = $moyennes->search(function ($item) use ($eleveId) {
            return $item->eleve_id == $eleveId;
        });

        return $rang !== false ? $rang + 1 : null;
    }

    /**
     * Obtenir les statistiques des notes
     */
    public static function getStatistiques($periode = null)
    {
        $query = self::join('matieres', 'notes.matiere_id', '=', 'matieres.id');

        if ($periode) {
            $query->where('notes.periode', $periode);
        }

        $stats = $query->select(
            DB::raw('COUNT(*) as total_notes'),
            DB::raw('AVG(notes.note) as moyenne_generale'),
            DB::raw('MIN(notes.note) as note_min'),
            DB::raw('MAX(notes.note) as note_max'),
            DB::raw('COUNT(DISTINCT notes.eleve_id) as nombre_eleves'),
            DB::raw('COUNT(DISTINCT notes.matiere_id) as nombre_matieres')
        )->first();

        return [
            'total_notes' => $stats->total_notes,
            'moyenne_generale' => round($stats->moyenne_generale, 2),
            'note_min' => $stats->note_min,
            'note_max' => $stats->note_max,
            'nombre_eleves' => $stats->nombre_eleves,
            'nombre_matieres' => $stats->nombre_matieres
        ];
    }

    /**
     * Déterminer la mention selon la moyenne
     */
    private static function getMention($moyenne)
    {
        if ($moyenne >= 16) return 'Très Bien';
        if ($moyenne >= 14) return 'Bien';
        if ($moyenne >= 12) return 'Assez Bien';
        if ($moyenne >= 10) return 'Passable';
        if ($moyenne >= 8) return 'Insuffisant';
        return 'Très Insuffisant';
    }

    /**
     * Obtenir les notes d'un élève avec les moyennes par matière
     */
    public static function getNotesAvecMoyennes($eleveId, $periode = null)
    {
        $query = self::join('matieres', 'notes.matiere_id', '=', 'matieres.id')
            ->where('notes.eleve_id', $eleveId);

        if ($periode) {
            $query->where('notes.periode', $periode);
        }

        return $query->select(
            'notes.id',
            'notes.matiere_id',
            'matieres.nom as matiere_nom',
            'matieres.coefficient',
            'notes.note',
            'notes.appreciation',
            'notes.periode'
        )->get();
    }
} 