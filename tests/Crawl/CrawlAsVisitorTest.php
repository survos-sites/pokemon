<?php

namespace App\Tests\Crawl;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\TestWith;
use Survos\CrawlerBundle\Tests\BaseVisitLinksTest;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CrawlAsVisitorTest extends BaseVisitLinksTest
{
	#[TestDox('/$method $url ($route)')]
	#[TestWith(['', 'App\Entity\User', '/', 200])]
	#[TestWith(['', 'App\Entity\User', 'pokemon', 200])]
	#[TestWith(['', 'App\Entity\User', '/api/docs', 200])]
	#[TestWith(['', 'App\Entity\User', 'pokemon?page=1&sort%5Bid%5D=DESC', 200])]
	#[TestWith(['', 'App\Entity\User', 'pokemon?page=1&sort%5Bname%5D=DESC', 200])]
	#[TestWith(['', 'App\Entity\User', 'pokemon?page=1&sort%5Bowned%5D=DESC', 200])]
	#[TestWith(['', 'App\Entity\User', 'pokemon?page=1&sort%5BfetchStatusCode%5D=DESC', 200])]
	#[TestWith(['', 'App\Entity\User', 'pokemon?page=1&sort%5BdownloadStatusCode%5D=DESC', 200])]
	#[TestWith(['', 'App\Entity\User', 'pokemon?page=1&sort%5Bmarking%5D=DESC', 200])]
	#[TestWith(['', 'App\Entity\User', 'pokemon/1', 200])]
	#[TestWith(['', 'App\Entity\User', 'pokemon/2', 200])]
	#[TestWith(['', 'App\Entity\User', 'pokemon?page=1&sort%5Bid%5D=ASC', 200])]
	#[TestWith(['', 'App\Entity\User', 'pokemon/2?page=1&sort%5Bid%5D=DESC', 200])]
	#[TestWith(['', 'App\Entity\User', 'pokemon/1?page=1&sort%5Bid%5D=DESC', 200])]
	public function testRoute(string $username, string $userClassName, string $url, string|int|null $expected): void
	{
		parent::testWithLogin($username, $userClassName, $url, (int)$expected);
	}
}
