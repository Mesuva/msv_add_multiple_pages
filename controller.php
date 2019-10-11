<?php
// Author: Ryan Hewitt - http://www.mesuva.com.au
namespace Concrete\Package\MsvAddMultiplePages;
use Concrete\Core\Package\Package;
use Concrete\Core\Page\Single as SinglePage;

class Controller extends Package {

    protected $pkgHandle = 'msv_add_multiple_pages';
    protected $appVersionRequired = '8.0';
    protected $pkgVersion = '1.1';

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
}
