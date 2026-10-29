<?php

namespace Engine\IO;

class UserInput
{
    public function ReadInput(): string
    {
        while (true) {
            echo 'Введите координаты двух соседних ячеек ';

            $line = fgets(STDIN);
            if ($line === false) {
                return 'exit';
            }
            $input = trim($line);

            if ($input !== '') {
                return $input;
            }

            echo "Ошибка: ввод не может быть пустым.\n";
        }
    }

    public function ValidateInput(string $input): bool
    {
        if (in_array(trim($input), ['exit', 'restart'], true)) {
            return true;
        }

        $parts = preg_split('/\s+/', trim($input));

        if (count($parts) !== 4) {
            return false;
        }

        foreach ($parts as $part) {
            if (!ctype_digit($part)) {
                return false;
            }
        }

        [$row1, $col1, $row2, $col2] = array_map(
            'intval',
            $parts
        );

        foreach ([$row1, $col1, $row2, $col2] as $value) {
            if ($value < 0 || $value > 7) {
                return false;
            }
        }

        $distance = abs($row1 - $row2) + abs($col1 - $col2);

        return $distance === 1;
    }
}
