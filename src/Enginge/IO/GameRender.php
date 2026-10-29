<?php

namespace Engine\IO;

use Board\Board;
use Engine\Stats\StatsCollector;

class GameRender
{
    public function RenderStats(StatsCollector $statsCollector): void
    {
        $stats = $statsCollector->getData();

        echo "\nСтатистика игрока:\n";
        echo "Очки: {$stats['score']}\n";
        echo "Количество ходов: " . count($stats['moveHistory']) . "\n";
        echo "Бонусы: {$stats['bonusCount']}\n";
        echo "Удалено ячеек: {$stats['cellsClearCount']}\n";
        echo "История ходов:\n";
        if ($stats['moveHistory'] === []) {
            echo "Нет ходов.\n";
        }
        foreach ($stats['moveHistory'] as $index => $move) {
            $number = $index + 1;
            [$fromRow, $fromCol] = $move['from'];
            [$toRow, $toCol] = $move['to'];
            echo "$number. ($fromRow, $fromCol) -> ($toRow, $toCol): {$move['score']} очков\n";
        }
    }

    public function RenderGame(Board $board): void
    {
        $board->draw();
    }

    public function RenderUserInput(): void
    {
        echo "\nВведите координаты двух соседних ячеек.\n";
        echo "Формат: строка1 столбец1 строка2 столбец2\n";
        echo "Пример: 1 1 1 2\n";
        echo "Для выхода введите exit.\n";
        echo "Для рестарта введите restart.\n";
    }

    /**
     * Выводит текущий счёт игрока.
     */
    public function RenderScore(int $score): void
    {
        echo "\nОчки игрока: $score\n";
    }
}
