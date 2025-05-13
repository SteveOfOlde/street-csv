<?php

use StreetCsv\Config;
use StreetCsv\Parser;

require_once '../vendor/autoload.php';

$config = new Config();

$parser = new Parser(new Config());


$people = [];
if (!empty($_FILES)) {
    $filename = filter_var($_FILES["file"]["tmp_name"]);

    $row = -1;
    if (($handle = fopen($filename, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ",", '"', '\\')) !== FALSE) {
            $row++;

            if ($row === 0) {
                continue;
            }

            $people[] = $parser->parseEntry($data[0]);
        }
        fclose($handle);
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Street CSV File Upload</title>
    <link
            rel="stylesheet"
            href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css"
    >
    <style>
        form {
            max-width: 450px;
            padding: 40px;
        }
    </style>
</head>
<body>
<h1>Street CSV File Upload</h1>
<form method="post" enctype="multipart/form-data">
    <label for="file">Select a file:</label>
    <input type="file" id="file" name="file">
    <br><br>
    <input type="submit" value="Upload">
</form>

<?php if (!empty($people)) { ?>
    <table>
        <tbody>
        <?php foreach ($people as $persons) { ?>
            <tr>
                <td><?php foreach ($persons as $key => $person) {
                        if ($key > 0) {
                            echo ', ';
                        }
                        echo $person;
                    }
                    ?>
                </td>
                <td>
                    <pre><?php print_r($persons) ?></pre>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

<?php } ?>

</body>
</html>
