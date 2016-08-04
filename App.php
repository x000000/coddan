<?php

require_once 'Db.php';

class App
{

	/**
	 * @var Db
	 */
	private $_db;

	public function __construct($config)
	{
		set_error_handler([$this, 'handleError'], E_ALL);
		set_exception_handler([$this, 'handleException']);

		$this->initDb($config['db']);
	}

	private function initDb($config)
	{
		$config = array_merge([
			'dsn'        => null,
			'username'   => null,
			'password'   => null,
			'attributes' => null,
			'charset'    => 'utf8',
		], $config);

		$this->_db = new Db(
			$config['dsn'],
			$config['username'],
			$config['password'],
			$config['attributes'],
			$config['charset']
		);
		$this->_db->open();
	}

	public function start()
	{
		$columns = [
			'Continent'      => true,
			'Region'         => true,
			'Countries'      => false,
			'LifeExpectancy' => false,
			'Population'     => false,
			'Cities'         => false,
			'Languages'      => false,
		];
		$activeSort   = $this->getSort(array_keys($columns), isset($_GET['sort']) ? $_GET['sort'] : '', 'Continent.asc');
		$dataProvider = $this->getDataProvider($activeSort);

		$data = compact('dataProvider', 'columns', 'activeSort');
		echo $this->isAjax()
			? $this->renderFile('index.php', $data)
			: $this->render('index.php', $data);
	}

	public function isAjax()
	{
		return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
			strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
	}

	private function getSort($columns, $value, $default = null)
	{
		if (preg_match('#^(' . implode('|', $columns) . ')\.(asc|desc)$#', $value, $sort)) {
			return array_slice($sort, 1);
		}
		return $default ? $this->getSort($columns, $default) : $default;
	}

	private function getDataProvider($sort)
	{
		$sql = <<< SQL
			SELECT * FROM (
				SELECT
					`Continent`,
					`Region`,
					COUNT(*) AS `Countries`,
					AVG(`LifeExpectancy`) AS `LifeExpectancy`,
					SUM(`Population`) AS `Population`,
					`Cities`,
					`Languages`
				FROM `Country` `s`

				LEFT JOIN (
					SELECT COUNT(*) AS `Cities`, `CountryCode`
					FROM `City`
					GROUP BY `CountryCode`
				) `c` ON (`c`.`CountryCode` = `s`.`Code`)

				LEFT JOIN (
					SELECT COUNT(*) AS `Languages`, `CountryCode`
					FROM `CountryLanguage`
					GROUP BY `CountryCode`
				) `l` ON (`l`.`CountryCode` = `s`.`Code`)

				GROUP BY `Continent`, `Region`
			) `t` ORDER BY TRIM(`$sort[0]`) $sort[1];
SQL;
		// some wierd stuff with sorting alphabetically happens, so we trim for sure

		return $this->_db->fetchAll($sql);
	}

	public function handleError($errno, $errstr, $errfile, $errline, $context = null)
	{
		echo $this->render('error.php', ['message' => $errstr]);
		die();
	}

	public function handleException($e)
	{
		echo $this->render('error.php', ['message' => $e->getMessage()]);
		die();
	}

	private function render($view, $params = [])
	{
		$content = $this->renderFile($view, $params);
		return $this->renderFile("layout.php", array_merge($params, ['content' => $content]));
	}

	private function renderFile($file, $params = [])
	{
		ob_start();
		ob_implicit_flush(false);
		extract($params, EXTR_OVERWRITE);
		require("views/$file");
		return ob_get_clean();
	}

}