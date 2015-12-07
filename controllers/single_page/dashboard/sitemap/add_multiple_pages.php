<?php

namespace Concrete\Package\MsvAddMultiplePages\Controller\SinglePage\Dashboard\Sitemap;

use \Concrete\Core\Page\Controller\DashboardPageController;
use Database;
use Config;
use File;
use PageTemplate;
use PageType;
use Page;

defined('C5_EXECUTE') or die("Access Denied.");
class AddMultiplePages extends DashboardPageController
{

    public function view($pagetypehandle = '', $success = 0) {

        $homepage =  Page::getByID(HOME_CID);
        $emptysite = ($homepage->getFirstChild('cDisplayOrder asc', true) === false);
        $this->set('emptysite', $emptysite);

        $typeList = PageType::getList();
        $pageTypesSelect = array();

        foreach($typeList as $_pagetype) {
            $pageTypesSelect[$_pagetype->getPageTypeHandle()] = $_pagetype;
        }

        $this->set('pageTypes', $pageTypesSelect);

        $onepagetype =  (count($pageTypesSelect) == 1);
        $this->set('onepagetype', $onepagetype);

        if ($pagetypehandle) {
            $pageType = \PageType::getByHandle($pagetypehandle);

            if ($pageType) {
                $pageTemplates = $pageType->getPageTypePageTemplateObjects();
                $pageTemplatesSelect = array();

                foreach($pageTemplates as $pt) {
                    $pageTemplatesSelect[$pt->getPageTemplateID()] = $pt->getPageTemplateDisplayName();
                }
                $this->set('pageTemplates', $pageTemplatesSelect);
                $this->set('pageType', $pageType);
            } else {
                $this->redirect('/dashboard/sitemap/add_multiple_pages/');
            }

            if ($success > 0) {
                $this->set('success', $success . ' ' . ($success > 1 ? t('pages created') : t('page created')));
            }

        } else {
            if ($onepagetype) {
                $this->redirect('/dashboard/sitemap/add_multiple_pages/' . array_pop($pageTypesSelect)->getPageTypeHandle());
            }
        }


        if ($this->post()) {
            $error = false;

            if ($this->post('parent_page') <= 0) {
                $this->error->add(t('A parent page must be selected'));
                $error = true;
            }

            if ($this->post('page_template') <= 0) {
                $this->error->add(t('A page template must be selected'));
                $error = true;
            }

            $pagenames = trim($this->post('pagenames'));

            if (!$pagenames) {
                $this->error->add(t('At least one page name must be entered'));
                $error = true;
            }

            if (substr($pagenames, 0, 1) == '-') {
                $this->error->add(t('The first page must not start with a dash'));
                $error = true;
            }

            if (!$error && $pagetypehandle ) {
                $numpages = 0;
                $pagenames = explode("\n", trim($this->post('pagenames')));
                $parentPage = \Page::getByID($this->post('parent_page'));
                $parentList = array($parentPage);
                $currentLevel = 0;
                $pageTemplate = \PageTemplate::getByID($this->post('page_template'));

                if (is_object($parentPage) && is_object($pageType) && is_object($pageTemplate)) {
                    foreach ($pagenames as $pagename) {
                        $pagename = trim($pagename);

                        preg_match('/^(-\s*)*/',$pagename, $matches);

                        if (!empty($matches)){
                            $depth = strlen(str_replace(' ', '', $matches[0]));
                            $pagename = ltrim($pagename, '- ');
                            $pagename = trim($pagename);

                            if ($depth > $currentLevel && isset($parentList[1])) {
                                $currentLevel++;
                            }

                            if ($depth < $currentLevel) {
                                $currentLevel = $depth;
                            }
                        } else {
                            $currentLevel = 0;
                        }

                        $entry = $parentList[$currentLevel]->add($pageType, array(
                            'cName' => $pagename
                        ), $pageTemplate);

                        $parentList[$currentLevel+1] = $entry;

                        $pageType->savePageTypeComposerForm($entry);
                        $entry->updateCollectionName($pagename);
                        $numpages++;
                    }
                }

                $this->redirect('/dashboard/sitemap/add_multiple_pages/' . $pagetypehandle . '/' . $numpages);
            }
        }
    }
}
?>
