<?php
/**
 * This file contains the class::Frontend to create and print the HTML-Page.
 * The class::Frontend is the main class of this project.
 * It will include all needed classes.
 * For using it's enough to include this class.
 */
/**
 * Class: Frontend
 * 
 * @author Hannes Christiansen <mail@laufhannes.de>
 * @version 1.0
 * @uses class::Mysql
 * @uses class::Error
 * //@uses class::Config
 * @uses class::Icon
 * @uses class::Ajax
 * @uses class::Plugin
 * @uses class::PluginPanel
 * @uses class::PluginStat
 * //@uses class::PluginDraw
 * @uses class::Training
 * @uses class::DataBrowser
 * @uses class::Dataset
 * @uses class::Draw
 *
 * Last modified 2011/03/14 16:00 by Hannes Christiansen
 */
//error_reporting(E_ALL);
class Frontend {
	/**
	 * Global array (should be deleted later on)
	 * @var array
	 */
	public $global;

	/**
	 * Boolean flag if it was an Ajax-request
	 * @var bool
	 */
	private $ajax_request;

	/**
	 * Called filename
	 * @var string
	 */
	private $file;

	/**
	 * Constructor for frontend
	 * @param bool $ajax_request
	 * @param string $file
	 */
	public function __construct($ajax_request = false, $file = __FILE__) {
		global $global;

		$this->file = $file;
		$this->global = $global;
		$this->ajax_request = $ajax_request;

		$this->initConsts();
		$this->initVars();
		$this->initErrorHandling();
		$this->initMySql();
		$this->initConfigConsts();
		$this->initRequiredFiles();
	}

	/**
	 * Destructer, closes mysql-connection and prints error-log if set (hopefully without another call?)
	 */
	public function __destruct() {}

	/**
	 * Calls the destructer
	 */
	public function close() {
		$this->__destruct();
	}

	/**
	 * Init constants
	 */
	private function initConsts() {
		define('FRONTEND_PATH', dirname(__FILE__).'/');
		define('RUNALYZE_VERSION', '0.5');
		define('RUNALYZE_DEBUG', true);
		define('INFINITY', PHP_INT_MAX);
		define('DAY_IN_S', 86400);
		define('YEAR', date("Y"));
		define('CUT_LENGTH', 29);
		define('NL', "\n");
	}

	/**
	 * Init class-variables
	 */
	private function initVars() {
		if (!is_bool($this->ajax_request)) {
			Error::getInstance()->add('WARNING',' First argument for class::Frontend__construct() is expected to be boolean.');
			$this->ajax_request = true;
		}
	}

	/**
	 * Include class::Error and and initialise it
	 */
	private function initErrorHandling() {
		require_once(FRONTEND_PATH.'class.Error.php');
		Error::init();
	}

	/**
	 * Include class::Mysql and connect to database
	 */
	private function initMySql() {
		require_once(FRONTEND_PATH.'class.Mysql.php');
		require_once(FRONTEND_PATH.'config.inc.php');
		Mysql::connect($host, $username, $password, $database);
		unset($host, $username, $password, $database);
	}

	/**
	 * Define all CONFIG_CONSTS
	 */
	private function initConfigConsts() {
		Error::getInstance()->addTodo('Set up new class::Config');
		//require_once(FRONTEND_PATH.'class.Config.php');

		$config = Mysql::getInstance()->fetch('SELECT * FROM `'.PREFIX.'config` LIMIT 1');
		foreach ($config as $key => $value)
			define('CONFIG_'.strtoupper($key), $value);
		unset($config);
	}

	/**
	 * Include alle required files
	 */
	private function initRequiredFiles() {
		global $global;

		require_once(FRONTEND_PATH.'class.Ajax.php');
		require_once(FRONTEND_PATH.'class.Helper.php');
		require_once(FRONTEND_PATH.'class.Icon.php');
		require_once(FRONTEND_PATH.'class.Training.php');
		require_once(FRONTEND_PATH.'class.DataBrowser.php');
		require_once(FRONTEND_PATH.'class.Dataset.php');
		require_once(FRONTEND_PATH.'class.Plugin.php');
		require_once(FRONTEND_PATH.'class.PluginPanel.php');
		require_once(FRONTEND_PATH.'class.PluginStat.php');
		//require_once(FRONTEND_PATH.'class.PluginDraw.php');
		//require_once(FRONTEND_PATH.'class.PluginTool.php');
		require_once(FRONTEND_PATH.'class.Draw.php');
	}

	/**
	 * Function to display the HTML-Header
	 */
	public function displayHeader() {
		header('Content-type: text/html; charset=ISO-8859-1');

		if (!$this->ajax_request)
			include('tpl/tpl.Frontend.header.php');
	}

	/**
	 * Function to display the HTML-Footer
	 */
	public function displayFooter() {
		if (RUNALYZE_DEBUG)
			include('tpl/tpl.Frontend.debug.php');

		if (!$this->ajax_request)
			include('tpl/tpl.Frontend.footer.php');
	}

	/**
	 * Display the panels for the right side
	 */
	public function displayPanels() {
		$panels = Mysql::getInstance()->fetchAsArray('SELECT * FROM `'.PREFIX.'plugin` WHERE `type`="panel" AND `active`>0 ORDER BY `order` ASC');
		foreach ($panels as $i => $panel) {
			$Panel = Plugin::getInstanceFor($panel['key']);
			$Panel->display();
		}
	}
}
?>