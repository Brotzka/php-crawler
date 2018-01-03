<?php
/**
 * Created by PhpStorm.
 * User: fabs
 * Date: 23.11.17
 * Time: 05:29
 */

Route::group(['middleware' => ['web']], function(){
	Route::group(['prefix' => 'crawler', 'namespace' => '\Brotzka\PhpCrawler\Controllers'], function(){
                Route::match(['post', 'get'], '/', 'CrawlController@index')->name('crawler.index');
                Route::get('/runUrlChecker', 'CrawlController@runUrlChecker')->name('crawler.runUrlChecker');
                
		Route::get('/test2', 'CrawlController@test2');
		Route::match(['post','get'],'/test1', 'CrawlController@test1');
	});
});