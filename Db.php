<?php

class Db
{

	public $_dsn;
	public $_username;
	public $_password;
	public $_attributes;
	public $_charset;
	/**
	 * @var PDO
	 */
	public $_pdo;

	public function __construct($dsn, $username, $password, $attributes, $charset = null)
	{
		$this->_dsn        = $dsn;
		$this->_username   = $username;
		$this->_password   = $password;
		$this->_attributes = $attributes;
		$this->_charset    = $charset;
	}

	public function open()
	{
		if ($this->_pdo !== null) {
			return;
		}

		if (empty($this->_dsn)) {
			throw new \Exception('Connection::dsn cannot be empty.');
		}

		$this->_pdo = $this->createPdoInstance();
		$this->initConnection();
	}

	public function close()
	{
		if ($this->_pdo !== null) {
			$this->_pdo = null;
		}
	}

	protected function createPdoInstance()
	{
		return new PDO($this->_dsn, $this->_username, $this->_password, $this->_attributes);
	}

	protected function initConnection()
	{
		$this->_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$this->_pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
		if ($this->_charset !== null) {
			$this->_pdo->exec('SET NAMES ' . $this->_pdo->quote($this->_charset));
		}
	}


	public function fetchScalar($sql, $params = [], &$out = null) {
		$stmt = $this->_pdo->prepare($sql);
		try {
			if ($stmt->execute($params)) {
				if (null !== $out) {
					$out = $stmt->rowCount();
				}
				return $stmt->fetchColumn();
			} else {
				if (null !== $out) {
					$out = $stmt->errorInfo();
				}
				return false;
			}
		}
		catch (\Exception $e) {
			if (null !== $out) {
				$out = $e->getMessage();
			}
			return false;
		}
	}

	public function fetchOne($sql, $params = [], &$out = null) {
		$stmt = $this->_pdo->prepare($sql);
		try {
			if ($stmt->execute($params)) {
				if (null !== $out) {
					$out = $stmt->rowCount();
				}
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
				return $row === false ? null : $row;
			} else {
				if (null !== $out) {
					$out = $stmt->errorInfo();
				}
				return false;
			}
		}
		catch (\Exception $e) {
			if (null !== $out) {
				$out = $e->getMessage();
			}
			return false;
		}
	}

	public function fetchAll($sql, $params = [], &$out = null) {
		$stmt = $this->_pdo->prepare($sql);
		try {
			if ($stmt->execute($params)) {
				if (null !== $out) {
					$out = $stmt->rowCount();
				}
				return $stmt->fetchAll(PDO::FETCH_ASSOC);
			} else {
				if (null !== $out) {
					$out = $stmt->errorInfo();
				}
				return false;
			}
		}
		catch (\Exception $e) {
			if (null !== $out) {
				$out = $e->getMessage();
			}
			return false;
		}
	}

	public function fetchColumn($sql, $params = [], &$out = null) {
		if (false === $rows = $this->fetchAll($sql, $params, $out)) {
			return false;
		}

		foreach ($rows as &$row) {
			$row = reset($row);
		}

		return $rows;
	}

	public function execute($sql, $params = [], &$out = null) {
		$stmt = $this->_pdo->prepare($sql);
		try {
			if ($result = $stmt->execute($params)) {
				if (null !== $out) {
					$out = $stmt->rowCount();
				}
			} else {
				if (null !== $out) {
					$out = $stmt->errorInfo();
				}
			}
		}
		catch (\Exception $e) {
			if (null !== $out) {
				$out = $e->getMessage();
			}
			return false;
		}
		return $result;
	}

}