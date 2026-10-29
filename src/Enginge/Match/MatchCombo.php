<?php

namespace Engine\Match;

class MatchCombo
{
    public function __construct(public readonly array $match,
    public readonly BonusType $bonusType)
    {

    }

    public function isBonus(): bool
    {
        return $this->bonusType !== BonusType::None;
    }
}