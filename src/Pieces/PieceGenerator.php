<?php

namespace Pieces;

class PieceGenerator
{
    public function generate(): Piece
    {
        $symbol = PiecesType::cases()[rand(0, count(PiecesType::cases()) - 1)];
        return self::generator($symbol)();
    }


    private function generator(PiecesType $symbol): \Closure
    {
        $generators = [
            "A" => static fn() => Piece::NewA(),
            "B" => static fn() => Piece::NewB(),
            "C" => static fn() => Piece::NewC(),
            "D" => static fn() => Piece::NewD(),
            "E" => static fn() => Piece::NewE(),
            "F" => static fn() => Piece::NewF(),
            "G" => static fn() => Piece::NewG(),
        ];

        return $generators[$symbol->value];
    }
}
