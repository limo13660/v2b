<?php

namespace App\Http\Controllers\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\NodeResource;
use App\Models\User;
use App\Services\ServerService;
use App\Services\UserService;
use Illuminate\Http\Request;

/**kxboard主题 */
use App\Protocols\General;

class ServerController extends Controller
{

    // public function fetch(Request $request)
    // {
    //     $user = User::find($request->user['id']);
    //     $servers = [];
    //     $userService = new UserService();
    //     if ($userService->isAvailable($user)) {
    //         $servers = ServerService::getAvailableServers($user);
    //     }
    //     $eTag = sha1(json_encode(array_column($servers, 'cache_key')));
    //     if (strpos($request->header('If-None-Match'), $eTag) !== false ) {
    //         return response(null,304);
    //     }
    //     $data = NodeResource::collection($servers);
    //     return response([
    //         'data' => $data
    //     ])->header('ETag', "\"{$eTag}\"");
    // }

    public function fetch(Request $request)
    {
        $user = User::find($request->user['id']);
        $servers = [];
        $userService = new UserService();

        if ($userService->isAvailable($user)) {
            $servers = ServerService::getAvailableServers($user);
        }

        /**kxboard主题 每个节点生成配置 */
        if($request->input('type')=='config'){
            // $servers = ServerService::getAvailableServers($user);
            $servers = json_decode(json_encode($servers), true);
            $servers = array_map(function ($server) use($user) {
                $class = new General($user, $server);
                $server['config'] = $class->handle();;
                return $server;
            }, $servers);
            $servers = NodeResource::collection($servers);
            return response(['data' => $servers]);
        }


        $eTag = sha1(json_encode(array_column($servers, 'cache_key')));
        if (strpos($request->header('If-None-Match'), $eTag) !== false ) {
            return response(null,304);
        }
        $servers = NodeResource::collection($servers);
        return response([
            'data' => $servers,
        ])->header('ETag', "\"{$eTag}\"");
    }

    /**kxboard主题 增加flag图标字段
     *
        ALTER TABLE `v2_server_vmess` ADD COLUMN `flag` VARCHAR(255) DEFAULT '' NULL AFTER `id`;
        ALTER TABLE `v2_server_vless` ADD COLUMN `flag` VARCHAR(255) DEFAULT '' NULL AFTER `id`;
        ALTER TABLE `v2_server_trojan` ADD COLUMN `flag` VARCHAR(255) DEFAULT '' NULL AFTER `id`;
        ALTER TABLE `v2_server_shadowsocks` ADD COLUMN `flag` VARCHAR(255) DEFAULT '' NULL AFTER `id`;
        ALTER TABLE `v2_server_hysteria` ADD COLUMN `flag` VARCHAR(255) DEFAULT '' NULL AFTER `id`;
    */
    /**kxboard主题 web的节点列表 */
    public function config(Request $request)
    {
        $user = User::find($request->user['id']);

        $type = $request->input('type');
        $id = $request->input('id');
        $server = User::find($request->input('id'));

        $class = new General($user, $server);
        return response(['data' => $class->handle()]);
    }


}
