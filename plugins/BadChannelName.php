<?php
/**
 * Created by PhpStorm.
 * User: Jake
 * Date: 09/08/2016
 * Time: 03:50
 */

namespace Plugin;


use App\Plugin;
use TeamSpeak3\TeamSpeak3;
use TeamSpeak3\Ts3Exception;

class BadChannelName extends Plugin implements PluginContract
{

    public function isTriggered()
    {

        foreach($this->CONFIG['blacklist'] as $regex) {
            if(!$this->info['channel_name']->contains($regex, true)) {
                continue;
            }

            $this->handle();
            return;
        }
    }

    private function handle()
    {
        $channel = $this->teamSpeak3Bot->node->channelGetById(@$this->info['ctid'] ? $this->info['ctid'] : $this->info['cid']);
        $violator = $this->teamSpeak3Bot->node->clientGetById($this->info['invokerid'] ? $this->info['invokerid'] : $this->info['clid']);

        try {
            $channel->delete(true);
            $violator->kick(TeamSpeak3::KICK_SERVER, "Channel Name Violation");
        } catch(Ts3Exception $e) {
            echo $e->getMessage();
        }
    }
}
