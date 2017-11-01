<?php

/**
 * AP Computer Science Principles Final Project (2015)
 * @author Benjamin Huang
 */

/*
    Program Overview:
    WordSearch Program! Generates a word search that consists of words given in an array.

    Generation Process:
    1. Creates a virtual 'Parent Grid' that has all empty values but has all the 'box' positions set up.
    2. Taking each of the words, the program assigns each of them a position that they will be on the grid. Then several checks will be done to ensure that there are no abrupt ends like a long word hitting the left side, or words 'colliding' with each other that are not compatible (later you will see what I mean).
    3. Finally, all the remaining empty spaces will be filled with a random letter from the list of letters given.
*/

$letters = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S",
                 "T", "U", "V", "W", "X", "Y", "Z");

$words = array( // The list of words that will be put into the word search.
        "NEWYORK",
        "CHICAGO",
        "PARIS",
        "LONDON",
        "VIENNA",
        "LOSANGELES"
    );


/* The WordSearch class which has all the functions that create the final product. */
class WordSearch {
    // A log to keep track of what's going on.
    public $log = array();

    public $words;
    public $size;

    /* This function sets up the virtual grid of the word search. It does it simply by assigning an empty value to each "box" of the word search. */
    public function createGrid($size = 20) {
        $a = array();
        $count = 1;


        for($i = 0; $i < $size; $i++) {
            for($j = 0; $j < $size; $j++) {
                $a[$count] = array(
                    "row" => $i + 1,
                    "col" => $j + 1,
                    "letter" => ""); // "letter" => $letter_pool[rand(0, count($letter_pool) - 1)]);
                $count++;
            }
        }

        return $a;
    }

    /* This function takes the virtual grid created by the createGrid() function and creates the HTML layout in a table for it so it actually looks like a wordsearch. */
    // The grid is displayed in a TABLE.
    public function gridDisplay($grid) { // needs the grid from 'createGrid()'
        // build the display
        $dim = sqrt(count($grid)); // n x n
        $count = 1;
        $a = "<table id=\"wordsearch_tbl\" align=\"center\" border=\"1\" style=\"border-collapse: collapse; font-size: 16px;\">";

        for($i = 0; $i < $dim; $i++)
        {
            $a = $a."<tr id=\"row_".$i."\">";
            for($j = 0; $j < $dim; $j++)
            {
                $b = $grid[$count][letter];
                $count++;
                $a = $a."<td class=\"wordsearch_cell\" style=\"text-align: center;\">".$b."</td>";
            }

            $a = $a."</tr>";
        }

        $a = $a."</table>";

        array_push($this->log, "Grid created!");

        return $a;
    }

    /* Many steps are involved in this function. Several things need to be considered before a word can be properly placed in any spot. First we have to consider the fact that words cannot abruptly end when they touch any edge of the grid. Words that happen to have crossing paths have to share the same letter when they intersect. The following function will take care all of that. */
    public function assignPositions($grid, $words, $highlight = 0) {
        $n = sqrt(count($grid)); // size of each edge
        $words = array_map("strtoupper", $words); // Capitalize every single letter

        foreach($words as $word) {
            $collide;

            do {
                $collide = array();

                $split_word = str_split($word);
                $letters = count($split_word);
                $r = rand(1, count($grid)); // Selects the random spot the word is placed initially.
                while(!isset($grid[$r][letter])) {
                    $r = rand(1, count($grid));
                }

                // Select Direction
                /*
                    Directions:
                    0 = right; 1 = down; 2 = left; 3 = up
                    4 = down-right; 5 = down-left; 6 = up-left; 7 = up-right

                    Guide:
                    i = row; j = column
                    top bound: row 1; bottom bound: row $n
                    left bound: column 1; right bound: column $n
                */

                $dir_check_passed = false;
                $count = 0;

                while(!$dir_check_passed) { // Loops until the word passes the direction test.
                    global $dir;
                    $dir = rand(0, 7); // There are only 8 directions to choose from. Select a random direction to start with.

                    /* Checks the direction. I used a switch statement to handle the 8 cases/directions. Each direction has its own math involved. */
                    switch($dir) {
                        /*
                            $w (horizontal) is space available for word within bounds.
                            $x vertical
                        */
                        case 0:
                            /*
                                Right
                                Bounds: Right-most Column
                                Loop the amount of characters the word has by +1
                                    Check bounds every iteration
                                    If we hit the bound and we're not at the last character, choose different direction / starting point
                            */
                            $w = $n - $grid[$r][col] + 1;
                            $x = $letters;
                            break;
                        case 1: // down
                            $x = $n - $grid[$r][row] + 1;
                            $w = $letters;
                            break;
                        case 2: // left
                            $w = $grid[$r][col];
                            $x = $letters;
                            break;
                        case 3: // top
                            $x = $grid[$r][row];
                            $w = $letters;
                            break;
                        case 4: // down right
                            $w = $n - $grid[$r][col] + 1;
                            $x = $n - $grid[$r][row] + 1;
                            break;
                        case 5: // down left
                            $w = $grid[$r][col];
                            $x = $n - $grid[$r][row] + 1;
                            break;
                        case 6:
                            $w = $grid[$r][col];
                            $x = $grid[$r][row];
                            break;
                        case 7:
                            $w = $n - $grid[$r][col] + 1;
                            $x = $grid[$r][row];
                            break;
                    }

                    if($w >= $letters && $x >= $letters || $count == 50) {
                        $dir_check_passed = true;
                        array_push($this->log, "<div>$letters <b>$word</b>: box_num_$r, horiz_space_$w, vert_space_$x</div><div>Direction: $dir</div>");
                    } else {
                        array_push($this->log, "<div>New Direction: $dir</div>");
                        $count++;
                    }
                }

                /* Collision Check - Checks each letter of the word with whatever already exists in the space. If the values are not equal, then the entire process of selecting a position and direction will be redone until the collision check is passed. */
                if($dir_check_passed) {
                    global $inc; // globalize so we can use it outside later

                    switch($dir) {
                        case 0:
                            $inc = 1;
                            break;
                        case 1:
                            $inc = $n;
                            break;
                        case 2:
                            $inc = -1;
                            break;
                        case 3:
                            $inc = -1 * $n;
                            break;
                        case 4:
                            $inc = $n + 1;
                            break;
                        case 5:
                            $inc = $n - 1;
                            break;
                        case 6:
                            $inc = -1 * ($n + 1);
                            break;
                        case 7:
                            $inc = -1 * ($n - 1);
                            break;
                    }

                    for($i = 0; $i < $letters; $i++) {
                        $c = $r + ($inc * $i);

                        if($grid[$c][letter] == $split_word[$i] || $grid[$c][letter] == "") {
                            $collide[$i] = 0;
                        }

                        else {
                            $collide[$i] = 1;
                            array_push($this->log, "<div>Words collided! Redoing...</div>");
                        }
                    }
                }
            } while(in_array(1, $collide));

            /* After all checks are passed, the word is finally placed into the virtual grid. */
            for($i = 0; $i < $letters; $i++) {
                $c = $r + $inc * $i;
                if($highlight == "1") {
                    $grid[$c][letter] = "<span style=\"color: orange; font-weight: bold;\">".$split_word[$i]."</span>";
                }

                else {
                    $grid[$c][letter] = $split_word[$i];
                }
            }
        }

        return $grid;
    }

    /* Fills grid with random letters. */
    public function fillGrid($grid, $letter_pool) {
        for($i = 1; $i <= count($grid); $i++){
            if($grid[$i][letter] == "") {
                $grid[$i][letter] = $letter_pool[rand(0, count($letter_pool) - 1)];
            }
        }

        array_push($this->log, "Grid filled.");

        return $grid;
    }

    public function calculateSize($words) {
        // Sort array
        array_multisort(array_map("strlen", $words), $words);

        return sizeof($words) * 2;
    }

    public static function displayLog() {
        $inst = new self();

        array_push($inst->log, "Log started.");
        $inst->log = array_reverse($inst->log);

       foreach($inst->log as $entry) {
           echo "<div>".$entry."</div>";
       }
    }

    public function __construct() {
        $highlight = 0;
    }

    // Create a word search quickly
    public static function make($words, $letters, $highlight = 0) {
        $inst = new self();
        $grid = $inst->createGrid(30);
        $grid = $inst->assignPositions($grid, $words, $highlight);
        $grid = $inst->fillGrid($grid, $letters);
        echo $inst->gridDisplay($grid);
    }
}

/*
    Example of how the word search would be generated:
*/
/*
$wordsearch = new WordSearch;

// Initiate Parent Grid of dimensions 30x30
$grid = $wordsearch->createGrid(30);

// Rewrite Grid with each new $word.
$grid = $wordsearch->assignPositions($grid, $words); // Assign words
$grid = $wordsearch->fillGrid($grid, $letters); // Fill

echo $wordsearch->gridDisplay($grid); // Final Function.
*/

?>
