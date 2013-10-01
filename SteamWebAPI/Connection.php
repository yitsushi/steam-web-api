<?php
namespace SteamWebAPI;

class Connection {
  const BASE_URL     = "http://api.steampowered.com";
  const API_VERSION  = "v0001";
  const FORMAT       = "json";
  const TIMEOUT      = 10;

  private static $API_KEY = null;

  public static function setAPIKey($apiKey) {
    self::$API_KEY = $apiKey;
  }

  public static function ISteamUser_ResolveVanityURL($vanityurl) {
    $resolve = self::getJson(
      self::buildUrl('ISteamUser', 'ResolveVanityURL'),
      array(
        "vanityurl" => $vanityurl
      )
    );

    if ($resolve->success != 1) {
      throw new \Exception($resolve->message);
    }

    return $resolve->steamid;
  }

  public static function IPlayerService_GetOwnedGames($steamid) {
    $games = self::getJson(
      self::buildUrl('IPlayerService', 'GetOwnedGames'),
      array(
        'steamid' => $steamid,
        'include_appinfo' => true,
        'include_played_free_games' => true
      )
    );

    return $games;
  }

  private static function buildUrl($class, $action) {
    return implode("/", array(self::BASE_URL, $class, $action, self::API_VERSION)) . "/";
  }

  private static function getJson($url, $parameters = array()) {
    if (!is_array($parameters)) {
      throw new \Exception('Parameters must be an array.');
    }
    if (!array_key_exists("key", $parameters)) {
      $parameters['key'] = self::$API_KEY;
    }
    $parameters['format'] = self::FORMAT;

    $curl = curl_init();
    curl_setopt($curl, \CURLOPT_URL, $url . "?" . http_build_query($parameters));
    curl_setopt($curl, \CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, \CURLOPT_CONNECTTIMEOUT, self::TIMEOUT);
    $data = json_decode(curl_exec($curl));
    curl_close($curl);
    if ($data === null) {
      throw new \Exception('No data.');
    }
    return $data->response;
  }
}