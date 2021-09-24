<?php defined('C5_EXECUTE') or die("Access Denied.");
$form = $app->make('helper/form');
$ci = $app->make('helper/concrete/ui');
?>

<?php if (isset($pageType)) { ?>
    <form method="post" action="<?= $view->action($pageType->getPageTypeHandle()) ?>">
        <fieldset>
            <?php if ($onepagetype) { ?>
                <legend><?php echo t('Configure and create pages'); ?></legend>
            <?php } else { ?>
                <legend><?php echo t('Step 2 - Configure and create pages'); ?></legend>
            <?php } ?>
            <div class="form-group">
                <label for="page_type" class="form-label"><?php echo t('Page Type'); ?></label>
                <p><em><?php echo $pageType->getPageTypeDisplayName(); ?></em>

                    <?php if(!$onepagetype) { ?>
                        - <a href="<?=$view->url('/dashboard/sitemap/add_multiple_pages');?>"><?=t('change')?></a>
                    <?php } ?>
                </p>
            </div>

            <div class="form-group">
                <label for="parent_page" class="form-label"><?php echo t('Parent Page'); ?></label>
                <?php $pageSelector = Loader::helper('form/page_selector');
                echo $pageSelector->selectPage('parent_page', ($emptysite ? \Concrete\Core\Page::getHomePageID() : null));
                ?>
            </div>

            <div class="form-group">
                <label for="page_template" class="form-label"><?php echo t('Page Template'); ?></label>
                <?php echo $form->select('page_template', $pageTemplates) ?>
            </div>

            <div class="form-group">
                <label for="pagenames" class="form-label"><?php echo t('Page Names (enter one per line)'); ?></label>
                <?php echo $form->textarea('pagenames', '', array('rows' => 6, 'class' => 'form-control', 'placeholder' => t('Page names, one per line'))) ?>
                <div class="help-block"><?=t('To create nested pages, indent child pages using dashes.');?></div>
            </div>
        </fieldset>

        <?php
        ob_start();
        $pageType->renderComposerOutputForm(null, $parent);
        $output = trim(ob_get_clean());
        $haslabel = (strpos($output, 'label') !== false);
        ?>

        <?php if (strpos($output, 'label') !== false) { ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><?php echo t('Page Defaults'); ?></h3>
                    <p><?= t('Values entered below will be applied to all new pages'); ?></p>
                </div>

                <div class="panel-body">
                    <div id="defaults" style="display: none">
                        <?php echo $output; ?>
                    </div>
                </div>
            </div>
        <?php } ?>

        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <?php if (!$onepagetype) { ?>
                    <?php echo $ci->button(t('Select Page Type'), $view->url('/dashboard/sitemap/add_multiple_pages'), 'left'); ?>
                <?php } ?>
                <button class="pull-right float-right float-end btn btn-primary"
                        type="submit"><?php echo t('Create Pages') ?></button>
            </div>
        </div>

    </form>

    <script>
        $(document).ready(function () {
            $("div[data-composer-field='name'],div[data-composer-field='page_template']").parent().remove();
            $("div[data-composer-field='url_slug']").remove();
            $('#defaults').show();
        });
    </script>
<?php } else { ?>
    <fieldset>
        <legend><?php echo t('Step 1 - Select Page Type'); ?></legend>
        <table class="table table-striped">
            <?php foreach ($pageTypes as $key => $pagetype) { ?>
                <tr>
                    <td>
                        <a href="<?php echo $this->url('/dashboard/sitemap/add_multiple_pages', $key); ?>"><?php echo $pagetype->getPageTypeDisplayName(); ?></a>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </fieldset>
<?php } ?>

<?php
Core::make('help')->display('<strong>'.t('Nested Pages').'</strong><br />' . t('To create nested pages, indent child page names using dashes, e.g. <br /><br />
<pre>Page one
- Page two
- Page three
-- Page four
- Page five
Page six</pre>
<br />
produces a site structure of:<br /><br />
<ul>
    <li>Page one
        <ul>
            <li>Page two</li>
            <li>Page three
                <ul>
                    <li>Page four</li>
                </ul>
            </li>
            <li>Page five</li>
        </ul>
    </li>
    <li>Page six</li>
</ul>
') . '<br /><strong>' .t('Page defaults').'</strong><br />'.
    t('The page default fields available for the selected Page Type are those configured for Composer editing. These can be modifed within the \'Edit Form\' section of Pages & Themes -> Page Types.')

);
?>
