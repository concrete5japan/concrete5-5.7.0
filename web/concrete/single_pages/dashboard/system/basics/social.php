<? defined('C5_EXECUTE') or die("Access Denied.");?>

<? if ($controller->getTask() == 'add'
    || $controller->getTask() == 'add_link'
    || $controller->getTask() == 'edit'
    || $controller->getTask() == 'edit_link'
    || $controller->getTask() == 'delete_link') {

    $url = '';
    $ssHandle = '';
    $action = $view->action('add_link');
    $token = 'add_link';
    if (is_object($link)) {
        $url = $link->getURL();
        $ssHandle = $link->getServiceHandle();
        $action = $view->action('edit_link', $link->getID());
        $token = 'edit_link';
    }
    ?>

    <? if (is_object($link)) { ?>
        <div class="ccm-dashboard-header-buttons">
            <button data-dialog="delete-link" class="btn btn-danger"><?php echo t("Delete Link")?></button>
        </div>

    <div style="display: none">
        <div id="ccm-dialog-delete-social-link" class="ccm-ui">
            <form method="post" class="form-stacked" action="<?=$view->action('delete_link')?>">
                <?=Loader::helper("validation/token")->output('delete_link')?>
                <input type="hidden" name="slID" value="<?=$link->getID()?>" />
                <p><?=t('Are you sure? This action cannot be undone.')?></p>
            </form>
            <div class="dialog-buttons">
                <button class="btn btn-default pull-left" onclick="jQuery.fn.dialog.closeTop()"><?=t('Cancel')?></button>
                <button class="btn btn-danger pull-right" onclick="$('#ccm-dialog-delete-social-link form').submit()"><?=t('Delete Link')?></button>
            </div>
        </div>
    </div>

    <? } ?>

    <script type="text/javascript">
        $(function() {
            $('button[data-dialog=delete-link]').on('click', function() {
                jQuery.fn.dialog.open({
                    element: '#ccm-dialog-delete-social-link',
                    modal: true,
                    width: 320,
                    title: '<?=t("Delete Social Link")?>',
                    height: 'auto'
                });
            });
        });
    </script>

    <form method="post" class="form-horizontal" action="<?=$action?>">
        <?=$this->controller->token->output($token)?>

        <div class="form-group">
            <?=$form->label('ssHandle', t('Service'), array('class' => 'col-md-2'))?>
            <div class="col-md-5">
            <?=$form->select('ssHandle', $services, $ssHandle)?>
            </div>
        </div>

        <div class="form-group">
            <?=$form->label('url', t('URL'), array('class' => 'col-md-2'))?>
            <div class="col-md-5">
                <?=$form->text('url', $url)?>
            </div>
        </div>

        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <a href="<?=URL::to('/dashboard/system/basics/social')?>" class="btn btn-default pull-left"><?=t("Cancel")?></a>
                <button class="pull-right btn btn-success" type="submit" ><?=t('Add')?></button>
            </div>
        </div>

    </form>
<? } else { ?>


    <div class="ccm-dashboard-header-buttons">
        <a href="<?php echo View::url('/dashboard/system/basics/social', 'add')?>" class="btn btn-primary"><?php echo t("Add Link")?></a>
    </div>


    <? if (count($links) > 0) { ?>
        <div class="col-md-8">
        <table class="table table-striped">
        <? foreach($links as $link) {
            $service = $link->getServiceObject(); ?>
        <tr>
            <td style="width: 48px"><?=$service->getServiceIconHTML()?></td>
            <td><a href="<?=$view->action('edit', $link->getID())?>"><?=$service->getName()?></a></td>
            <td><?=$link->getURL()?></td>
        </tr>
        <? } ?>
        </table>
        </div>

    <? } else { ?>
        <p><?=t("You have not added any social links.")?></p>
    <? } ?>


<? } ?>