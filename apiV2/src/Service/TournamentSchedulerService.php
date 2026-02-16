<?php
namespace App\Service;

class TournamentSchedulerService
{
    // Costanti per i tempi (in secondi)
    private const TIME_PALLONCINO = 50; // 30s + 20s cambio
    private const TIME_PERCORSO_BASE = 15; // gap tra partenze
    private const TIME_KATA_BASE = 65; // 45s + 20s voto
    private const TIME_GESTIONE_KUMITE = 30; // Tempo morto tra incontri

    public function generateSchedule(array $categories, int $tatamiPre, int $tatamiAgo, string $startTime)
    {
        $schedule = [];
        $tatamiTimelines = array_fill(1, max($tatamiPre, $tatamiAgo), strtotime($startTime));
        
        // Funzione helper per estrarre il tipo (prime 3 lettere dell'ID)
        $getTipo = fn($cat) => substr($cat['id'], 0, 3);

        // Definiamo i tipi pre-agonistici
        $tipiPre = ['PAL', 'PER', 'KAG', 'KUG'];

        // Filtriamo usando il prefisso dell'ID
        $preAgonistic = array_filter($categories, fn($c) => in_array($getTipo($c), $tipiPre));
        $agonistic = array_filter($categories, fn($c) => !in_array($getTipo($c), $tipiPre));

        // 1. Fase Pre-Agonistica (Usa tatamiPre)
        foreach ($preAgonistic as $cat) {
            $duration = $this->calculateDuration($cat);
            $assignedTatami = $this->getEarliestTatami($tatamiTimelines, $tatamiPre);
            
            $start = $tatamiTimelines[$assignedTatami];
            $tatamiTimelines[$assignedTatami] += $duration;
            
            $schedule[] = $this->formatEntry($assignedTatami, $start, $tatamiTimelines[$assignedTatami], $cat);
        }

        // Calcolo orario di transizione: il momento in cui i tatami in eccesso vengono chiusi
        $transitionTime = max(array_slice($tatamiTimelines, 0, $tatamiPre));

        // 2. Fase Agonistica (Usa tatamiAgo)
        foreach ($agonistic as $cat) {
            $duration = $this->calculateDuration($cat);
            // Consideriamo solo i primi 'tatamiAgo' tatami
            $assignedTatami = $this->getEarliestTatami($tatamiTimelines, $tatamiAgo);
            
            // Se un tatami era libero molto prima della fine della fase pre-agonistica, 
            // deve comunque aspettare la transizione se necessario, o partire subito.
            $start = $tatamiTimelines[$assignedTatami];
            $tatamiTimelines[$assignedTatami] += $duration;

            $schedule[] = $this->formatEntry($assignedTatami, $start, $tatamiTimelines[$assignedTatami], $cat);
        }

        return $schedule;
    }

    private function calculateDuration($cat): int
    {

        $n = $cat['n_athletes']; // Assicurati che il nome del campo atleti sia corretto
        $tipo = substr($cat['id'], 0, 3);
        
        switch ($tipo) {
            case 'PAL': return ($n / 2) * self::TIME_PALLONCINO;
            case 'PER': return ($n * self::TIME_PERCORSO_BASE) + 30;
            case 'KAG':
            case 'KAS': return $n * self::TIME_KATA_BASE;
            case 'KUG': return ($n - 1) * (60 + self::TIME_GESTIONE_KUMITE);
            case 'KIA': return $this->calculateKataAdulti($n, $cat['grado']);
            case 'KUA': return $this->calculateKumiteAdulti($n, $cat['grado'], $cat['categoria']);
            default: return 300; // 5 min di default se il tipo non è riconosciuto
        }
    }

    private function calculateKataAdulti($n, $grado): int
    {
        $fase1 = $n * 60; // Punteggio
        $fase2 = 0;
        if ($grado !== 'Marrone/Nera' && $n > 4) $fase2 = 3 * 60; // Semifinali + Finale
        if ($grado === 'Marrone/Nera') {
            if ($n > 8) $fase2 = 7 * 60;
            elseif ($n > 4) $fase2 = 3 * 60;
        }
        return $fase1 + $fase2;
    }

    private function calculateKumiteAdulti($n, $grado, $eta): int
    {
        $durataIncontro = ($eta === 'Esordienti') ? 90 : 120;
        $incontri = $n - 1;
        // Aggiunta ripescaggi per Marroni/Nere con > 4 atleti
        if ($grado === 'Marrone/Nera' && $n > 4) $incontri += 2; 
        
        return $incontri * ($durataIncontro + self::TIME_GESTIONE_KUMITE);
    }

    private function getEarliestTatami($timelines, $limit): int
    {
        $subset = array_slice($timelines, 0, $limit, true);
        return array_keys($subset, min($subset))[0];
    }

    private function formatEntry($tatami, $start, $end, $cat): array
    {
        return [
            'tatami' => $tatami,
            'inizio' => date('H:i', $start),
            'fine' => date('H:i', $end),
            'categoria' => $cat['specialita']. ' '.$cat['categoria'].' '.$cat['grado'].' '.$cat['sesso'].' '.$cat['cat_peso'] ,
            'tipo' => $cat['id']
        ];
    }
}