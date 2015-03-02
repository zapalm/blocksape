<?php
/**
 * Sape block: module for PrestaShop 1.4-1.6
 *
 * @author zapalm <zapalm@ya.ru>
 * @copyright (c) 2013-2015, zapalm
 * @link http://prestashop.modulez.ru/en/ The module's homepage
 * @license http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 */

if (!defined('_PS_VERSION_'))
	exit;

class BlockSape extends Module
{
	private $conf_default = array(
		'BLOCKSAPE_USER' => '',
		'BLOCKSAPE_FORCE_SHOW' => 1,
		'BLOCKSAPE_FORCE_URI' => 0,
	);

	public function __construct()
	{
		$this->name = 'blocksape';
		$this->tab = 'advertising_marketing';
		$this->version = '1.1.1';
		$this->author = 'zapalm';
		$this->need_instance = 0;
		$this->ps_versions_compliancy = array('min' => '1.4.0.0', 'max' => '1.6.1.0');
		$this->bootstrap = false;
		
		parent::__construct();

		$this->displayName = $this->l('Sape block');
		$this->description = $this->l('Adds a block to display Sape advertisement links.');
	}

	public function install()
	{
		foreach ($this->conf_default as $c => $v)
			Configuration::updateValue($c, $v);
		
		return parent::install()
			&& $this->registerHook('header')
			&& $this->registerHook('footer')
			&& $this->registerHook('leftColumn')
			&& $this->registerHook('rightColumn');
	}

	public function uninstall()
	{
		foreach ($this->conf_default as $c => $v)
			Configuration::deleteByName($c);

		return parent::uninstall();
	}

	public function getContent()
	{
		$output = '<h2>'.$this->displayName.'</h2>';

		if (Tools::isSubmit('submit_save'))
		{
			$res = 1;
			foreach ($this->conf_default as $k => $v)
			{
				if ($k == 'BLOCKSAPE_USER')
					$res &= Configuration::updateValue($k, Tools::getValue($k));
				else
					$res &= Configuration::updateValue($k, (int)Tools::getValue($k));
			}

			$output .= $res ? $this->displayConfirmation($this->l('Settings updated')) : $this->displayError($this->l('Some setting not updated'));
		}

		$conf = Configuration::getMultiple(array_keys($this->conf_default));

		$output .= '
			<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
				<fieldset>
					<legend><img src="'._PS_ADMIN_IMG_.'cog.gif" />'.$this->l('Settings').'</legend>
					<label>'.$this->l('Your user ID').'</label>
					<div class="margin-form">
						<input type="text" size="40" name="BLOCKSAPE_USER" value="'.$conf['BLOCKSAPE_USER'].'" />
						<p class="clear">'.$this->l('Something like this: sadfsdfsdf7d76gf5ds5g6df7gdfg8dd.').'</p>
					</div>
					<label>'.$this->l('Force show a code').'</label>
					<div class="margin-form">
						<input type="checkbox" name="BLOCKSAPE_FORCE_SHOW" value="1" '.($conf['BLOCKSAPE_FORCE_SHOW'] ? 'checked="checked"' : '').'" />
						<p class="clear">'.$this->l('For the testing.').'</p>
					</div>
					<label>'.$this->l('Force page URI applying').'</label>
					<div class="margin-form">
						<input type="checkbox" name="BLOCKSAPE_FORCE_URI" value="1" '.($conf['BLOCKSAPE_FORCE_URI'] ? 'checked="checked"' : '').'" />
						<p class="clear">'.$this->l('When there is a problem of detecting pages URLs.').'</p>
					</div>
					<center><input type="submit" name="submit_save" value="'.$this->l('Save').'" class="button" /></center>
				</fieldset>
			</form>
			<br class="clear">
		';

		return $output;
	}

	public function hookHeader($params)
	{
		Tools::addCSS($this->_path.'blocksape.css', 'all');
	}

	public function hookLeftColumn($params)
	{
		global $smarty, $link, $product, $category;

		$conf = Configuration::getMultiple(array_keys($this->conf_default));

		if (!$conf['BLOCKSAPE_USER'])
			return null;

		if (!defined('_SAPE_USER'))
			define('_SAPE_USER', $conf['BLOCKSAPE_USER']);

		$sape_handler = $_SERVER['DOCUMENT_ROOT'].'/'._SAPE_USER.'/sape.php';
		if (!file_exists($sape_handler))
			return null;

		require_once $sape_handler;

		$options = array();
		$options['charset'] = 'UTF-8';
		$options['force_show_code'] = (bool)$conf['BLOCKSAPE_FORCE_SHOW'];

		if ($conf['BLOCKSAPE_FORCE_URI'])
		{
			// we are in a category or in a product page?
			if (!empty($product))
				$options['request_uri'] = $link->getProductLink($product->id, $product->link_rewrite);
			elseif (!empty($category))
				$options['request_uri'] = $link->getCategoryLink($category->id, $category->link_rewrite);
		}
		
		$sape = new SAPE_client($options);
		$ad_link = $sape->return_links();

		$smarty->assign('ad_link', $ad_link);

		return $this->display(__FILE__, 'blocksape.tpl');
	}

	public function hookRightColumn($params)
	{
		return $this->hookLeftColumn($params);
	}

	public function hookFooter($params)
	{
		global $smarty;

		$smarty->assign('footer', true);

		return $this->hookLeftColumn($params);
	}
}