<div class="p-5">

<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

$buddybot_checks = new BuddyBot\Admin\InitialChecks();

if ($buddybot_checks->hasErrors()) {
    return;
}

$mo_playground_page = new \BuddyBot\Admin\Html\Views\Playground();
$mo_playground_page->getHtml();
$mo_playground_page->pageJs();
?>

</div>