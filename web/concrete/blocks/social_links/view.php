<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div id="ccm-block-social-links">
    <ul class="list-inline">
    <? foreach($links as $link) {
        $service = $link->getServiceObject();
        ?>
        <li><a href="<?=$link->getURL()?>"><?=$service->getServiceIconHTML()?></a></li>
    <? } ?>
    </ul>
</div>