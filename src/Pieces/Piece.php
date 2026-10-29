<?php

namespace Pieces;

class Piece
{
    private function __construct(private PiecesType $symbol)
    {
    }

    public static function NewA(): self
    {
        return new self(PiecesType::A);
    }

    public static function NewB(): self
    {
        return new self(PiecesType::B);
    }

    public static function NewC(): self
    {
        return new self(PiecesType::C);
    }

    public static function NewD(): self
    {
        return new self(PiecesType::D);
    }

    public static function NewE(): self
    {
        return new self(PiecesType::E);
    }

    public static function NewF(): self
    {
        return new self(PiecesType::F);
    }

    public static function NewG(): self
    {
        return new self(PiecesType::G);
    }

    public function draw()
    {
        echo $this->symbol->value;
    }

    public function type(): PiecesType
    {
        return $this->symbol;
    }

    public function isEqual(Piece $piece): bool
    {
        return $this->type() === $piece->type();
    }
}
