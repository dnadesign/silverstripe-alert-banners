<?php

namespace DNADesign\AlertBanners\Model;

use Page;
use SilverStripe\CMS\Model\RedirectorPage;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\Versioned\Versioned;

class AlertBanner extends DataObject
{
    /**
     * @var string
     */
    private static $table_name = 'AlertBanner';

    /**
     * @var array
     */
    private static $extensions = [
        Versioned::class,
    ];

    /**
     * @var array
     */
    private static $db = [
        'Title' => 'Varchar(255)',
        'Type' =>  'Enum("Info, Warning, Error")',
        'Content' => 'HTMLText',
        'DisplayFrom' => 'Datetime',
        'DisplayTo' => 'Datetime',
        'SortOrder' => 'Int',
    ];

    /**
     * @var array
     */
    private static $many_many = [
        'DisplayRules' => AlertDisplayRule::class,
    ];

    /**
     * @var array
     */
    private static $summary_fields = [
        'ID' => 'ID',
        'Title' => 'Title',
        'Created' => 'Created',
        'ValidDateRange' => 'Displayed',
        'Type' => 'Theme',
        'Status' => 'Status',
    ];

    /**
     * @var array
     */
    private static $searchable_fields = [
        'Title',
        'Type',
    ];

    /**
     * CMS Fields
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        // main tab
        $fields->removeByName('SortOrder');
        if ($this->ID) {
            $titleField = $fields->dataFieldByName('Title');
            $titleField->setDescription($this->getPreviewLink());
        }
        $contentField = $fields->dataFieldByName('Content');
        $contentField->setEditorConfig('alert-banners')->setRows(4);

        // display rules tab
        if ($this->ID) {
            $gridField = $fields->dataFieldByName('DisplayRules');
            $gridField->setDescription(
                '<p class="alert alert-primary" style="margin-top:20px;">
                    This alert banner will be displayed on all pages be default. You can add exclude/include rules for pages here.<br>
                    This alert banner will <strong>always show</strong> on pages that are added as an <strong>include</strong> rule.<br>
                    This alert banner will not be shown on pages (or any of their child pages) that are added as an <strong>exclude</strong> rule.
                </p>'
            );
        }
        return $fields;
    }

    /**
     * Finds a random page not in the exclusion list and returns the a preview link as HTML
     * @return string
     */
    public function getPreviewLink()
    {
        $excludeFilter = ['ClassName' => RedirectorPage::class];
        $excludeOnPages = $this->getArrayOfExcludedPageIDs();
        if (count($excludeOnPages)) {
            $excludeFilter['ID'] = $excludeOnPages;
        }
        $page = Page::get()->excludeAny($excludeFilter);
        $link = $page->count() > 0 ? $page->first()->AbsoluteLink() : null;
        $html = isset($link) ? '<a href="' . $link . '?stage=Stage" target="_blank">Preview</a>' : '';
        return DBField::create_field(DBHTMLText::class, $html);
    }

    /**
     * Summary Field - returns information about when the alert banner will be displayed
     * @return DBHTMLText
     */
    public function getValidDateRange()
    {
        $validDateRange = 'From: ' . $this->dbObject('DisplayFrom')->Nice();
        $validDateRange .= '';
        if (!$this->DisplayFrom) {
            $validDateRange = 'Start Date Required';
        } elseif ($this->DisplayTo) {
            $validDateRange .= '<br>To: ' . $this->dbObject('DisplayTo')->Nice();
        }
        return DBField::create_field(DBHTMLText::class, $validDateRange);
    }

    /**
     * Summary Field - returns current status of the alert banner
     * @return DBHTMLText
     */
    public function getStatus()
    {
        $status = '';
        $published = $this->isPublished();
        if (!$published) {
            $status = '<i class="font-icon-pencil btn--icon-md"></i>Draft';
        } else {
            $status =  '<i class="font-icon-check-mark-circle btn--icon-md"></i>Published';
        }
        return DBField::create_field(DBHTMLText::class, $status);
    }

    /**
     * Returns an array of page ID's for which the alert banner should be displayed on
     * @return array
     */
    public function getArrayOfIncludedPageIDs()
    {
        $ids = [];
        $includeRules = $this->DisplayRules()->filter(['Rule' => 'Include']);
        foreach ($includeRules as $includeRule) {
            $page = $includeRule->Page();
            $ids[] = $page->ID;
        }
        return $ids;
    }

    /**
     * Returns an array of page ID's for which the alert banner should NOT be displayed on
     * @return array
     */
    public function getArrayOfExcludedPageIDs()
    {
        $ids = [];
        $excludeRules = $this->DisplayRules()->filter(['Rule' => 'Exclude']);
        foreach ($excludeRules as $excludeRule) {
            $page = $excludeRule->Page();
            $ids[] = $page->ID;
            $ids = array_merge($ids, $page->getDescendantIDList());
        }
        return $ids;
    }

    /**
     * CSS modifier
     * @return string
     */
    public function Modifier()
    {
        return strtolower($this->Type);
    }
}
