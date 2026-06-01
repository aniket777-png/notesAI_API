<?php

namespace App\Services;

class SearchService
{
    public function vectorize($text)
    {
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9 ]/', '', $text);

        $words = explode(' ', $text);

        $vector = [];

        foreach ($words as $word) {
            if (!$word) continue;
            $vector[$word] = ($vector[$word] ?? 0) + 1;
        }

        return $vector;
    }

    public function cosineSimilarity($a, $b)
    {
        $dot = 0;
        $magA = 0;
        $magB = 0;

        $allKeys = array_unique(array_merge(array_keys($a), array_keys($b)));

        foreach ($allKeys as $key) {

            $av = $a[$key] ?? 0;
            $bv = $b[$key] ?? 0;

            $dot += $av * $bv;
            $magA += $av * $av;
            $magB += $bv * $bv;
        }

        if ($magA == 0 || $magB == 0) return 0;

        return $dot / (sqrt($magA) * sqrt($magB));
    }
}

?>