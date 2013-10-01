<?php
namespace SteamWebAPI;

class User {
  private $userId = null;
  private $gameInfo = array(
    'game_count' => 0,
    'games' => array()
  );

  public function __construct($userId) {
    if (!preg_match("/^[0-9]+$/", $userId)) {
      throw new \Exception("Invalid Steam ID.");
    }
    $this->userId = $userId;
  }

  public function getGames() {
    if ($this->userId === null) {
      throw new \Exception("The 64 bit Steam ID is not defined.");
    }

    $games = Connection::IPlayerService_GetOwnedGames($this->userId);
    $this->gameInfo['game_count'] = $games->game_count;
    $this->gameInfo['games']      = array_map(
      array($this, "prepareGameInfo"),
      $games->games
    );

    return $this->gameInfo;
  }

  private function prepareGameInfo($game) {
    if (property_exists($game, "has_community_visible_stats") && $game->has_community_visible_stats === true) {
      $game->communityLink = "http://steamcommunity.com/profiles/"
                             . $this->userId
                             . "/stats/"
                             . $game->appid;
    }
    $game->logoUrl = "http://media.steampowered.com/steamcommunity/public/images/apps/"
                     . $game->appid
                     . "/"
                     . $game->img_logo_url
                     . ".jpg";
    $game->iconUrl = "http://media.steampowered.com/steamcommunity/public/images/apps/"
                     . $game->appid
                     . "/"
                     . $game->img_icon_url
                     . ".jpg";
    return $game;
  }

  /* Static Methods */
  public static function vanityUrl($nick) {
    return Connection::ISteamUser_ResolveVanityURL($nick);
  }
}
