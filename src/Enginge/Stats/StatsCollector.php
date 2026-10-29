<?php

namespace Engine\Stats;

use Board\Cell;
use Engine\Match\BonusType;

class StatsCollector
{
    public function __construct(
        private int $score = 0,
        private int $bonusCount = 0,
        private int $cellsClearCount = 0,
        private array $moveHistory = [],
    ) {}


    public function addScore(int $score): void
    {
        $this->score += $score;
    }

    public function addBonus(BonusType $bonus): void
    {
        if ($bonus !== BonusType::None) {
            $this->bonusCount += 1;
        }
    }

    public function addCellsClear(int $cellsClear): void
    {
        $this->cellsClearCount += $cellsClear;
    }

    public function addMoveHistory(Cell $from, Cell $to, int $score): void
    {
        $this->moveHistory[] = [
            'from' => [$from->row, $from->column],
            'to' => [$to->row, $to->column],
            'score' => $score,
        ];
    }

    public function getData(): array
    {
        return [
            'score' => $this->score,
            'bonusCount' => $this->bonusCount,
            'cellsClearCount' => $this->cellsClearCount,
            'moveHistory' => $this->moveHistory,
        ];
    }
}
