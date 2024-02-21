<?php

/** @var View $this */
/** @var string $content */

use yii\web\View;

$this->beginPage();

?>

<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="X-UA-Compatible" content="IE=Edge">
        <title>Test</title>
        <?php $this->head() ?>
    </head>
    <body>
        <?php $this->beginBody() ?>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <?= $content ?>
                </div>
            </div>
        </div>
        <div class="test" style="display: none;">
            hidden
        </div>
        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>
