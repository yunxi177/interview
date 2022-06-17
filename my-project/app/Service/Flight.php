<?php

namespace App\Service;

class Flight 
{
    /**
     * User: YunXi
     * Date: 2022-06-16 
     * Time: 16:10 
     * Notes: 查询从form到to的航班最短路径
     *
     * @param string $from
     * @param string $to
     * @return array
     */
    public function search(string $from, string $to) : array {
        $client = Neo4j::getClient();
        //执行查询语句
        $results = $client->run(
            "MATCH (f:airports {iata: '{$from}'} ),(t:airports {iata: '${to}'}),p = shortestPath((f)-[:`航班`*]->(t)) RETURN p"
        );
        $serialize = $results->jsonSerialize();
        $result = $serialize['result'] ?? [];
        $path = [];
        // 遍历节点
        foreach($result as $nodes) {
            // 获取关系
            $relations = $nodes["p"]["relationships"];
            // 遍历关系
            foreach ($relations as $relation) {
                // 不存在属性跳过
                if (!isset($relation['properties']) || empty($relation['properties'])) {
                    continue;
                }
                // 获取关系的属性
                $properties = $relation['properties'];
                // 获取航班信息
                $path[] = [
                    'id' => $properties['id'] ?? '',
                    'form' => $properties['form'] ?? '',
                    'to' => $properties['to'] ?? '',
                ];
    
            }
        }

        return $path;
    }
}