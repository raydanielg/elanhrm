<?php

namespace App\Observers\SuperAdmin;

use App\Models\SuperAdmin\FooterMenu;

class FooterMenuObserver
{

    public function created(FooterMenu $footerMenu)
    {
        $this->createDuplicateForOtherLanguage($footerMenu);
    }

    public function createDuplicateForOtherLanguage(FooterMenu $footerMenu)
    {
        foreach (language_setting() as $language) {
            if ($language->id != $footerMenu->language_setting_id) {
                if (!FooterMenu::where('language_setting_id', $language->id)->where('slug', $footerMenu->slug)->exists()) {
                    $this->createFooterMenu($footerMenu, $language->id);
                }
            }
        }
    }

    public function createFooterMenu(FooterMenu $footerMenu, $languageId)
    {
        $newMenu = new FooterMenu();
        $newMenu->name = $footerMenu->name;
        $newMenu->slug = $footerMenu->slug;
        $newMenu->description = $footerMenu->description;
        $newMenu->video_link = $footerMenu->video_link;
        $newMenu->video_embed = $footerMenu->video_embed;
        $newMenu->file_name = $footerMenu->file_name;
        $newMenu->hash_name = $footerMenu->hash_name;
        $newMenu->external_link = $footerMenu->external_link;
        $newMenu->type = $footerMenu->type;
        $newMenu->status = $footerMenu->status;
        $newMenu->language_setting_id = $languageId;
        $newMenu->private = $footerMenu->private;
        $newMenu->saveQuietly();
    }

    public function deleting(FooterMenu $footerMenu)
    {
        FooterMenu::where('slug', $footerMenu->slug)->delete();
    }

}
