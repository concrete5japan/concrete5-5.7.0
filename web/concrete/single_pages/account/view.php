<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<script type="text/javascript">
$(function() {
	$('i.icon-question-sign').parent().tooltip();
});
</script>

<div class="row">
    <div class="col-sm-10 col-sm-offset-1">
        <h1 class="page-header"><?=t('Account')?></h1>
        <p><?=t('You are currently logged in as <strong>%s</strong>', $profile->getUserDisplayName())?>.</p>


        <? foreach($pages as $p) { ?>
            <hr/>
            <div>
                <a href="<?=$p->getCollectionLink()?>"><?=$p->getCollectionName()?></a>
                <?
                $description = $p->getCollectionDescription();
                if ($description) { ?>
                    <p><?=$description?></p>
                <? } ?>
            </div>
        <? } ?>


        <? if (Config::get('concrete.user.profiles_enabled')) { ?>
            <hr/>
            <div>
                <a href="<?=URL::to('/members/profile')?>"><?=t("View Public Profile")?></a>
                <p><?=t('View your public user profile and the information you are sharing.')?></p>
            </div>


        <? } ?>

    </div>
</div>
