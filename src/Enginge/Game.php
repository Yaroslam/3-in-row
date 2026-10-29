<?php

namespace Engine;

use Board\Board;
use Board\Cell;
use Engine\IO\GameRender;
use Engine\IO\UserInput;
use Engine\Match\MatchFinder;
use Engine\Stats\ScoreCounter;
use Engine\Stats\StatsCollector;
use Engine\Match\BonusType;
use Engine\Match\MatchClearResult;

class Game
{
    private Board $board;
    private GameState $state = GameState::Preparing;
    private bool $statsRendered = false;

    public function __construct(
        private GameRender $gameRender = new GameRender(),
        private UserInput $userInput = new UserInput(),
        private ScoreCounter $scoreCounter = new ScoreCounter(),
        private StatsCollector $statsCollector = new StatsCollector(),
        private MatchFinder $matchFinder = new MatchFinder()
    ){
        $this->board = Board::BoardOfN(8);
    }

    public function run(): void
    {
        while ($this->state !== GameState::Exited) {
            if ($this->state === GameState::Preparing) {
                $this->resolveBoard(false);
                $this->state = $this->IsGameOver() ? GameState::GameOver : GameState::WaitingForMove;
            }
            $this->renderGame();
            if ($this->state === GameState::GameOver) {
                $this->renderStats();
                echo "Ходов нет. Введите restart или exit.\n";
            }
            $this->processMove();
        }
    }

    public function IsGameOver(): bool
    {
        return !$this->board->hasMove();
    }

    public function getState(): GameState
    {
        return $this->state;
    }

    public function renderGame(): void
    {
        $this->gameRender->RenderGame($this->board);
        $this->gameRender->RenderScore($this->scoreCounter->getScore());
        $this->gameRender->RenderUserInput();
        sleep(1);
    }

    public function processMove(): void
    {
        $move = $this->userInput->ReadInput();
        if (!$this->userInput->ValidateInput($move)) {
            echo "Некорректный ввод.\n";
            return;
        }
        if ($move === 'exit') {
            $this->renderStats();
            $this->state = GameState::Exited;
            return;
        }
        if ($move === 'restart') {
            $this->restartGame();
            return;
        }
        if ($this->state === GameState::Preparing) {
            $this->resolveBoard(false);
            $this->state = $this->IsGameOver() ? GameState::GameOver : GameState::WaitingForMove;
        }
        if ($this->state !== GameState::WaitingForMove) {
            return;
        }
        [$row1, $col1, $row2, $col2] = array_map('intval', preg_split('/\s+/', trim($move)));
        $from = new Cell($row1, $col1);
        $to = new Cell($row2, $col2);
        if (!$this->matchFinder->isMatchAfterSwap($this->board->getBoardState(), $from, $to)) {
            echo "Ход не создаёт комбинацию.\n";
            return;
        }
        $this->board->processMove($from, $to);
        $this->state = GameState::Resolving;
        $scoreBefore = $this->scoreCounter->getScore();
        $this->resolveBoard();
        $this->statsCollector->addMoveHistory($from, $to, $this->scoreCounter->getScore() - $scoreBefore);
        $this->state = $this->IsGameOver() ? GameState::GameOver : GameState::WaitingForMove;
    }

    private function resolveBoard(bool $countScore = true): void
    {
        while ($matches = $this->matchFinder->findAllMatches($this->board->getBoardState())) {
            $toClear = $this->matchFinder->resolveMatches($matches, $this->board->getBoardState());
            if ($countScore) {
                $scoreBefore = $this->scoreCounter->getScore();
                $this->scoreCounter->countMatch(new MatchClearResult(count($toClear), BonusType::None));
                foreach ($matches as $match) {
                    $this->scoreCounter->countBonus(new MatchClearResult(count($toClear), $match->bonusType));
                    $this->statsCollector->addBonus($match->bonusType);
                }
                $this->statsCollector->addCellsClear(count($toClear));
                $this->statsCollector->addScore($this->scoreCounter->getScore() - $scoreBefore);
            }
            foreach ($toClear as $cell) {
                $this->board->clearCell($cell);
            }
            $this->renderGame();
            $cells = $this->board->applyGravity();
            $this->renderGame();
            foreach ($cells as $cell) {
                $this->board->fillCell($cell);
            }
        }
    }

    public function restartGame(): self
    {
        $this->renderStats();
        $this->board = Board::BoardOfN(8);
        $this->scoreCounter = new ScoreCounter();
        $this->statsCollector = new StatsCollector();
        $this->statsRendered = false;
        $this->state = GameState::Preparing;
        return $this;
    }

    private function renderStats(): void
    {
        if ($this->statsRendered) {
            return;
        }

        $this->gameRender->RenderStats($this->statsCollector);
        $this->statsRendered = true;
    }
}
