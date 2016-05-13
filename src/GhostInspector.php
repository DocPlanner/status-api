<?php
/**
 * Author: gellu
 * Date: 13.05.2016 13:56
 */

class GhostInspector
{
	const DEBUG = false;

	private $_httpClient;

	public function __construct()
	{
		$this->_httpClient = new GuzzleHttp\Client();
	}

	public function getGeckoView()
	{
		$testResults = ['failed' => 0, 'failed screen' => 0, 'passed' => 0];

		$tests = $this->getTests();
		foreach($tests as $test)
		{
			if ($test['passing'] == '1')
			{
				$testResults['passed']++;
			}
			else
			{
				$testResults['failed']++;
			}

			if ($test['screenshotComparePassing'] != 1)
			{
				$testResults['failed screen']++;
			}
		}

		$output = [];
		foreach($testResults as $k => $v)
		{
			$output['item'][] = ['value' => $v, 'text' => $k];
		}

		echo json_encode($output);

	}

	private function getTests()
	{
		$response = $this->_httpClient->get('https://api.ghostinspector.com/v1/tests/?apiKey=' . Config::GHOST_INSPECTOR_API_KEY, ['debug' => self::DEBUG]);
		return json_decode($response->getBody()->getContents(), true)['data'];
	}
}