<?php

namespace Engine\Match;

enum BonusType
{
    case Horizontal;
    case Vertical;
    case None;

    public function score(): float
    {
        return match ($this) {
            BonusType::Vertical => 2,
            BonusType::None => 0,
            BonusType::Horizontal => 2,
        };
    }
}
