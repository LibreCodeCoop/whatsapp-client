<?php

use Facebook\WebDriver\Cookie;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

require 'vendor/autoload.php';

$host = 'http://selenium-hub:4444/wd/hub';
$desiredCapabilities = DesiredCapabilities::firefox();
$desiredCapabilities->setCapability('moz:firefoxOptions', ['args' => ['-headless']]);
$driver = RemoteWebDriver::create($host, $desiredCapabilities);

$driver->get('http://cadernos.ensp.fiocruz.br/csp1/');
print_r($driver->getCurrentURL());
print_r($driver->getPageSource());

$driver->findElement(WebDriverBy::name('sagas_login'))
    ->sendKeys('marciacp');
$driver->findElement(WebDriverBy::name('sagas_senha'))
    ->sendKeys('UMASENHACOMPLEXAAQUI')
    ->submit();

$driver->wait()->until(
    WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('conteudo'), 'marciacp')
);

print_r($driver->getCurrentURL());
print_r($driver->getPageSource());

$cookies = $driver->manage()->getCookies();
print_r($cookies);
print_r(serialize($cookies));

/*
// navigate to Selenium page on Wikipedia
$driver->get('https://en.wikipedia.org/wiki/Selenium_(software)');

// write 'PHP' in the search box
$driver->findElement(WebDriverBy::id('searchInput')) // find search input element
    ->sendKeys('PHP') // fill the search box
    ->submit(); // submit the whole form

// wait until 'PHP' is shown in the page heading element
$driver->wait()->until(
    WebDriverExpectedCondition::elementTextContains(WebDriverBy::id('firstHeading'), 'PHP')
);

// print title of the current page to output
echo "The title is '" . $driver->getTitle() . "'\n";

// print URL of current page to output
echo "The current URL is '" . $driver->getCurrentURL() . "'\n";

// find element of 'History' item in menu
$historyButton = $driver->findElement(
    WebDriverBy::cssSelector('#ca-history a')
);

// read text of the element and print it to output
echo "About to click to button with text: '" . $historyButton->getText() . "'\n";

// click the element to navigate to revision history page
$historyButton->click();

// wait until the target page is loaded
$driver->wait()->until(
    WebDriverExpectedCondition::titleContains('Revision history')
);

// print the title of the current page
echo "The title is '" . $driver->getTitle() . "'\n";

// print the URI of the current page

echo "The current URI is '" . $driver->getCurrentURL() . "'\n";

// delete all cookies
$driver->manage()->deleteAllCookies();

// add new cookie
$cookie = new Cookie('cookie_set_by_selenium', 'cookie_value');
$driver->manage()->addCookie($cookie);

// dump current cookies to output
$cookies = $driver->manage()->getCookies();
print_r($cookies);
*/

$driver->quit();