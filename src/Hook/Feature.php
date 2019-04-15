<?php
/**
 * 2007-2019 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\Module\FacetedSearch\Hook;

use Db;
use Language;
use Tools;

class Feature extends AbstractHook
{
    const AVAILABLE_HOOKS = [
        'actionFeatureDelete',
        'displayFeatureForm',
        'displayFeaturePostProcess',
    ];

    /**
     * Hook after delete a feature
     *
     * @param array $params
     */
    public function actionFeatureDelete(array $params)
    {
        if (empty($params['id_feature'])) {
            return;
        }

        Db::getInstance()->execute(
            'DELETE FROM ' . _DB_PREFIX_ . 'layered_indexable_feature
            WHERE `id_feature` = ' . (int) $params['id_feature']
        );
        $this->module->invalidateLayeredFilterBlockCache();
    }


    /**
     * Hook post process feature
     *
     * @param array $params
     */
    public function displayFeaturePostProcess(array $params)
    {
        $this->module->checkLinksRewrite($params);
    }

    /**
     * Hook feature form
     *
     * @param array $params
     */
    public function displayFeatureForm(array $params)
    {
        $values = [];
        $isIndexable = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
            'SELECT `indexable`
            FROM ' . _DB_PREFIX_ . 'layered_indexable_feature
            WHERE `id_feature` = ' . (int) $params['id_feature']
        );

        // Request failed, force $isIndexable
        if ($isIndexable === false) {
            $isIndexable = true;
        }

        if ($result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT `url_name`, `meta_title`, `id_lang` FROM ' . _DB_PREFIX_ . 'layered_indexable_feature_lang_value
            WHERE `id_feature` = ' . (int) $params['id_feature']
        )) {
            foreach ($result as $data) {
                $values[$data['id_lang']] = ['url_name' => $data['url_name'], 'meta_title' => $data['meta_title']];
            }
        }

        $this->context->smarty->assign([
            'languages' => Language::getLanguages(false),
            'default_form_language' => (int) $this->context->controller->default_form_language,
            'values' => $values,
            'is_indexable' => (bool) $isIndexable,
        ]);

        return $this->module->render('feature_form.tpl');
    }
}