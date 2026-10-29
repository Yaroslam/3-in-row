<?php

namespace Engine\Stats;

use Engine\Match\BonusType;
use Engine\Match\MatchClearResult;

class ScoreCounter
{

    private const int SCORE_FOR_CELL = 10;

    public function __construct(
        private int $score = 0,
    )
    {
    }

    public function countMatch(MatchClearResult $match): void
    {
        $this->score += $match->cellCount * self::SCORE_FOR_CELL;
    }

    public function countBonus(MatchClearResult $match): void
    {
        $this->score += $match->cellCount * $match->bonusType->score();
    }

    public function getScore(): int
    {
        return $this->score;
    }
}