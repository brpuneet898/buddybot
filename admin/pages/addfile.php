<div class="p-5">

<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly 

$buddybot_checks = new BuddyBot\Admin\InitialChecks();

if ($buddybot_checks->hasErrors()) {
    return;
}

$mo_addfile_page = new \BuddyBot\Admin\Html\Views\AddFile();
$mo_addfile_page->getHtml();
$mo_addfile_page->pageJs();
?>

</div>