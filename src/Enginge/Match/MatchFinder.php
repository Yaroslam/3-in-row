<?php

namespace Engine\Match;

use Board\Board;
use Board\Cell;

class MatchFinder
{
    public function findAllMatches(array $boardState): array
    {
        return $this->findMatchesInState($boardState);
    }

    private function isBonus(MatchCombo $matchCombo): bool
    {
        return $matchCombo->isBonus();
    }

    public function findPossibleMatches(array $boardState): array
    {
        $height = count($boardState);
        $width = count($boardState[0]);
        $moves = [];

        for ($i = 0; $i < $height; $i++) {
            for ($j = 0; $j < $width; $j++) {
                foreach ([[$i, $j + 1], [$i + 1, $j]] as [$row, $column]) {
                    if ($row >= $height || $column >= $width) {
                        continue;
                    }

                    $from = new Cell($i, $j);
                    $to = new Cell($row, $column);
                    if ($this->isMatchAfterSwap($boardState, $from, $to)) {
                        $moves[] = [$from, $to];
                    }
                }
            }
        }

        return $moves;
    }

    public function resolveMatches(array $matches, array $boardState): array
    {
        $height = count($boardState);
        $width = count($boardState[0]);

        $cellsToClear = [];
        foreach ($matches as $matchCombo) {
            $cells = $this->isBonus($matchCombo)
                ? $this->bonusCells($matchCombo, $height, $width)
                : $matchCombo->match;

            foreach ($cells as $cell) {
                $cellsToClear["{$cell->row}:{$cell->column}"] = $cell;
            }

        }
        return $cellsToClear;
    }

    private function bonusCells(MatchCombo $matchCombo, int $height, int $width): array
    {
        [$first, $second] = $matchCombo->match;

        return match ($matchCombo->bonusType) {
            BonusType::Horizontal => $this->rowCells($first->row, $width),
            BonusType::Vertical => $this->columnCells($first->column, $height),
            BonusType::None => $matchCombo->match,
        };
    }

    public function isMatchAfterSwap(array $boardState, Cell $from, Cell $to): bool
    {
        $fromPiece = $boardState[$from->row][$from->column];
        $toPiece = $boardState[$to->row][$to->column];
        if (is_null($fromPiece) || is_null($toPiece) || $fromPiece->isEqual($toPiece)) {
            return false;
        }

        $boardState[$from->row][$from->column] = $toPiece;
        $boardState[$to->row][$to->column] = $fromPiece;

        foreach ($this->findMatchesInState($boardState) as $combo) {
            if (array_any($combo->match, fn($cell) => ($cell->row === $from->row && $cell->column === $from->column)
                || ($cell->row === $to->row && $cell->column === $to->column))) {
                return true;
            }
        }
        return false;
    }

    private function findMatchesInState(array $boardState): array
    {
        return array_merge(
            $this->find3InRowHorizontal($boardState),
            $this->find3InRowVertical($boardState),
        );
    }

    private function find3InRowHorizontal(array $boardState): array
    {
        $width = count($boardState[0]);
        $matches = [];
        for ($i = 0; $i < count($boardState); $i++) {
            $line = $this->rowCells($i, $width);
            $matches = array_merge($matches, $this->findInLine($boardState, $line, BonusType::Horizontal));
        }

        return $matches;
    }

    private function find3InRowVertical(array $boardState): array
    {
        $height = count($boardState);
        $matches = [];
        for ($j = 0; $j < count($boardState[0]); $j++) {
            $line = $this->columnCells($j, $height);
            $matches = array_merge($matches, $this->findInLine($boardState, $line, BonusType::Vertical));
        }

        return $matches;
    }

    /**
     * @return Cell[]
     */
    private function rowCells(int $row, int $width): array
    {
        $line = [];
        for ($j = 0; $j < $width; $j++) {
            $line[] = new Cell($row, $j);
        }

        return $line;
    }

    /**
     * @return Cell[]
     */
    private function columnCells(int $column, int $height): array
    {
        $line = [];
        for ($i = 0; $i < $height; $i++) {
            $line[] = new Cell($i, $column);
        }

        return $line;
    }

    private function findInLine(array $boardState, array $line, BonusType $longBonus): array
    {
        $matches = [];
        $run = [];
        foreach ($line as $cell) {
            $piece = $boardState[$cell->row][$cell->column];
            $last = empty($run) ? null : $boardState[end($run)->row][end($run)->column];

            if (!is_null($piece) && !is_null($last) && $piece->isEqual($last)) {
                $run[] = $cell;
                continue;
            }

            $matchCombo = $this->createCombo($run, $longBonus);
            if (!is_null($matchCombo)) {
                $matches[] = $matchCombo;
            }
            $run = is_null($piece) ? [] : [$cell];
        }

        $matchCombo = $this->createCombo($run, $longBonus);
        if (!is_null($matchCombo)) {
            $matches[] = $matchCombo;
        }

        return $matches;
    }

    private function createCombo(array $run, BonusType $longBonus): ?MatchCombo
    {
        if (count($run) === 3) {
            return new MatchCombo($run, BonusType::None);
        }

        if (count($run) > 3) {
            return new MatchCombo($run, $longBonus);
        }

        return null;
    }
}
