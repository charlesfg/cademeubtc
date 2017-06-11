<?php

/**
 * Created by PhpStorm.
 * User: charles
 * Date: 10/06/17
 * Time: 12:32
 */
class BTCWalletWatcher
{
    private $addr;


    function __construct($addr) {
        $this->addr = $addr;
    }

    private function get($url){
        echo "-- $url<br> ";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        $content = curl_exec($ch);
        echo "-- $content<br> ";
        curl_close($ch);
        return $content;
    }

    public function satoshi_to_btc($sat){
        return $sat / (float) 100000000;
    }

    public function btc_usd(){
        return $this->btc_ask("https://www.bitstamp.net/api/v2/ticker/btcusd/");
    }

    public function btc_ask($url, $which = "last"){
        $json_ret = $this->get($url);
        $ret = json_decode($json_ret, true);
        return $ret[$which];
    }

    public function btc_brl(){
        return $this->btc_ask("https://api.blinktrade.com/api/v1/BRL/ticker");
    }

    public function btc(){
        return $this->get("https://blockchain.info/q/addressbalance/" . $this->addr);
    }


    public function btc_v2(){
        return $this->btc_ask("https://blockexplorer.com/api/addr/" . $this->addr, "balance");
    }

    public function getWalletBalance(){

        $satoshi_value = $this->btc();
        $btc_value = $this->satoshi_to_btc($satoshi_value);
        $brl_value = $btc_value * $this->btc_brl();
        $usd_value = $btc_value * $this->btc_usd();

        $balance = array($btc_value, $brl_value, $usd_value);

        return $balance;
    }
}