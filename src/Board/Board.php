<?php

namespace Board;

use Engine\Match\MatchFinder;
use Pieces\PieceGenerator;

class Board
{
    private function __construct(
        private array          $board,
        private int            $height,
        private int            $width,
        private PieceGenerator $pieceGenerator = new PieceGenerator(),
        private MatchFinder     $matchFinder = new MatchFinder())
    {
        for ($i = 0; $i < $this->height; $i++) {
            for ($j = 0; $j < $this->width; $j++) {
                $this->board[$i][$j] = $this->pieceGenerator->generate();
            }
        }
    }

    public static function BoardOfN(int $n): self
    {
        $board = [];
        for ($i = 0; $i < $n; $i++) {
            $board[] = [];
        }

        return new self($board, $n, $n);
    }

    public function hasMove(): bool
    {
        return !empty($this->matchFinder->findPossibleMatches($this->getBoardState()));
    }

    public function applyGravity(): array
    {
        $cellToFill = [];
        for ($j = 0; $j < $this->width; $j++) {
            $write = $this->height - 1;
            for ($read = $this->height - 1; $read >= 0; $read--) {
                if (is_null($this->board[$read][$j])) {
                    continue;
                }
                if ($read !== $write) {
                    $this->board[$write][$j] = $this->board[$read][$j];
                    $this->board[$read][$j] = null;
                }
                $write--;
            }

            for ($i = $write; $i >= 0; $i--) {
                $cellToFill[] = new Cell($i, $j);
            }
        }
        return $cellToFill;
    }

    public function clearCell(Cell $cell): void
    {
        $this->board[$cell->row][$cell->column] = null;
    }

    public function fillCell(Cell $cell): void
    {
        $this->board[$cell->row][$cell->column] = $this->pieceGenerator->generate();
    }

    public function processMove(Cell $from, Cell $to): void
    {
        $temp = $this->board[$from->row][$from->column];
        $this->board[$from->row][$from->column] = $this->board[$to->row][$to->column];
        $this->board[$to->row][$to->column] = $temp;
    }

    public function draw(): void
    {
        $separator = "   +" . str_repeat("---+", $this->width) . "\n";

        echo "    ";
        for ($j = 0; $j < $this->width; $j++) {
            printf(" %-3d", $j);
        }
        echo "\n";

        echo $separator;
        for ($i = 0; $i < $this->height; $i++) {
            printf("%2d |", $i);
            for ($j = 0; $j < $this->width; $j++) {
                echo " ";
                is_null($this->board[$i][$j]) ? print " " : $this->board[$i][$j]->draw();
                echo " |";
            }
            echo "\n";
            echo $separator;
        }
    }

    public function getBoardState(): array
    {
        return $this->board;
    }
}