<?php
/**
 * Part of Admin project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Lyrasoft\Luna\Table;

use Lyrasoft\Luna\Helper\LunaHelper;

define('LUNA_TABLE_CATEGORIES', 'categories');
define('LUNA_TABLE_ARTICLES', 'articles');
define('LUNA_TABLE_TAGS', 'tags');
define('LUNA_TABLE_TAG_MAPS', 'tag_maps');
define('LUNA_TABLE_LANGUAGES', 'languages');
define('LUNA_TABLE_COMMENTS', 'comments');
define('LUNA_TABLE_MODULES', 'modules');
define('LUNA_TABLE_CONTACTS', 'contacts');
define('LUNA_TABLE_PAGES', 'pages');
define('LUNA_TABLE_CONFIGS', 'configs');
define('LUNA_TABLE_MENUS', 'menus');

/**
 * The Table class.
 *
 * @since  1.0
 */
interface LunaTable
{
    const CATEGORIES = LUNA_TABLE_CATEGORIES;

    const ARTICLES = LUNA_TABLE_ARTICLES;

    const TAGS = LUNA_TABLE_TAGS;

    const TAG_MAPS = LUNA_TABLE_TAG_MAPS;

    const LANGUAGES = LUNA_TABLE_LANGUAGES;

    const COMMENTS = LUNA_TABLE_COMMENTS;

    const MODULES = LUNA_TABLE_MODULES;

    const CONTACTS = LUNA_TABLE_CONTACTS;

    const PAGES = LUNA_TABLE_PAGES;

    const CONFIGS = LUNA_TABLE_CONFIGS;

    const MENUS = LUNA_TABLE_MENUS;

    // @muse-placeholder  db-table  Do not remove this.
}
