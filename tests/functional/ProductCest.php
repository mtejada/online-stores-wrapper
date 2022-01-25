<?php


class ProductCest {

    public function _before(FunctionalTester $I) {
        
    }

    public function _after(FunctionalTester $I) {
        
    }

    public function basicFilterTest(FunctionalTester $I) {

        $keyword = "car";

        $I->sendGET("/products?keywords=" . $keyword);

        $response = $I->grabResponse();
        $parsed = json_decode($response, true);

        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);


        $I->assertContains(strtoupper($keyword), strtoupper($parsed['data'][0]['title']), "Keyword");
    }
    public function changePageTest(FunctionalTester $I) {

        $keyword = "car";
        $page=2;

        $I->sendGET("/products?page=".$page."&keywords=" . $keyword);

        $response = $I->grabResponse();
        $parsed = json_decode($response, true);

        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);


        $I->assertEquals($page, $parsed['page'], "Page");
    }    
    public function changeLimitDataTest(FunctionalTester $I) {

        $keyword = "car";
        $limit=25;

        $I->sendGET("/products?limit=".$limit."&keywords=" . $keyword);

        $response = $I->grabResponse();
        $parsed = json_decode($response, true);

        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);


        $I->assertEquals($limit, $parsed['items_per_page'], "Limit");
    } 
    public function testSortTest(FunctionalTester $I) {

        $keyword = "car";
        $sort='by_price_asc';

        $I->sendGET("/products?limit=10&keywords=" . $keyword);
        
        $response = $I->grabResponse();
        $parsed = json_decode($response, true);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $oldId=$parsed['data'][0]['item_id'];

        $I->sendGET("/products?limit=10&order=".$sort."&keywords=" . $keyword);
        
        $response = $I->grabResponse();
        $parsed = json_decode($response, true);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
   
        $I->assertNotEquals($oldId, $parsed['data'][0]['item_id'], "Item id");
    }
    public function testAmountsTest(FunctionalTester $I) {

        $keyword = "car";
        $minAmount=250;
        $maxAmount=1500;

        $I->sendGET("/products?limit=10&order=by_price_asc&price_min=".$minAmount."&price_max=".$maxAmount."&keywords=" . $keyword);
        
        $response = $I->grabResponse();
        $parsed = json_decode($response, true);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);

   
        $I->assertGreaterOrEquals($minAmount, $parsed['data'][0]['price'], "min price");
        $I->assertLessOrEquals($maxAmount, end($parsed['data'])['price'], "max price");
    }
}
