<?php

namespace App\Service;

use Laudis\Neo4j\ClientBuilder;
use Laudis\Neo4j\Databags\Statement;
use Laudis\Neo4j\Contracts\ClientInterface;

class Neo4j 
{
    private static $client;
    /**
     * User: YunXi
     * Date: 2022-06-16 
     * Time: 15:47 
     * Notes: 获取neo4j连接
     * @return ClientInterface
     */
    public static function getClient() :ClientInterface
    {
     
        if (self::$client instanceof ClientInterface) {
            return self::$client;
        } else {
            $neo4jConfig = config('database.connections.neo4j');
            self::$client = ClientBuilder::create()
                ->withDriver('bolt', "bolt://{$neo4jConfig['username']}:{$neo4jConfig['password']}@{$neo4jConfig['host']}") // creates a bolt driver
                ->withDefaultDriver('bolt')
                ->build();
        }
        // 未完成数据初始化，先初始化数据
        if (!self::isInitData()) {
            self::initData(self::$client);
        }
        return self::$client;
    }

    /**
     * User: YunXi
     * Date: 2022-06-17 
     * Time: 09:26 
     * Notes: 初始化数据
     *
     * @param ClientInterface $client
     * @return boolean
     */
    private static function initData(ClientInterface $client) : bool
    {
        try {
            $loadFlights =  Statement::create("
                load csv from 'file:///flights.csv' as line
                create(:flights{id:line[0],from:line[1],to:line[2]})
            ");
            $loadAirports = Statement::create("
                load csv from 'file:///airports.csv' as line
                create(
                    :airports{
                        id:line[0],
                        name:line[1],
                        city:line[2],
                        country:line[3],
                        iata:line[4],
                        icao:line[5],
                        latitude:line[6],
                        longitude:line[7],
                        timezone:line[8],
                        dst:line[9],
                        tz:line[10]}
                )
            ");

            $relation = Statement::create("
                match(a1:airports),(f:flights),(a2:airports) where a1.iata=f.from and a2.iata=f.to
                create(a1)-[r:航班{id:f.id,from:f.from,to:f.to}]->(a2)
            ");

            $tsx = $client->beginTransaction(
                [
                    $loadFlights, $loadAirports, $relation
                ],
            );
            $tsx->commit();
            // 创建初始化数据文件锁
            file_put_contents(self::getLockPath(), true);
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * User: YunXi
     * Date: 2022-06-17 
     * Time: 09:29 
     * Notes: 获取数据初始化锁路径
     * @return string
     */
    private static function getLockPath() : string
    {
        return base_path() . '/neo4j.lock';
    }

    /**
     * User: YunXi
     * Date: 2022-06-17 
     * Time: 09:22 
     * Notes: 判断是否已完成neo4j的初始化
     * @return boolean
     */
    private static function isInitData() : bool
    {
        return file_exists(self::getLockPath());
    }
}