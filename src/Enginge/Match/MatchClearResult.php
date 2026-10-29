<?php

namespace Engine\Match;

class MatchClearResult
{
    public function __construct(
        public readonly int $cellCount,
        public readonly BonusType $bonusType,
    ) {}
}