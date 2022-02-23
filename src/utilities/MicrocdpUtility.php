<?php
/**
 * MicroCDP plugin for Craft CMS 3.x
 *
 * Track user events and get user summary data
 *
 * @link      https://www.disposition.tools
 * @copyright Copyright (c) 2020 Disposition Tools
 */

namespace dispositiontools\microcdp\utilities;

use dispositiontools\microcdp\MicroCDP;
use dispositiontools\microcdp\assetbundles\microcdputility\MicrocdpUtilityAsset;

use Craft;
use craft\base\Utility;

/**
 * MicroCDP Utility
 *
 * Utility is the base class for classes representing Control Panel utilities.
 *
 * https://craftcms.com/docs/plugins/utilities
 *
 * @author    Disposition Tools
 * @package   MicroCDP
 * @since     1.0.0
 */
class MicrocdpUtility extends Utility
{
    // Static
    // =========================================================================

    /**
     * Returns the display name of this utility.
     *
     * @return string The display name of this utility.
     */
    public static function displayName(): string
    {
        return Craft::t('micro-cdp', 'Microcdp');
    }

    /**
     * Returns the utility’s unique identifier.
     *
     * The ID should be in `kebab-case`, as it will be visible in the URL (`admin/utilities/the-handle`).
     *
     * @return string
     */
    public static function id(): string
    {
        return 'microcdp-microcdp';
    }

    /**
     * Returns the path to the utility's SVG icon.
     *
     * @return string|null The path to the utility SVG icon
     */
    public static function iconPath()
    {
        return Craft::getAlias("@dispositiontools/microcdp/assetbundles/microcdputility/dist/img/Microcdp-icon.svg");
    }

    /**
     * Returns the number that should be shown in the utility’s nav item badge.
     *
     * If `0` is returned, no badge will be shown
     *
     * @return int
     */
    public static function badgeCount(): int
    {
        return 0;
    }

    /**
     * Returns the utility's content HTML.
     *
     * @return string
     */
    public static function contentHtml(): string
    {
        Craft::$app->getView()->registerAssetBundle(MicrocdpUtilityAsset::class);

        $someVar = 'Have a nice day!';
        return Craft::$app->getView()->renderTemplate(
            'micro-cdp/_components/utilities/Microcdp_content',
            [
                'someVar' => $someVar
            ]
        );
    }
}
