<?php
    require("generator.php");
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <meta name="author" content="Benjamin Huang" />
        <meta name="description" content="Word search generator: AP Computer Science Principles Final Project" />
        <link rel="stylesheet" type="text/css" href="main.css" />

        <title>Word Search by Benjamin Huang</title>
    </head>
    <body>
        <div id="header">Word Search!</div>
        <div id="canvas">
        <?php
            $wordsearch = new WordSearch;

            if(!isset($_POST["wordsearch_submit"])) {
            ?>
                <div id="form">
                    <form method="post" id="wordsearch" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
                        <div>Please kthe words you wish to be in the word search. Separate words by using a space " ".</div>
                        <div><input type="text" id="wordsearch_words" name="wordsearch_words" value="NEWYORK PARIS LONDON VIENNA SEOUL ANCHORAGE" style="width: 400px;" /></div>
                        <div><input type="checkbox" value="1" id="wordsearch_highlight" name="wordsearch_highlight" checked /><label for="wordsearch_highlight">Highlight words in the grid.</label></div>
                        <div><input type="submit" id="wordsearch_submit" name="wordsearch_submit" value="Create Word Search!" /></div>
                    </form>
                </div>
            <?php
            } else {
                if(ctype_alpha(str_replace(" ", "", $_POST["wordsearch_words"]))) {
                ?>
                <div id="wordsearch_grid">
                <?php
                    $words = $_POST["wordsearch_words"];
                    $words = explode(" ", $words);
                    echo "Your words: ";
                    foreach($words as $word) {
                        echo $word . " ";
                    }

                    /*$ws = new WordSearch;
                    $grid = $ws->createGrid(30);
                    $grid = $ws->assignPositions($grid, $words);
                    $grid = $ws->fillGrid($grid, $letters);
                    echo $ws->gridDisplay($grid);*/

                    // Quick create
                    WordSearch::make($words, $letters, $_POST["wordsearch_highlight"]);
                ?>
                </div>
                <!--<div id="log"><div>This log will keep track of the steps that the generator takes to create the word search.</div><div><?php WordSearch::displayLog(); ?></div></div>-->
                <?php
                } else {
                ?>
                    <div>Only letters allowed!</div> <div><a href="javascript:void(0);" onclick="window.history.back();">Go Back</a></div>
                <?php
                }
            }
        ?>
        </div>
    </body>
</html>
