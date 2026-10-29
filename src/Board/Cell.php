<?php

namespace Board;

use Pieces\Piece;

class Cell
{
    public function __construct(
        public readonly int $row,
        public readonly int $column,
    )
    {}
}