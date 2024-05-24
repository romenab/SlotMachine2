<?php

class CreateElement
{
    public string $name;
    public int $chance;
    public int $value;

    public function __construct(string $name, int $chance, int $value)
    {
        $this->name = $name;
        $this->chance = $chance;
        $this->value = $value;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getChance(): int
    {
        return $this->chance;
    }

    public function getValue(): int
    {
        return $this->value;
    }
}

class MakeBoard
{
    private array $elements;
    private int $rows;
    private int $columns;

    public function __construct(array $elements, int $rows, int $columns)
    {
        $this->elements = $elements;
        $this->rows = $rows;
        $this->columns = $columns;
    }

    public function makeBoard(): array
    {
        $board = [];
        for ($row = 0; $row < $this->rows; $row++) {
            for ($column = 0; $column < $this->columns; $column++) {
                foreach ($this->elements as $element) {
                    if (rand(1, 100) <= $element->chance) {
                        $board[$row][$column] = $element;
                        break;
                    }
                }
            }
        }
        return $board;
    }
}

class Game
{
    private array $conditions;
    private array $board;
    private int $tokens;
    private int $bet;
    private int $multiplier;
    private array $elements;

    public function __construct(array $conditions, array $elements, int $tokens, int $bet)
    {
        $this->conditions = $conditions;
        $this->board = [];
        $this->tokens = $tokens;
        $this->bet = $bet;
        $this->multiplier = $bet / 5;
        $this->elements = $elements;
    }


    private function displayBoard()
    {
        foreach ($this->board as $row) {
            foreach ($row as $cell) {
                echo "[" . $cell->getName() . "]";
            }
            echo PHP_EOL;
        }
    }

    private function checkWin(): int
    {
        $tokensWin = 0;
        foreach ($this->conditions as $condition) {
            $elementsInCondition = [];
            foreach ($condition as $coordinates) {
                [$row, $column] = $coordinates;
                $elementsInCondition[] = $this->board[$row][$column];
            }

            if (count(array_count_values(array_column($elementsInCondition, "name"))) === 1) {
                $tokensWin += $elementsInCondition[0]->getValue() * $this->multiplier;
            }
        }
        return $tokensWin;
    }

    public function playGame()
    {
        while ($this->tokens >= $this->bet) {
            $this->tokens -= $this->bet;
            $makeBoard = new MakeBoard($this->elements, 3, 4);
            $this->board = $makeBoard->makeBoard();
            $this->displayBoard();
            $win = $this->checkWin();
            $this->tokens += $win;

            echo "Your balance " . $this->tokens . PHP_EOL;
            if ($this->tokens == 0) {
                break;
            }
            $continue = trim(strtolower(readline("Continue (y/n): ")));
            if (strtolower($continue) !== "y") {
                break;
            }
        }
        exit("Thank you for playing!");
    }
}


$elements = [
    new CreateElement("A", 3, 100),
    new CreateElement("C", 28, 10),
    new CreateElement("*", 53, 10),
    new CreateElement("#", 93, 5),
    new CreateElement("B", 100, 50),
];
$conditions = [
    [
        [0, 0], [0, 1], [0, 2], [0, 3]
    ],
    [
        [1, 0], [1, 1], [1, 2], [1, 3]
    ],
    [
        [2, 0], [1, 1], [0, 2], [0, 3]
    ]
];

while (true) {
    $tokens = readline("Enter starting amount: ");
    if (!is_numeric($tokens) || $tokens < 1) {
        continue;
    }
    $bet = (int)readline("Enter BET amount (5, 10, 15, 20, 25): ");
    if (!in_array($bet, [5, 10, 15, 20, 25])) {
        continue;
    }
    break;
}

$game = new Game($conditions, [], $tokens, $bet);
$game->playGame();



