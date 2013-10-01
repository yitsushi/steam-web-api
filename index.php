<?php
require_once('SteamWebAPI/index.php');

$nick = (getenv('NICK') ? getenv('NICK') : 'yitsushi');

\SteamWebAPI\Connection::setAPIKey(getenv('API_KEY'));

try {
  $user = new \SteamWebAPI\User(\SteamWebAPI\User::vanityUrl($nick));
} catch(Exception $e) {
  echo $e->getMessage(), "\n";
  return false;
}

try {
  $gameInfo = $user->getGames();
} catch(Exception $e) {
  echo $e->getMessage(), "\n";
  return false;
}

echo "Game count: ", $gameInfo['game_count'], "\n";
echo "Games: \n";
foreach($gameInfo['games'] as $game) {
  $postfix = "never played";
  if ($game->playtime_forever > 1) {
    if ($game->playtime_forever < 2) {
      $postfix = "{$game->playtime_forever} minute";
    }
    if ($game->playtime_forever < 60) {
      $postfix = "{$game->playtime_forever} minutes";
    }
    if ($game->playtime_forever >= 60) {
      $postfix = floor($game->playtime_forever / 60) . " hour";
      if (floor($game->playtime_forever / 60) > 1) {
        $postfix .= "s";
      }

      $minutes = $game->playtime_forever % 60;
      if ($minutes > 0) {
        $postfix .= " and {$minutes} minute";
        if ($minutes > 1) {
          $postfix .= "s";
        }
      }
    }
  }
  echo " - {$game->name} ({$postfix})\n";
}