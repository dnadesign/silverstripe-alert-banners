<?php

namespace DNADesign\AlertBanners\Config;

use SilverStripe\Core\Manifest\ModuleLoader;
use SilverStripe\Forms\HTMLEditor\HTMLEditorConfig;
use SilverStripe\TinyMCE\TinyMCEConfig;

class EditorConfig
{
    /**
     * Creates a new 'alert-banners' HTMLEditorConfig
     */
    public static function create()
    {
        $editor = HTMLEditorConfig::get('alert-banners');

        if ($editor instanceof TinyMCEConfig) {
            $editor->setOptions([
                'friendly_name' => 'Alert Banners Editor',
            ]);

            $manifest = ModuleLoader::inst()->getManifest();
            $tinymce = $manifest->getModule('silverstripe/htmleditor-tinymce');

            $editor->enablePlugins([
                'sslink' => $tinymce->getResource('client/dist/js/TinyMCE_sslink.js'),
                'sslinkexternal' => $tinymce->getResource('client/dist/js/TinyMCE_sslink-external.js'),
                'sslinkemail' => $tinymce->getResource('client/dist/js/TinyMCE_sslink-email.js'),
                'sslinkinternal' => $tinymce->getResource('client/dist/js/TinyMCE_sslink-internal.js'),
                'sslinkanchor' => $tinymce->getResource('client/dist/js/TinyMCE_sslink-anchor.js'),
                'contextmenu' => null,
                'image' => null,
            ])->setOption('contextmenu', 'sslink inserttable | cell row column deletetable');

            $editor->removeButtons(
                'alignleft',
                'aligncenter',
                'alignright',
                'alignjustify',
                'underline',
                'indent',
                'outdent',
                'bullist',
                'numlist',
                'formatselect',
                'paste',
                'pastetext',
                'table',
                'sslink'
            );

            $editor->addButtonsToLine(1, 'sslink', 'code');
        }
    }
}
