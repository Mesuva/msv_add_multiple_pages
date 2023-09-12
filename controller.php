<?php
// Author: Ryan Hewitt - http://www.mesuva.com.au
namespace Concrete\Package\MsvAddMultiplePages;
use Concrete\Core\Package\Package;
use Concrete\Core\Page\Single as SinglePage;

class Controller extends Package {

    protected $pkgHandle = 'msv_add_multiple_pages';
    protected $appVersionRequired = '8.0';
    protected $pkgVersion = '1.2.2';

    public function getPackageDescription() {
        return t("Add multiple pages to your site at a time");
    }

    public function getPackageName() {
        return t("Add Multiple Pages");
    }

    public function install() {
        $pkg = parent::install();
        SinglePage::add('/dashboard/sitemap/add_multiple_pages',$pkg);
    }

    public function on_start() {
        $this->app->make('help/dashboard')->registerMessageString('/dashboard/sitemap/add_multiple_pages', self::returnHelp());
    }

    public static function returnHelp(){
        return '<strong>'.t('Nested Pages').'</strong><br />' . t('To create nested pages, indent child page names using dashes, e.g. <br /><br />
<pre>Page one
- Page two
- Page three
-- Page four
- Page five
Page six</pre>
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
            t('The page default fields available for the selected Page Type are those configured for Composer editing. These can be modifed within the \'Edit Form\' section of Pages & Themes -> Page Types.');

    }
}
