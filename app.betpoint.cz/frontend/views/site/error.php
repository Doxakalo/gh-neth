<?php

/** @var yii\web\View $this */
/** @var string $name */
/** @var string $message */
/** @var Exception $exception */

use yii\helpers\Html;

$this->title = $name;
?>

<div id="root">
    <header>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="header-inner">
                        <a class="brand" href="/" data-discover="true">
                            <img src="/images/betpoint-logo.svg" alt="<?= Yii::$app->name ?>">
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <main>
        <div class="page">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h1><?= Html::encode($this->title) ?></h1>
                        <p>
                            <?= nl2br(Html::encode($message)) ?>
                        </p>
                        <p>Please check the URL or return to the <a href="/" data-discover="true">Home page</a>.</p>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <footer class="main-footer bg-dark ">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="footer-inner">
                        <p class="copyright">&copy; <?= date('Y') ?> <?= Yii::$app->name ?></p>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</div>
